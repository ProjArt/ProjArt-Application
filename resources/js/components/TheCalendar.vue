<script setup>
import { ref, computed, toRaw, watch } from "vue"
import useFetch from '../composables/useFetch';
import { API } from '../stores/api';

// Constants
// ====================================== 

const TODAY = new Date()
const DAY_LABELS = ['LU', 'MA', 'ME', 'JE', 'VE', 'SA', 'DI'];
const DAY_LABELS_ORDERS = ['DI', 'MA', 'ME', 'JE', 'VE', 'SA', 'LU'];
const DATE_OPTION = { year: 'numeric', month: 'long' }
const AVAILABLE_LAYOUT = { MONTH: 0, WEEK: 1, DAY: 3 };

// Ref
// ====================================== 

const allCalendars = ref({});
const calendarsNames = ref([]);
const currentLayout = ref(AVAILABLE_LAYOUT.MONTH);
const currentCalendarId = ref(1);
const displayedDate = ref(new Date());
const newEventPopupRef = ref([]);
const showNewEventPopupRef = ref('');
const showCurrentEventsPopupRef = ref('');
const newEventForm = ref({});
const formUpdate = ref({});
const isSubmitted = ref(false);
const selectedDate = ref('')
const start = ref('...')
const end = ref('...')
const indexUnderEdition = ref('')
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
    set(events) {
        newEventPopupRef.value = events;
    }
});

const showCurrentEventsPopup = computed({
    get() { return showCurrentEventsPopupRef.value; },
    set(events) { showCurrentEventsPopupRef.value = events; }
});

// Helpers
// ====================================== 

function toSwissDay(day) {
    const index = DAY_LABELS_ORDERS[day]
    return DAY_LABELS.indexOf(index, 0)
}

function formatDayObject(ref) {
    return {
        local: ref.toLocaleDateString(),
        class: setDayCssClass(ref),
        dayOfMonthNumber: ref.getDate(),
        dayOfWeekNumber: toSwissDay(ref.getDay()),
        events: []
    }
}

function setEvents() {
    const currentCalendar = allCalendars.value[currentCalendarId.value]
    currentCalendar.events.forEach(event => {
        const newEvent = toRaw(event)
        const date = new Date(event.start)
        const local = date.toLocaleDateString()
        newEvent['local'] = local
        dates.value?.[local]?.events.push(newEvent)
    })
}

function getAllDaysInMonth(year, month) {
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

function getAllDaysInWeek(choosenDate) {
    const monday = new Date(choosenDate.getFullYear(), choosenDate.getMonth(), choosenDate.getDate() - choosenDate.getDay() + 1)
    const dates = {}
    for (let i = 0; i <= 6; i++) {
        const date = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate() + i)
        const key = date.toLocaleDateString()
        dates[key] = formatDayObject(date);
    }
    return dates
}

function toChDate(date) {
    date = date.split(' ')[0]
    date = date.split('-')
    date = `${date[2]}/${date[1]}/${date[0]}`
    return date
}

function prepareFormBeforeSending(rawForm) {
    //  expected output: 2022-05-27 12:06:28
    const date = rawForm.start_date
    const dateParts = date.split('/')
    const newDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`
    const start = `${newDate} ${rawForm.start}:00`
    const end = `${newDate} ${rawForm.end}:00`
    rawForm.start = start
    rawForm.end = end
    delete rawForm.end_date
    delete rawForm.start_date
    return rawForm
}

function sortEvents(date) {
    dates.value[date].events.sort((a, b) => {
        const dateA = new Date(a['start'])
        const dateB = new Date(b['start'])
        return dateA - dateB
    })
}

function hideNewEventForm() {
    showNewEventPopup.value = '';
}

function hideEditEventsForm() {
    showCurrentEventsPopup.value = '';
}

// CRUD operations on API
// ====================================== 

async function storeEvent(form) {
    const date = form.start_date
    form = prepareFormBeforeSending(form)
    const response = await useFetch({
        url: API.storeEvent.path(),
        method: API.storeEvent.method,
        data: form
    });
    if (response.success === true) {
        try {
            form['id'] = response.data.id
            dates.value[date].events.push(form)
            sortEvents(date)
        } catch (error) {
            console.log(error)
        }
    } else {
        console.log('error')
    }
}

async function deleteEvent(dayId, eventId) {
    const date = toChDate(dayId)
    const events = toRaw(dates.value[date].events)
    const newEvents = events.filter((event) => event.id !== eventId)
    const response = await useFetch({
        url: API.deleteEvent.path(eventId),
        method: API.deleteEvent.method,
    });
    if (response.success === true) {
        try {
            dates.value[date].events = newEvents
            sortEvents(date)
            newEventPopup.value = newEventPopup.value.filter((event) => event.id !== eventId)
        } catch (error) {
            console.log(error)
        }
    } else {
        console.log('error')
    }
}

async function updateEvent(form) {
    const date = form.start_date
    form = prepareFormBeforeSending(form)
    const events = toRaw(dates.value[date].events)
    let newEvents = events.filter((event) => event.id !== form.id)
    newEvents.push(form)
    const response = await useFetch({
        url: API.updateEvent.path(form.id),
        method: API.updateEvent.method,
        data: form
    });
    if (response.success === true) {
        try {
            dates.value[date].events = newEvents
            sortEvents(date)
            newEventPopup.value = newEvents
            console.log({ newEvents })
        } catch (error) {
            console.log(error)
        }
    } else {
        console.log('error')
    }
}

async function setCalendars() {
    const response = await useFetch({
        url: API.getEvents.path(),
        method: API.getEvents.method,
    });
    if (response.success === true) {
        const calendars = []
        response.data.forEach((calendar) => {
            calendars.push({ label: calendar.name, value: calendar.key })
        })
        calendarsNames.value = calendars;
        console.log(toRaw(calendarsNames))
        allCalendars.value = response.data
        setEvents()
    } else {
        console.log(response, 'error')
    }
}

// Features
// ====================================== 

function getMonthCalendarFormat(year, month) {
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

function setDisplayedDates(timeInterval) {
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

function setDayCssClass(date) {
    const today = new Date(TODAY.getFullYear(), TODAY.getMonth(), TODAY.getDate());
    if (typeof date === 'undefined') return ''
    else if (today.toLocaleDateString() === date.toLocaleDateString()) return 'is-current-day'
    else return 'is-modifiable-day'
}

function changeLayout(next) {
    if (next && currentLayout.value === AVAILABLE_LAYOUT.MONTH) setDisplayedDates(1)
    else if (!next && currentLayout.value === AVAILABLE_LAYOUT.MONTH) setDisplayedDates(-1)
    else if (next && currentLayout.value === AVAILABLE_LAYOUT.WEEK) setDisplayedDates(7)
    else if (!next && currentLayout.value === AVAILABLE_LAYOUT.WEEK) setDisplayedDates(-7)
}

function showNewEventForm(event) {
    hideEditEventsForm()
    event.stopPropagation()
    const index = event.target.value
    selectedDate.value = index
    start.value = index
    end.value = index
    showNewEventPopup.value = index;
    newEventForm.value = {
        title: 'title ' + index,
        location: 'location' + index,
        description: 'description' + index,
        start: '08:00',
        end: '09:00',
        start_date: index,
        end_date: index,
        calendar_id: currentCalendarId.value
    }
}

function showCurrentEvent(index) {
    hideNewEventForm()
    selectedDate.value = index
    showCurrentEventsPopup.value = index;
}

function showEventEditForm(index, id) {
    indexUnderEdition.value = id !== indexUnderEdition.value ? id : undefined
    let newEvent = toRaw(dates.value[index].events)
    newEvent = newEvent.filter((event, key) => {
        if (event.id === id) return event
    })
    let start;
    let end;
    try {
        start = newEvent[0].start.split(' ')[1].split(':')
        start = `${start[0]}:${start[1]}`
        end = newEvent[0].end.split(' ')[1].split(':')
        end = `${end[0]}:${end[1]}`
    } catch (error) {
        start = ''
        end = ''
    }
    formUpdate.value = {
        title: newEvent[0].title,
        location: newEvent[0].location,
        description: newEvent[0].description,
        start: start,
        end: end,
        start_date: index,
        end_date: index,
        calendar_id: currentCalendarId.value
    }
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
        <!--====  Calendar Header  ====-->
        <header class="calendar__header">
            <h3>{{ displayedDateManager }}</h3>
            <button @click="changeLayout(false)">&lt;&lt;</button>
            <button @click="changeLayout(true)">&gt;&gt;</button>
            <button @click="currentLayout = AVAILABLE_LAYOUT.MONTH">Mois</button>
            <button @click="currentLayout = AVAILABLE_LAYOUT.WEEK">Semaines</button>
            <button @click="setDisplayedDates(0)">Aujaurd'hui</button>
            <FormKit type="select" name="calendar" :options="calendarsNames" v-model="currentCalendarId" />
        </header>
        <!--====  Calendar days names  ====-->
        <div class="calendar__days-names">
            <div v-for="dayLabel in dayLabels">
                {{ dayLabel }}
            </div>
        </div>
        <!--====  Calendar days  ====-->
        <div class="calendar__days">
            <div v-for="(day, index) in dates" class="calendar__day" @click="showCurrentEvent(index)"
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
    <!--====  Popup new event  ====-->
    <div class="popup popup--new-event" v-show="showNewEventPopup">
        <FormKit type="form" v-model="newEventForm" :form-class="isSubmitted ? 'hide' : 'show'"
            submit-label="Enregistrer" @submit="storeEvent">
            <h2>Add Event</h2>
            <p>{{ selectedDate }}</p>
            <FormKit type="text" name="title" validation="required" label="Titre" />
            <FormKit type="text" name="location" validation="required" label="Lieu" />
            <FormKit type="textarea" name="description" validation="required" label="Description" />
            <FormKit type="time" name="start" label="Début" value="08:00" />
            <FormKit type="time" name="end" label="Fin" value="08:00" />
            <FormKit name="calendar_id" type="hidden" />
            <FormKit name="start_date" type="hidden" />
            <FormKit name="end_date" type="hidden" />
        </FormKit>
    </div>
    <!--====  Popup edit event  ====-->
    <div class="popup popup--edit-events" v-show="showCurrentEventsPopup">
        <h2>Current Events</h2>
        <article class="popup__event" v-for="(event, index) in newEventPopup">
            <div class="event">
                <div class="event__infos">
                    <p>id: {{ event.id }}</p>
                    <p>titre: {{ event.title }}</p>
                    <p>lieu: {{ event.location }}</p>
                    <p>description: {{ event.description }}</p>
                    <p>début: {{ event.start }}</p>
                    <p>fin: {{ event.end }}</p>
                </div>
                <button @click="deleteEvent(event.start, event.id)">supprimer</button>
                <button @click="showEventEditForm(event.local, event.id)">editer</button>
                <FormKit type="form" v-model="formUpdate" submit-label="Enregistrer" @submit="updateEvent"
                    v-if="indexUnderEdition === event.id" :key="event.id">
                    <FormKit type="text" name="title" validation="required" :label="'Titre ' + event.id" />
                    <FormKit type="text" name="location" validation="required" label="Lieu" />
                    <FormKit type="textarea" name="description" validation="required" label="Description" />
                    <FormKit type="time" name="start" label="Début" />
                    <FormKit type="time" name="end" label="Fin" />
                    <FormKit name="calendar_id" type="hidden" :value="currentCalendarId" />
                    <FormKit name="start_date" type="hidden" :value="event.local" />
                    <FormKit name="end_date" type="hidden" :value="event.local" />
                    <FormKit name="id" type="hidden" :value="event.id" />
                </FormKit>
            </div>
        </article>
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

.popup {
    width: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.popup--new-event {}

.event {
    display: flex;
    background-color: lightgray;
    width: 100%;
    margin: 2px 0;
    box-sizing: content-box;
}

.event__infos {
    display: flex;
    flex-direction: column;
    padding: 0.5rem;
}

.popup--edit-events,
:deep(.formkit-form) {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.popup__event {
    width: 100%;
    margin: 0 1rem;
}
</style>
