<script setup>
import { ref, computed, reactive, toRaw, watch } from "vue"
import useFetch from '../composables/useFetch';
import { API } from '../stores/api';

// local variables
// ====================================== 

const DAY_LABELS = ['LU', 'MA', 'ME', 'JE', 'VE', 'SA', 'DI'];
const DAY_LABELS_ORDERS = ['DI', 'MA', 'ME', 'JE', 'VE', 'SA', 'LU'];
const DATE_OPTION = { year: 'numeric', month: 'long' }
const AVAILABLE_LAYOUT = { MONTH: 0, WEEK: 1, DAY: 3 };
const formData = ref({});
const isSubmitted = ref(false);

// Ref & Computed 
// ====================================== 

const calendar = ref({});
const calendarNames = ref([]);
const TODAY = ref(new Date())
const currentLayout = ref(AVAILABLE_LAYOUT.MONTH);
const currentCalendarId = ref(0);
const displayedDate = ref(new Date());
const showPopupRef = ref('');
const showPopup = computed({
    get: () => showPopupRef.value,
    set: (date) => {
        showPopup.value !== date ? showPopupRef.value = date : showPopupRef.value = '';
    }
});

const displayedDateManager = computed({
    get() {
        return displayedDate.value.toLocaleDateString('fr-ch', { year: 'numeric', month: 'long' });
    },
    set({ year, month }) {
        displayedDate.value = new Date(year, month);
    }
});

// Helpers
// ====================================== 

const toSwissDay = (day) => {
    const index = DAY_LABELS_ORDERS[day]
    return DAY_LABELS.indexOf(index, 0)
}

const formatDayObject = (ref) => {
    return {
        date: ref,
        local: ref.toLocaleDateString(),
        class: setDayCssClass(ref),
        dayOfMonthNumber: ref.getDate(),
        dayOfWeekNumber: toSwissDay(ref.getDay()),
        dayOfWeekName: ref.getDay(),
        events: []
    }
}

const getEvents = () => {
    const currentCalendar = calendar.value[currentCalendarId.value]
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

// Features
// ====================================== 

async function getCalendar() {
    const response = await useFetch({
        url: API.events.path(),
        method: API.events.method,
    });
    const names = []
    response.data.forEach((event, key) => {
        names.push({ label: event.name, value: event.key })
    })
    calendarNames.value = names;
    calendar.value = response.data
    getEvents()
}

const getMonthFormat = (year, month) => {
    const days = {}
    const daysInMonth = year && month
        ? getAllDaysInMonth(year, month)
        : getAllDaysInMonth(TODAY.value.getFullYear(), TODAY.value.getMonth())
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
            dates.value = getMonthFormat(TODAY.value.getFullYear(), TODAY.value.getMonth());
        } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
            dates.value = getAllDaysInWeek(TODAY.value);
        }
        displayedDateManager.value = { year: TODAY.value.getFullYear(), month: TODAY.value.getMonth() };
    } else if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
        nextDate.setMonth(nextDate.getMonth() + parseInt(timeInterval));
        currDateCursor.value = nextDate;
        dates.value = getMonthFormat(currDateCursor.value.getFullYear(), currDateCursor.value.getMonth());
        displayedDateManager.value = { year: nextDate.getFullYear(), month: nextDate.getMonth() };
    } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
        nextDate.setDate(nextDate.getDate() + parseInt(timeInterval));
        currDateCursor.value = nextDate;
        dates.value = getAllDaysInWeek(nextDate);
        displayedDateManager.value = { year: nextDate.getFullYear(), month: nextDate.getMonth() };
    }
    getEvents()
}

const setDayCssClass = (date) => {
    const today = new Date(TODAY.value.getFullYear(), TODAY.value.getMonth(), TODAY.value.getDate());
    if (typeof date === 'undefined') {
        return ''
    } else if (today.toLocaleDateString() === date.toLocaleDateString()) {
        return 'current-day'
    } else {
        return 'modifiable-day'
    }
}

const changeLayout = (next) => {
    if (next && currentLayout.value === AVAILABLE_LAYOUT.MONTH) setDisplayedDates(1)
    else if (!next && currentLayout.value === AVAILABLE_LAYOUT.MONTH) setDisplayedDates(-1)
    else if (next && currentLayout.value === AVAILABLE_LAYOUT.WEEK) setDisplayedDates(7)
    else if (!next && currentLayout.value === AVAILABLE_LAYOUT.WEEK) setDisplayedDates(-7)
}

async function newEvent() {
    const [calendar_id, end, end_date, location, start, start_date, title] = [
        formData.value.calendar_id,
        formData.value.end,
        formData.value.end_date,
        formData.value.location,
        formData.value.start,
        formData.value.start_date,
        formData.value.title
    ]
    const startDate = `${start_date}:${start}`
    const endDate = `${end_date}:${end}`
    const body = {
        calendar_id,
        startDate,
        endDate,
        location,
        title
    }
    const response = await useFetch({
        url: API.newEvents.path(),
        method: API.newEvents.method,
        data: body
    });
    console.log(response)
}

// Watcher(s)
// ====================================== 

watch(currentLayout, () => {
    if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
        dates.value = getMonthFormat(currDateCursor.value.getFullYear(), currDateCursor.value.getMonth());
        getEvents()
    } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
        dates.value = getAllDaysInWeek(currDateCursor.value);
        getEvents()
    }
})

const showForm = (index) => {
    console.log(index)
    selectedDate.value = index
    showPopup.value = index;
    start.value = index
    end.value = index
}


const dates = ref(getMonthFormat(TODAY.value.getFullYear(), TODAY.value.getMonth()));
const selectedDate = ref('')
const start = ref('...')
const end = ref('...')
const currDateCursor = ref(TODAY.value)
const dayLabels = ref(DAY_LABELS.slice())
getCalendar()

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
            <FormKit type="select" name="calendar" :options="calendarNames" v-model="currentCalendarId" />
        </header>
        <div class="calendar__days-names">
            <div v-for="dayLabel in dayLabels">
                {{ dayLabel }}
            </div>
        </div>
        <div class="calendar__days">
            <div v-for="(day, index) in dates" class="calendar__day"
                :class="day.class, selectedDate === index ? 'is-selected-day' : ''" :key="index" :date-id="day.local"
                @click="showForm(index)">
                <p class="calendar__day-number">{{ day.dayOfMonthNumber }}</p>
                <div v-for="event in day.events">
                    <p class="calendar__event">{{ event.title }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="popup" v-show="showPopup">
        <FormKit type="form" v-model="formData" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Enregistrer"
            @submit="newEvent">
            <FormKit type="text" value="1" name="title" validation="required" label="Titre" />
            <FormKit type="text" value="2" name="location" validation="required" label="Lieu" />
            <FormKit type="time" name="start" label="Début" value="23:15" />
            <FormKit type="time" name="end" label="Fin" value="23:15" />
            <FormKit name="calendar_id" type="hidden" :value="currentCalendarId" />
            <FormKit name="start_date" type="hidden" v-model="start" />
            <FormKit name="end_date" type="hidden" v-model="end" />
        </FormKit>
    </div>
</template>
<style lang="scss" scoped>
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
    border: 1px solid #ccc;
    display: flex;
    flex-direction: column;
    font-size: 1.5rem;
    background-color: rgba(0, 0, 0, 0.1);
    pointer-events: none;
    cursor: none;
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

.modifiable-day {
    cursor: pointer;
    pointer-events: all;
    background-color: white;
}

.current-day {
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
    align-items: center;
    z-index: 10;
}
</style>
