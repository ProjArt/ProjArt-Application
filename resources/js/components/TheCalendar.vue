<script setup>
import { ref, computed, reactive, toRaw } from "vue"
import useFetch from '../composables/useFetch';
import { API } from '../stores/api';
const calendar = ref({});
const calendarNames = ref([]);
const TODAY = ref(new Date())
const DAY_LABELS = ['LU', 'MA', 'ME', 'JE', 'VE', 'SA', 'DI'];
const DAY_LABELS_ORDERS = ['DI', 'MA', 'ME', 'JE', 'VE', 'SA', 'LU'];
const MONTH_LABELS = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"]

const toSwissDay = (day) => {
    const index = DAY_LABELS_ORDERS[day]
    return DAY_LABELS.indexOf(index, 0)
}

const numberOfDaysInMonth = (year, month) => {
    return new Date(year, month, 0).getDate();
}

const getAllDaysInMonth = (year, month) => {
    const date = new Date(year, month, 1)
    console.log({ year }, { month }, { date })
    const dates = [];
    while (date.getMonth() === month) {
        const day = new Date(date)
        dates.push({
            date: day,
            dayOfMonthNumber: day.getDate(),
            dayOfWeekNumber: toSwissDay(day.getDay()),
            dayOfWeekName: day.getDay(),
        });
        date.setDate(date.getDate() + 1)
    }
    return dates;
}

const getAllDaysInWeek = (days) => {
    const date = typeof days == 'number'
        ? new Date(TODAY.value.getFullYear(), TODAY.value.getMonth(), days)
        : TODAY.value;
    console.log({ date }, { days }, TODAY.value)
    const dates = [];
    const day = toSwissDay(date.getDay())
    date.setDate(date.getDate() - day)
    for (let i = 0; i <= 6; i++) {
        dates.push({
            date: date,
            dayOfMonthNumber: date.getDate(),
            dayOfWeekNumber: toSwissDay(date.getDay()),
            dayOfWeekName: date.getDay(),
        });
        date.setDate(date.getDate() + 1)
    }
    console.log(dates)
    return dates
}

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

const monthFormat = ((year, month) => {
    dates.value = getMonthFormat(year, month)
})

const weekFormat = ((days) => {
    dates.value = getAllDaysInWeek(days)
})

const dates = ref(getMonthFormat(TODAY.value.getFullYear(), TODAY.value.getMonth()));
const selectedDate = ref(TODAY.value)
const currDateCursor = ref(TODAY.value)
const dayLabels = ref(DAY_LABELS.slice())

const previousMonth = () => {
    const prevDate = new Date(currDateCursor.value);
    prevDate.setMonth(prevDate.getMonth() - 1);
    currDateCursor.value = prevDate;
    dates.value = getMonthFormat(currDateCursor.value.getFullYear(), currDateCursor.value.getMonth());
}

const nextMonth = () => {
    const nextDate = new Date(currDateCursor.value);
    nextDate.setMonth(nextDate.getMonth() + 1);
    console.log(nextDate)
    currDateCursor.value = nextDate;
    dates.value = getMonthFormat(currDateCursor.value.getFullYear(), currDateCursor.value.getMonth());
}
// TODO set next/previous Week on click
</script>
<template>
    <FormKit type="select" label="Calendrier" name="calendar" :options="calendarNames" validation="required"
        validation-visibility="dirty" />

    <div class="calendar">
        <header class="calendar__header">
            <button @click="previousMonth">&lt;&lt;</button>
            <button @click="nextMonth">&gt;&gt;</button>
            <button @click="monthFormat">Mois</button>
            <button @click="weekFormat">Semaines</button>
        </header>
        <div class="calendar__days-names">
            <div v-for="dayLabel in dayLabels">
                {{ dayLabel }}
            </div>
        </div>
        <div class="days">
            <div v-for="(day, index) in dates" class="day" :class="!day.date ? 'grey' : ''">
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
    background-color: white;
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

.grey {
    background-color: rgba(0, 0, 0, 0.1);
}
</style>
