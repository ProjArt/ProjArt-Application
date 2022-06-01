<script setup>
import { ref, computed, toRaw, watch } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";

// Constants
// ======================================

const TODAY = new Date();
const DAY_LABELS = ["LU", "MA", "ME", "JE", "VE", "SA", "DI"];
const DATE_OPTION = ["fr-ch", { year: "numeric", month: "long" }];
const AVAILABLE_LAYOUT = { MONTH: 0, WEEK: 1, DAY: 3 };

const CSS = {
  currentDay: "is-current-day",
  clickable: "is-clickable-day",
};

// Ref
// ======================================

const dates = ref([]);
const allCalendars = ref({});
const calendarsNames = ref({});
const currentLayout = ref(AVAILABLE_LAYOUT.MONTH);
const currentsCalendarIds = ref([]);
const displayedDate = ref(new Date().toLocaleDateString(...DATE_OPTION));
const newEventPopupRef = ref([]);
const showNewEventPopupRef = ref("");
const showCurrentEventsPopupRef = ref("");
const newEventForm = ref({});
const formUpdate = ref({});
const isSubmitted = ref(false);
const selectedDate = ref("");
const indexUnderEdition = ref("");
const currDateCursor = ref(TODAY);
const dayLabels = DAY_LABELS.slice();
const events = ref([]);
const calendarIdWhereToAddTheNewEvent = ref(2);

// Computed
// ======================================

const displayedDateManager = computed({
  get() {
    return displayedDate.value;
  },
  set({ year, month, year2, month2 }) {
    if (typeof year2 === "undefined" && typeof month2 === "undefined") {
      const date = new Date(year, month);
      displayedDate.value = date.toLocaleDateString(...DATE_OPTION);
    } else {
      let date1 = new Date(year, month);
      let date2 = new Date(year2, month2);
      if (year === year2) {
        const month = date1.toLocaleString("fr-ch", { month: "long" });
        date2 = date2.toLocaleDateString(...DATE_OPTION);
        displayedDate.value = `${month} - ${date2}`;
      } else {
        date1 = date1.toLocaleDateString(...DATE_OPTION);
        date2 = date2.toLocaleDateString(...DATE_OPTION);
        displayedDate.value = `${date1} - ${date2}`;
      }
    }
  },
});

const showNewEventPopup = computed({
  get: () => showNewEventPopupRef.value,
  set: (date) => {
    showNewEventPopup.value !== date
      ? (showNewEventPopupRef.value = date)
      : (showNewEventPopupRef.value = "");
  },
});

const showCurrentEventsPopup = computed({
  get() {
    return showCurrentEventsPopupRef.value;
  },
  set(events) {
    showCurrentEventsPopupRef.value = events;
  },
});

const newEventPopup = computed({
  get() {
    return newEventPopupRef.value;
  },
  set(events) {
    newEventPopupRef.value = events;
  },
});

const editableCalendarsNames = computed(() => {
  let names = {};
  for (const [key, value] of Object.entries(allCalendars.value)) {
    if (allCalendars.value[key].can_edit) {
      names[value.id] = value.name;
    }
  }
  return names;
});

// Helpers
// ======================================

/**
 * Console log to value of an array of refs
 * @param {Array} obj { name: string, value: ref }
 */
function consoleRef(array) {
  array.forEach((element) => {
    const item = toRaw(element.value.value);
    console.log({ [element.name]: item });
  });
}

function toSwissDay(day) {
  return day - 1 < 0 ? 6 : day - 1; // si dimanche, on ajoute sinon on retire un jour
}

function formatDayObject(ref) {
  return {
    class: setDayCssClass(ref),
    local: ref.toLocaleDateString(),
    dayOfMonthNumber: ref.getDate(),
    dayOfWeekNumber: toSwissDay(ref.getDay()),
    events: [],
  };
}

function getAllDaysInMonth(year, month) {
  const date = new Date(year, month, 1);
  const dates = {};
  while (date.getMonth() === month) {
    const day = new Date(date);
    const key = day.toLocaleDateString();
    dates[key] = formatDayObject(day);
    date.setDate(date.getDate() + 1);
  }
  return dates;
}

function getMonday(date) {
  date = new Date(date);
  const day = date.getDay();
  const diff = date.getDate() - day + (day == 0 ? -6 : 1);
  return new Date(date.setDate(diff));
}

function getSunday(date) {
  date = new Date(date);
  const day = date.getDay();
  const diff = date.getDate() - day + (day == 0 ? -6 : 1);
  return new Date(date.setDate(diff + 6));
}

function toChDate(date) {
  date = date.split(" ")[0];
  date = date.split("-");
  date = `${date[2]}/${date[1]}/${date[0]}`;
  return date;
}

function prepareFormBeforeSending(rawForm) {
  //  expected output: 2022-05-27 12:06:28
  const date = rawForm.start_date;
  const start = `${date} ${rawForm.start}`;
  const end = `${date} ${rawForm.end}`;
  rawForm.start = start;
  rawForm.end = end;
  delete rawForm.end_date;
  delete rawForm.start_date;
  return rawForm;
}

function hideNewEventForm() {
  showNewEventPopup.value = "";
}

function hideEditEventsForm() {
  showCurrentEventsPopup.value = "";
}

function showNewEventForm(event) {
  hideEditEventsForm();
  event.stopPropagation();
  showNewEventPopup.value = true;
  showCurrentEventsPopup.value = false;
}

function getAllDaysInMonthAndBeginning(year, month) {
  const days = [];
  const daysInMonth =
    typeof year !== "undefined" && typeof month !== "undefined"
      ? getAllDaysInMonth(year, month)
      : getAllDaysInMonth(TODAY.getFullYear(), TODAY.getMonth());
  for (const [index, date] of Object.entries(daysInMonth)) {
    let i = 0;
    if (index === Object.keys(daysInMonth)[0]) {
      const day = Object.entries(daysInMonth)[0][1];
      for (let i = 0; i < day.dayOfWeekNumber; i++) {
        days.push({});
      }
    }

    days.push(date);
  }
  return days;
}

function getAllDaysInWeek(choosenDate) {
  const monday = getMonday(choosenDate);
  const dates = {};
  for (let i = 0; i <= 6; i++) {
    const date = new Date(
      monday.getFullYear(),
      monday.getMonth(),
      monday.getDate() + i
    );
    const key = date.toLocaleDateString();
    dates[key] = formatDayObject(date);
  }
  return dates;
}

function setDayCssClass(date) {
  const today = new Date(
    TODAY.getFullYear(),
    TODAY.getMonth(),
    TODAY.getDate()
  );
  if (typeof date === "undefined") return "";
  else if (today.toLocaleDateString() === date.toLocaleDateString())
    return `${CSS.clickable} ${CSS.currentDay}`;
  else return CSS.clickable;
}

function checkIfBothDatesAreInSameMonth(date1, date2) {
  const date1Month = date1.getMonth();
  const date2Month = date2.getMonth();
  return date1Month === date2Month;
}

function formatCurrentDateForDisplay(date) {
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    displayedDateManager.value = {
      year: date.getFullYear(),
      month: date.getMonth(),
    };
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    const monday = getMonday(date);
    const sunday = getSunday(date);
    checkIfBothDatesAreInSameMonth(monday, sunday)
      ? (displayedDateManager.value = {
          year: date.getFullYear(),
          month: date.getMonth(),
        })
      : (displayedDateManager.value = {
          year: monday.getFullYear(),
          month: monday.getMonth(),
          year2: sunday.getFullYear(),
          month2: sunday.getMonth(),
        });
  }
}

function getWeekRelativeToDate(date, numberOfDays) {
  return date.setDate(date.getDate() + parseInt(numberOfDays));
}

function getMonthRelativeToDate(date, numberOfMonths) {
  return date.setMonth(date.getMonth() + parseInt(numberOfMonths));
}

function chDateToYMD(dateString, separator) {
  const datesParts = dateString.split("/");
  const year = datesParts[2];
  const month = datesParts[1];
  const day = datesParts[0];
  return year + separator + month + separator + day;
}

// CRUD operations on API
// ======================================

async function storeEvent(form) {
  form = prepareFormBeforeSending(form);
  const response = await useFetch({
    url: API.storeEvent.path(),
    method: API.storeEvent.method,
    data: form,
  });
  if (response.success === true) {
    try {
      displayNewlyCreatedEvent(form);
    } catch (error) {
      console.log(error);
    }
  } else {
    console.log("error");
  }
}

async function deleteEvent(dayId, eventId) {
  const response = await useFetch({
    url: API.deleteEvent.path(eventId),
    method: API.deleteEvent.method,
  });
  if (response.success === true) {
    try {
      const date = toChDate(dayId);
      events.value[date] = events.value[date].filter(
        (event) => event.id !== eventId
      );
      const newEvents = sortEventsByDate(date);
      newEventPopup.value = newEvents;
    } catch (error) {
      console.log(error);
    }
  } else {
    console.log("error");
  }
}

async function updateEvent(form) {
  const date = form.start_date;
  form.start_date = chDateToYMD(form.start_date, "-");
  form.end_date = chDateToYMD(form.end_date, "-");
  form = prepareFormBeforeSending(form);
  const response = await useFetch({
    url: API.updateEvent.path(form.id),
    method: API.updateEvent.method,
    data: form,
  });
  if (response.success === true) {
    try {
      allCalendars.value.forEach((calendar) => {
        if (calendar.id == form.calendar_id) {
          form["can_edit"] = calendar.can_edit;
        }
      });
      let othersEvents = toRaw(
        events.value[date].filter((event) => event.id !== form.id)
      );
      othersEvents.push(form);
      events.value[date] = othersEvents;
      const newEvents = sortEventsByDate(date);
      newEventPopup.value = newEvents;
      indexUnderEdition.value = null;
    } catch (error) {
      console.log(error);
    }
  } else {
    console.log("error");
  }
}

async function getCalendars() {
  const response = await useFetch({
    url: API.getEvents.path(),
    method: API.getEvents.method,
  });
  if (response.success === true) {
    return response.data;
  } else {
    console.log(response, "error");
    return [];
  }
}

async function setCalendars(calendars) {
  const userCalendars = {};
  calendars.forEach((calendar) => {
    userCalendars[calendar.id] = calendar.name;
  });
  calendarsNames.value = userCalendars;
  allCalendars.value = calendars;
  currentsCalendarIds.value = [calendars[0].id.toString()];
}

// Features
// ======================================

function displayNewlyCreatedEvent(event) {
  const calendarId = calendarIdWhereToAddTheNewEvent.value;
  allCalendars.value.forEach((calendar) => {
    if (calendar.id == calendarId) {
      const index = toChDate(event.start);
      const canEdit = calendar.can_edit;
      event["can_edit"] = canEdit;
      events.value[index].push(event);
    }
  });
}

function actualPeriod() {
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    dates.value = getAllDaysInMonthAndBeginning(
      TODAY.getFullYear(),
      TODAY.getMonth()
    );
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    dates.value = getAllDaysInWeek(TODAY);
  }
  currDateCursor.value = TODAY;
  displayedDateManager.value = {
    year: TODAY.getFullYear(),
    month: TODAY.getMonth(),
  };
}

function nextPeriod() {
  const dateUnderCursor = new Date(currDateCursor.value);
  let nextPeriod;
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    nextPeriod = new Date(getMonthRelativeToDate(dateUnderCursor, 1));
    dates.value = getAllDaysInMonthAndBeginning(
      nextPeriod.getFullYear(),
      nextPeriod.getMonth()
    );
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    nextPeriod = new Date(getWeekRelativeToDate(dateUnderCursor, 7));
    dates.value = getAllDaysInWeek(nextPeriod);
  }
  currDateCursor.value = nextPeriod;
  formatCurrentDateForDisplay(nextPeriod);
}

function previousPeriod() {
  const dateUnderCursor = new Date(currDateCursor.value);
  let previousPeriod;
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    previousPeriod = new Date(getMonthRelativeToDate(dateUnderCursor, -1));
    dates.value = getAllDaysInMonthAndBeginning(
      previousPeriod.getFullYear(),
      previousPeriod.getMonth()
    );
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    previousPeriod = new Date(getWeekRelativeToDate(dateUnderCursor, -7));
    dates.value = getAllDaysInWeek(previousPeriod);
  }
  currDateCursor.value = previousPeriod;
  formatCurrentDateForDisplay(previousPeriod);
}

function getEvents() {
  const iterable = currentsCalendarIds.value.values();
  const calendars = [];
  for (const [value] of iterable) {
    allCalendars.value.forEach((calendar) => {
      if (calendar.id == value) {
        calendars.push(calendar);
      }
    });
  }
  return calendars;
}

function setEvents(calendars) {
  events.value = {};
  calendars.forEach((calendar) => {
    const canEdit = calendar.can_edit;
    calendar.events.forEach((event) => {
      const index = toChDate(event.start);
      event.can_edit = canEdit;
      event.local = index;
      if (events.value.hasOwnProperty(index)) {
        events.value[index].push(event);
      } else {
        events.value[index] = [event];
      }
    });
  });
}

function sortEventsByDate(index) {
  if (typeof events.value[index] !== "undefined") {
    let sortedEvents = [];
    sortedEvents = events.value[index].sort((a, b) => {
      return new Date(a.start) - new Date(b.start);
    });
    return sortedEvents;
  } else {
    return [];
  }
}

function showCurrentEvent(index, dayIndex) {
  hideNewEventForm();
  if (index == showCurrentEventsPopup.value) {
    selectedDate.value = null;
    showCurrentEventsPopup.value = null;
  } else {
    selectedDate.value = dayIndex;
    showCurrentEventsPopup.value = index;
    newEventPopup.value = sortEventsByDate(index);
  }
}

function showEventEditForm(startDate, id) {
  const date = toChDate(startDate);
  indexUnderEdition.value = id !== indexUnderEdition.value ? id : undefined;
  let newEvent = toRaw(events.value[date]);
  newEvent = newEvent.filter((event) => {
    return event.id == id;
  });
  let start;
  let end;
  try {
    start = newEvent[0].start.split(" ")[1].split(":");
    start = `${start[0]}:${start[1]}`;
    end = newEvent[0].end.split(" ")[1].split(":");
    end = `${end[0]}:${end[1]}`;
  } catch (error) {
    start = "";
    end = "";
  }
  formUpdate.value = {
    title: newEvent[0].title,
    location: newEvent[0].location,
    description: newEvent[0].description,
    start: start,
    end: end,
    start_date: date,
    end_date: date,
  };
}

// At startup
// ======================================

(async function startUp() {
  const calendars = await getCalendars();
  await setCalendars(calendars);
  setEvents(getEvents());
  dates.value = getAllDaysInMonthAndBeginning(
    TODAY.getFullYear(),
    TODAY.getMonth()
  );
})();

// Watcher(s)
// ======================================

watch(currentLayout, () => {
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    dates.value = getAllDaysInMonthAndBeginning(
      currDateCursor.value.getFullYear(),
      currDateCursor.value.getMonth()
    );
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    dates.value = getAllDaysInWeek(currDateCursor.value);
  }
});

watch(currentsCalendarIds, () => {
  setEvents(getEvents());
});
</script>
<template>
  <div class="calendar">
    <!--====  Calendar Header  ====-->
    <h3>{{ displayedDateManager }}</h3>
    <div class="calendar__choose">
      <FormKit
        v-model="currentsCalendarIds"
        type="checkbox"
        label="Calendrier"
        :options="calendarsNames"
      />
    </div>
    <header class="calendar__header">
      <button @click="previousPeriod">&lt;&lt;</button>
      <button @click="nextPeriod">&gt;&gt;</button>
      <button @click="currentLayout = AVAILABLE_LAYOUT.MONTH">Mois</button>
      <button @click="currentLayout = AVAILABLE_LAYOUT.WEEK">Semaines</button>
      <button @click="actualPeriod">Aujaurd'hui</button>
      <button @click="showNewEventForm">Ajouter événement</button>
    </header>
    <!--====  Calendar days names  ====-->
    <div class="calendar__days-names">
      <div v-for="dayLabel in dayLabels">
        {{ dayLabel }}
      </div>
    </div>
    <!--====  Calendar days  ====-->
    <div class="calendar__days">
      <div
        v-for="(day, index) in dates"
        class="calendar__day"
        @click="showCurrentEvent(day?.local, index)"
        :class="(day?.class, selectedDate === index ? 'is-selected-day' : '')"
        :key="index"
        :date-id="day?.local"
      >
        <p class="calendar__day-number">{{ day?.dayOfMonthNumber }}</p>
        <p class="calendar__day-date">{{ day?.local }}</p>
        <div v-for="event in sortEventsByDate(day?.local)">
          <p class="calendar__event">{{ event.title }}</p>
        </div>
      </div>
    </div>
  </div>
  <!--====  Popup new event  ====-->
  <div class="popup popup--new-event" v-show="showNewEventPopup">
    <FormKit
      type="form"
      v-model="newEventForm"
      :form-class="isSubmitted ? 'hide' : 'show'"
      submit-label="Enregistrer"
      @submit="storeEvent"
    >
      <h2>Ajouter un événement</h2>
      <FormKit
        type="text"
        name="title"
        validation="required"
        label="Titre"
        :value="new Date().getHours() + ':' + new Date().getMinutes()"
      />
      <FormKit
        type="text"
        name="location"
        validation="required"
        label="Lieu"
        value="HEIG"
      />
      <FormKit
        type="textarea"
        name="description"
        validation="required"
        label="Description"
        value="..."
      />
      <FormKit type="time" name="start" label="Début" value="08:00" />
      <FormKit type="time" name="end" label="Fin" value="08:00" />
      <FormKit
        name="start_date"
        type="date"
        value="2022-06-01"
        label="Date de Début"
        validation="required"
      />
      <FormKit
        name="end_date"
        type="date"
        value="2022-06-01"
        label="Date de Fin"
        validation="required"
      />
      <FormKit
        v-model="calendarIdWhereToAddTheNewEvent"
        type="select"
        label="calendrier"
        name="calendar_id"
        validation="required"
      >
        <option v-for="(name, id) in editableCalendarsNames" :value="id">
          {{ name }}
        </option>
      </FormKit>
    </FormKit>
  </div>
  <!--====  Popup edit event  ====-->
  <div class="popup popup--edit-events" v-show="showCurrentEventsPopup">
    <h2>Current Events</h2>
    <article class="popup__event" v-for="(event, index) in newEventPopup">
      <div class="event">
        <div class="event__infos">
          <p>edit: {{ event.can_edit }}</p>
          <p>id: {{ event.id }}</p>
          <p>titre: {{ event.title }}</p>
          <p>lieu: {{ event.location }}</p>
          <p>description: {{ event.description }}</p>
          <p>début: {{ event.start }}</p>
          <p>fin: {{ event.end }}</p>
        </div>
        <button
          v-show="event.can_edit"
          @click="deleteEvent(event.start, event.id)"
        >
          supprimer
        </button>
        <button
          v-show="event.can_edit"
          @click="showEventEditForm(event.start, event.id)"
        >
          editer
        </button>
        <FormKit
          type="form"
          v-model="formUpdate"
          submit-label="Enregistrer"
          @submit="updateEvent"
          v-if="indexUnderEdition === event.id"
          :key="event.id"
        >
          <FormKit
            type="text"
            name="title"
            validation="required"
            label="Titre"
          />
          <FormKit
            type="text"
            name="location"
            validation="required"
            label="Lieu"
          />
          <FormKit
            type="textarea"
            name="description"
            validation="required"
            label="Description"
          />
          <FormKit type="time" name="start" label="Début" />
          <FormKit type="time" name="end" label="Fin" />
          <FormKit name="start_date" type="hidden" :value="event.start" />
          <FormKit name="end_date" type="hidden" :value="event.start" />
          <FormKit name="id" type="hidden" :value="event.id" />
          <FormKit
            v-model="calendarIdWhereToAddTheNewEvent"
            type="select"
            label="calendrier"
            name="calendar_id"
            validation="required"
          >
            <option v-for="(name, id) in editableCalendarsNames" :value="id">
              {{ name }}
            </option>
          </FormKit>
        </FormKit>
      </div>
    </article>
  </div>
</template>
<style lang="scss" scoped>
.calendar {
  margin: 2rem;
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
  text-overflow: ellipsis;
}

.calendar__days-names {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  grid-gap: 1rem;
}

.calendar__choose {
  display: flex;
}

/* :deep(.formkit-option) { */
/*     display: flex; */
/* } */

:deep(.formkit-option .formkit-wrapper) {
  display: grid;
  grid-template-columns: auto 1fr;
}

.calendar__day-number {
  font-size: 1.5rem;
  text-align: left;
  padding: 0.5rem;
}

.calendar__day-date {
  font-size: 0.8rem;
  text-align: left;
  padding: 0.5rem;
  opacity: 0.5;
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

.is-clickable-day {
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

.popup--new-event {
}

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
