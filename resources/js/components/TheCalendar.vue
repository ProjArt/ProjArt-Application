<script setup>
import { ref, computed, reactive, toRaw, watch } from "vue"
import useFetch from '../composables/useFetch';
import { API } from '../stores/api';

// local variables
// ====================================== 

const DAY_LABELS = ['LU', 'MA', 'ME', 'JE', 'VE', 'SA', 'DI'];
const DAY_LABELS_ORDERS = ['DI', 'MA', 'ME', 'JE', 'VE', 'SA', 'LU'];
const MONTH_LABELS = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"]
const AVAILABLE_LAYOUT = { MONTH: 0, WEEK: 1, DAY: 3 };

// Ref & Computed 
// ====================================== 

const calendar = ref({});
const calendarNames = ref([]);
const TODAY = ref(new Date())
const currentLayout = ref(AVAILABLE_LAYOUT.MONTH);

// Helpers
// ====================================== 

const toSwissDay = (day) => {
    const index = DAY_LABELS_ORDERS[day]
    return DAY_LABELS.indexOf(index, 0)
}

const numberOfDaysInMonth = (year, month) => {
    return new Date(year, month, 0).getDate();
}

const formatDayObject = (ref) => {
    return {
        date: ref,
        local: ref.toLocaleDateString(),
        class: setDayCssClass(ref),
        dayOfMonthNumber: ref.getDate(),
        dayOfWeekNumber: toSwissDay(ref.getDay()),
        dayOfWeekName: ref.getDay(),
    }
}

const getAllDaysInMonth = (year, month) => {
    const date = new Date(year, month, 1)
    const dates = [];
    while (date.getMonth() === month) {
        const day = new Date(date)
        dates.push(formatDayObject(day));
        date.setDate(date.getDate() + 1)
    }
    return dates;
}

const getAllDaysInWeek = (days) => {
    const date = typeof days == 'number'
        ? new Date(TODAY.value.getFullYear(), TODAY.value.getMonth(), days)
        : TODAY.value;
    const dates = [];
    const day = toSwissDay(date.getDay())
    date.setDate(date.getDate() - day)
    for (let i = 0; i <= 6; i++) {
        dates.push(formatDayObject(date));
        date.setDate(date.getDate() + 1)
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
    console.log(response.data);
    calendarNames.value = response.data.map(event => event.name);
    calendar.value = response.data
    console.log(calendar.value);
    console.log(new Date(2022, 0, 0).getDate());
}

const getMonthFormat = (year, month) => {
    const daysArray = []
    const daysInMonth = year && month
        ? getAllDaysInMonth(year, month)
        : getAllDaysInMonth(TODAY.value.getFullYear(), TODAY.value.getMonth())
    daysInMonth.forEach((date, index) => {
        let i = 0
        if (index === 0) {
            while (date.dayOfWeekNumber !== i) {
                daysArray.push({})
                i++
            }
        }
        daysArray.push(date)
    })
    return daysArray
}

const setDisplayedDates = (timeInterval) => {
    const nextDate = new Date(currDateCursor.value);
    if (timeInterval === 0) {
        if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
            dates.value = getMonthFormat(TODAY.value.getFullYear(), TODAY.value.getMonth());
        } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
            dates.value = getAllDaysInWeek(TODAY.value.getDate());
        }
    } else if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
        nextDate.setMonth(nextDate.getMonth() + parseInt(timeInterval));
        currDateCursor.value = nextDate;
        dates.value = getMonthFormat(currDateCursor.value.getFullYear(), currDateCursor.value.getMonth());
    } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
        nextDate.setDate(nextDate.getDate() + parseInt(timeInterval));
        currDateCursor.value = nextDate;
        dates.value = getAllDaysInWeek(currDateCursor.value.getDate());
    }
}

const setDayCssClass = (date) => {
    const today = new Date(TODAY.value.getFullYear(), TODAY.value.getMonth(), TODAY.value.getDate());
    if (typeof date === 'undefined') {
        return ''
    } else if (today.toLocaleDateString() === date.toLocaleDateString()) {
        return 'currentDay'
    } else {
        return 'white'
    }
}

const changeLayout = (next) => {
    if (next && currentLayout.value === AVAILABLE_LAYOUT.MONTH) setDisplayedDates(1)
    else if (!next && currentLayout.value === AVAILABLE_LAYOUT.MONTH) setDisplayedDates(-1)
    else if (next && currentLayout.value === AVAILABLE_LAYOUT.WEEK) setDisplayedDates(7)
    else if (!next && currentLayout.value === AVAILABLE_LAYOUT.WEEK) setDisplayedDates(-7)
}

// Watcher(s)
// ====================================== 

watch(currentLayout, () => {
    if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
        dates.value = getMonthFormat(currDateCursor.value.getFullYear(), currDateCursor.value.getMonth());
    } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
        dates.value = getAllDaysInWeek(currDateCursor.value.getDate());
    }
})

const dates = ref(getMonthFormat(TODAY.value.getFullYear(), TODAY.value.getMonth()));
const selectedDate = ref(TODAY.value)
const currDateCursor = ref(TODAY.value)
const dayLabels = ref(DAY_LABELS.slice())

</script>
<template>
    <FormKit type="select" label="Calendrier" name="calendar" :options="calendarNames" validation="required"
        validation-visibility="dirty" />

    <div class="calendar">
        <header class="calendar__header">
            <button @click="changeLayout(false)">&lt;&lt;</button>
            <button @click="changeLayout(true)">&gt;&gt;</button>
            <button @click="currentLayout = AVAILABLE_LAYOUT.MONTH">Mois</button>
            <button @click="currentLayout = AVAILABLE_LAYOUT.WEEK">Semaines</button>
            <button @click="setDisplayedDates(0)">Aujaurd'hui</button>
        </header>
        <div class="calendar__days-names">
            <div v-for="dayLabel in dayLabels">
                {{ dayLabel }}
            </div>
        </div>
        <div class="days">
            <div v-for="(day, index) in dates" class="day" :class="day.class">
                <p class="day__day-number">{{ day.dayOfMonthNumber }}</p>
            </div>
        </div>
    </div>
</template>
<style lang="scss" scoped>
.calendar__header {
    display: flex;
    justify-content: center;
    align-items: center;
}

.days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-gap: 1rem;
}

.day {
    min-height: 100px;
    border: 1px solid #ccc;
    display: flex;
    flex-direction: column;
    font-size: 1.5rem;
    background-color: rgba(0, 0, 0, 0.1);
    cursor: pointer;
}

.calendar__days-names {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-gap: 1rem;
}

.day__day-number {
    font-size: 1.5rem;
    text-align: left;
    padding: 0.5rem;
}

.white {
    background-color: white;
}

.currentDay {
    background-color: lightblue;
}
</style>
