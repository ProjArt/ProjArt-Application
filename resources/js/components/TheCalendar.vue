<script setup>
import { ref, computed, reactive, toRaw, watch } from "vue"
import useFetch from '../composables/useFetch';
import { API } from '../stores/api';
// TODO order events by date

// local variables
// ====================================== 

const DAY_LABELS = ['LU', 'MA', 'ME', 'JE', 'VE', 'SA', 'DI'];
const DAY_LABELS_ORDERS = ['DI', 'MA', 'ME', 'JE', 'VE', 'SA', 'LU'];
const DATE_OPTION = { year: 'numeric', month: 'long' }
const AVAILABLE_LAYOUT = { MONTH: 0, WEEK: 1, DAY: 3 };

// Ref
// ====================================== 

const TODAY = new Date()
const allCalendars = ref({});
const calendarsNames = ref([]);
const currentLayout = ref(AVAILABLE_LAYOUT.MONTH);
const currentCalendarId = ref(1);
const displayedDate = ref(new Date());
const newEventPopupRef = ref([]);
const showNewEventPopupRef = ref('');
const showEditEventsPopupRef = ref('');
const newEventForm = ref({});
const formUpdate = ref({});
const isSubmitted = ref(false);
const selectedDate = ref('')
const start = ref('...')
const end = ref('...')
const currDateCursor = ref(TODAY)
const dayLabels = ref(DAY_LABELS.slice())

// Computed 
// ====================================== 

const showNewEventPopup = computed({
    get: () => showNewEventPopupRef.value,
    set: (date) => { showNewEventPopup.value !== date ? showNewEventPopupRef.value = date : showNewEventPopupRef.value = ''; }
});

const displayedDateManager = computed({
    get() { return displayedDate.value.toLocaleDateString('fr-ch', DATE_OPTION); },
    set({ year, month }) { displayedDate.value = new Date(year, month); }
});

const newEventPopup = computed({
    get() { return newEventPopupRef.value; },
    set(events) { newEventPopupRef.value = events; }
});

const showEditEventsPopup = computed({
    get() { return showEditEventsPopupRef.value; },
    set(events) { showEditEventsPopupRef.value = events; }
});

// Helpers
// ====================================== 

const toSwissDay = (day) => {
    const index = DAY_LABELS_ORDERS[day]
    return DAY_LABELS.indexOf(index, 0)
}

const formatDayObject = (ref) => {
    return {
        local: ref.toLocaleDateString(),
        class: setDayCssClass(ref),
        dayOfMonthNumber: ref.getDate(),
        dayOfWeekNumber: toSwissDay(ref.getDay()),
        events: []
    }
}

const setEvents = () => {
    const currentCalendar = allCalendars.value[currentCalendarId.value]
    currentCalendar.events.forEach(event => {
        const date = new Date(event.start)
        const local = date.toLocaleDateString()
        dates.value?.[local]?.events.push(event)
    })
}

const getAllDaysInMonth = (year, month) => {
    const date = new Date(year, month, 1)
    const dates = {};
    while (date.getMonth() === month) {
        const day = new Date(date)
        const key = day.toLocaleDateString()
        dates[key] = formatDayObject(day);
        date.setDate(date.getDate() + 1)
    }
    return dates;
}

const getAllDaysInWeek = (choosenDate) => {
    const monday = new Date(choosenDate.getFullYear(), choosenDate.getMonth(), choosenDate.getDate() - choosenDate.getDay() + 1)
    const dates = {}
    for (let i = 0; i <= 6; i++) {
        const date = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate() + i)
        const key = date.toLocaleDateString()
        dates[key] = formatDayObject(date);
    }
    return dates
}

const toChDate = (date) => {
    date = date.split(' ')[0]
    date = date.split('-')
    date = `${date[2]}/${date[1]}/${date[0]}`
    return date
}

// Features
// ====================================== 

async function setCalendars() {
    const response = await useFetch({
        url: API.events.path(),
        method: API.events.method,
    });
    const calendars = []
    response.data.forEach((calendar) => {
        calendars.push({ label: calendar.name, value: calendar.key })
    })
    calendarsNames.value = calendars;
    allCalendars.value = response.data
    setEvents()
}

const getMonthCalendarFormat = (year, month) => {
    const days = {}
    const daysInMonth = year && month
        ? getAllDaysInMonth(year, month)
        : getAllDaysInMonth(TODAY.getFullYear(), TODAY.getMonth())
    for (const [index, date] of Object.entries(daysInMonth)) {
        let i = 0
        if (index === Object.keys(daysInMonth)[0]) {
            while (date.dayOfWeekNumber !== i) {
                days[i] = {}
                i++
            }
        }
        days[date.local] = date
    }
    return days
}

const setDisplayedDates = (timeInterval) => {
    const nextDate = new Date(currDateCursor.value);
    if (timeInterval === 0) {
        if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
            dates.value = getMonthCalendarFormat(TODAY.getFullYear(), TODAY.getMonth());
        } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
            dates.value = getAllDaysInWeek(TODAY);
        }
        displayedDateManager.value = { year: TODAY.getFullYear(), month: TODAY.getMonth() };
    } else if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
        nextDate.setMonth(nextDate.getMonth() + parseInt(timeInterval));
        currDateCursor.value = nextDate;
        dates.value = getMonthCalendarFormat(currDateCursor.value.getFullYear(), currDateCursor.value.getMonth());
        displayedDateManager.value = { year: nextDate.getFullYear(), month: nextDate.getMonth() };
    } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
        nextDate.setDate(nextDate.getDate() + parseInt(timeInterval));
        currDateCursor.value = nextDate;
        dates.value = getAllDaysInWeek(nextDate);
        displayedDateManager.value = { year: nextDate.getFullYear(), month: nextDate.getMonth() };
    }
    setEvents()
    showNewEventPopupRef.value = ''
}


const setDayCssClass = (date) => {
    const today = new Date(TODAY.getFullYear(), TODAY.getMonth(), TODAY.getDate());
    if (typeof date === 'undefined') return ''
    else if (today.toLocaleDateString() === date.toLocaleDateString()) return 'is-current-day'
    else return 'is-modifiable-day'
}

const changeLayout = (next) => {
    if (next && currentLayout.value === AVAILABLE_LAYOUT.MONTH) setDisplayedDates(1)
    else if (!next && currentLayout.value === AVAILABLE_LAYOUT.MONTH) setDisplayedDates(-1)
    else if (next && currentLayout.value === AVAILABLE_LAYOUT.WEEK) setDisplayedDates(7)
    else if (!next && currentLayout.value === AVAILABLE_LAYOUT.WEEK) setDisplayedDates(-7)
}

async function newEvent() {
    //  expected output: 2022-05-27 12:06:28
    const form = toRaw(newEventForm.value)
    let start = `${form.start_date} ${form.start}:00`
    let end = `${form.end_date} ${form.end}:00`
    start = start.replace(/\//g, '-')
    end = end.replace(/\//g, '-')
    form.start = start
    form.end = end
    delete form.end_date
    delete form.start_date
    const body = { calendar_id, start, end, location, title, description }
    const response = await useFetch({
        url: API.newEvents.path(),
        method: API.newEvents.method,
        data: body
    });
    if (response.success === true) {
        try {
            dates.value[start_date].events.push(body)
        } catch (error) {
            console.log(error)
        }
    } else {
        console.log('error')
    }
}

const removeEvent = (dayId, eventId) => {
    const date = toChDate(dayId)
    try {
        const events = toRaw(dates.value[date].events)
        const newEvents = events.filter((event, key) => {
            if (key !== eventId) {
                return event
            }
        })
        dates.value[date].events = newEvents
        // TODO call to api to remove event
    } catch (error) {
        console.log(error)
    }
}

const hideNewEventForm = () => {
    showNewEventPopup.value = '';
}

const hideEditEventsForm = () => {
    showEditEventsPopup.value = '';
}

const showNewEventForm = (event) => {
    hideEditEventsForm()
    event.stopPropagation()
    const index = event.target.value
    selectedDate.value = index
    start.value = index
    end.value = index
    showNewEventPopup.value = index;
    newEventForm.value = {
        title: 'test title',
        location: 'test location',
        description: 'test description',
        start_date: index,
        end_date: index,
        calendar_id: currentCalendarId.value
    }
}

const showEditEventsForm = (index) => {
    hideNewEventForm()
    selectedDate.value = index
    showEditEventsPopup.value = index;
}

// Watcher(s)
// ====================================== 

watch(currentLayout, () => {
    if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
        dates.value = getMonthCalendarFormat(currDateCursor.value.getFullYear(), currDateCursor.value.getMonth());
        setEvents()
    } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
        dates.value = getAllDaysInWeek(currDateCursor.value);
        setEvents()
    }
    showNewEventPopupRef.value = ''
})

watch(selectedDate, (index) => {
    newEventPopup.value = toRaw(dates.value[index].events)
})

// At startup
// ====================================== 

const dates = ref(getMonthCalendarFormat(TODAY.getFullYear(), TODAY.getMonth()));
setCalendars()

</script>
<template>

    <div class="calendar">
        <header class="calendar__header">
            <h3>{{ displayedDateManager }}</h3>
            <button @click="changeLayout(false)">&lt;&lt;</button>
            <button @click="changeLayout(true)">&gt;&gt;</button>
            <button @click="currentLayout = AVAILABLE_LAYOUT.MONTH">Mois</button>
            <button @click="currentLayout = AVAILABLE_LAYOUT.WEEK">Semaines</button>
            <button @click="setDisplayedDates(0)">Aujaurd'hui</button>
            <FormKit type="select" name="calendar" :options="calendarsNames" v-model="currentCalendarId" />
        </header>
        <div class="calendar__days-names">
            <div v-for="dayLabel in dayLabels">
                {{ dayLabel }}
            </div>
        </div>
        <div class="calendar__days">
            <div v-for="(day, index) in dates" class="calendar__day" @click="showEditEventsForm(index)"
                :class="day.class, selectedDate === index ? 'is-selected-day' : ''" :key="index" :date-id="day.local">
                <p class="calendar__day-number">{{ day.dayOfMonthNumber }}</p>
                <div v-for="event in day.events">
                    <p class="calendar__event">{{ event.title }}</p>
                </div>
                <button :value="index" @click="showNewEventForm" class="button--add-event"
                    v-show="index.includes('/')">+</button>
            </div>
        </div>
    </div>
    <div class="popup--new-event" v-show="showNewEventPopup">

        <FormKit type="form" v-model="newEventForm" :form-class="isSubmitted ? 'hide' : 'show'"
            submit-label="Enregistrer" @submit="newEvent" :key="selectedDate">
            <h2>Add Event</h2>
            <p>{{ selectedDate }}</p>
            <FormKit type="text" name="title" validation="required" label="Titre" />
            <FormKit type="text" name="location" validation="required" label="Lieu" />
            <FormKit type="textarea" name="description" validation="required" label="Description" />
            <FormKit type="time" name="start" label="Début" value="00:00" />
            <FormKit type="time" name="end" label="Fin" value="00:00" />
            <FormKit name="calendar_id" type="hidden" />
            <FormKit name="start_date" type="hidden" />
            <FormKit name="end_date" type="hidden" />
        </FormKit>

    </div>

    <div class="popup--edit-events" v-show="showEditEventsPopup">
        <h2>Current Events</h2>
        <!-- 
        <article class="popup__event" v-for="(event, index) in newEventPopup">
            <p>{{ event.title }}</p>
            <p>{{ event.start }}</p>
            <p>{{ event.end }}</p>
            <p>{{ event.location }}</p>
            <button @click="removeEvent(event.start, index)">supprimer</button>
            <FormKit type="form" v-model="formUpdate" :key="event.id" :form-class="isSubmitted ? 'hide' : 'show'"
                submit-label="Enregistrer" @submit="newEvent">
                <FormKit type="text" :value="event.title" name="title" validation="required" label="Titre" />
                <FormKit type="text" :value="event.location" name="location" validation="required" label="Lieu" />
                <FormKit type="textarea" :value="event.description" name="description" validation="required"
                    label="Description" />
                <FormKit type="time" name="start" label="Début" :value="newEventForm.start" />
                <FormKit type="time" name="end" label="Fin" :value="newEventForm.end" />
                <FormKit name="calendar_id" type="hidden" :value="currentCalendarId" />
                <FormKit name="start_date" type="hidden" v-model="start" />
                <FormKit name="end_date" type="hidden" v-model="end" />
            </FormKit>
        </article>
    -->
    </div>
</template>
<style lang="scss" scoped>
.calendar {
    margin: 2rem
}

.calendar__header {
    display: flex;
    justify-content: center;
    align-items: center;
}

.calendar__days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-gap: 1rem;
}

.calendar__day {
    min-height: 100px;
    max-width: 100%;
    position: relative;
    border: 1px solid #ccc;
    display: flex;
    flex-direction: column;
    font-size: 1.5rem;
    background-color: rgba(0, 0, 0, 0.1);
    pointer-events: none;
    cursor: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis
}

.calendar__days-names {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-gap: 1rem;
}

.calendar__day-number {
    font-size: 1.5rem;
    text-align: left;
    padding: 0.5rem;
}

.calendar__event {
    font-size: 0.7rem;
    text-align: left;
}

.button--add-event {
    position: absolute;
    bottom: 0;
    right: 0;
    background-color: #ccc;
    border: none;
    padding: 0.5rem;
    font-size: 1rem;
    cursor: pointer;
    z-index: 10;
}

.is-modifiable-day {
    cursor: pointer;
    pointer-events: all;
    background-color: white;
}

.is-current-day {
    cursor: pointer;
    pointer-events: all;
    background-color: lightblue;
}

.is-selected-day {
    border: 1px solid lightcoral;
}

.popup--new-event {
    width: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.popup--edit-events,
:deep(.formkit-form) {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.popup__event {
    list-style: none;
}
</style>
