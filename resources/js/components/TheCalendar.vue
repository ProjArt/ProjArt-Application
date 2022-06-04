<script setup>
import { ref, computed, toRaw, watch } from "vue";
import useFetch from "../composables/useFetch";
import * as useDate from "../composables/useDate";
import { API } from "../stores/api";

// Constants
// ======================================

const TODAY = new Date();
const DAY_LABELS = ["LU", "MA", "ME", "JE", "VE", "SA", "DI"];
const MONTH_LABELS = ["JANVIER", "FEVRIER", "MARS", "AVRIL", "MAI", "JUIN", "JUILLET", "AOUT", "SEPTEMBRE", "OCTOBRE", "NOVEMBRE", "DECEMBRE"];
const DATE_OPTION = ["fr-ch", { year: "numeric", month: "long" }];
const AVAILABLE_LAYOUT = { MONTH: 0, WEEK: 1, LIST: 3, DAY: 4 };
const AVAILABLE_POPUP = { STORE_EVENT: 0, STORE_CALENDAR: 1, SHOW_EVENT: 2, EDIT_CALENDAR: 3 }

// Ref
// ======================================

const dates = ref([]);
const allCalendars = ref({});
const calendarsNames = ref({});
const currentLayout = ref(AVAILABLE_LAYOUT.MONTH);
const currentsCalendarIds = ref(null);
const displayedDate = ref(null);
const newEventPopupRef = ref([]);
const showNewEventPopupRef = ref("");
const showCurrentEventsPopupRef = ref("");
const newEventForm = ref({});
const newCalendarForm = ref({});
const formUpdate = ref({});
const isSubmitted = ref(false);
const selectedDate = ref("");
const indexUnderEdition = ref("");
const currDateCursor = ref(TODAY);
const dayLabels = DAY_LABELS.slice();
const events = ref([]);
const calendarIdWhereToAddTheNewEvent = ref(2);
const currentPopup = ref(null)

// Computed
// ======================================

const displayedDateManager = computed({
  get() {
    return displayedDate.value;
  },
  set({ dateStart, dateEnd }) {
    if (typeof dateEnd === "undefined") {
      displayedDate.value = {
        year1: dateStart.getFullYear(),
        month1: MONTH_LABELS[dateStart.getMonth()],
        day1: dateStart.getDate(),
        weekOfYear: useDate.getWeekYearNumber(dateStart)
      };
    } else {
      displayedDate.value = {
        year1: dateStart.getFullYear(),
        month1: MONTH_LABELS[dateStart.getMonth()],
        day1: dateStart.getDate(),
        year2: dateEnd.getFullYear(),
        month2: MONTH_LABELS[dateEnd.getMonth()],
        day2: dateEnd.getDate(),
        weekOfYear: useDate.getWeekYearNumber(dateStart)
      };
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

const currentEventPopupIndex = computed({
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

const getCalendarsData = computed(() => {
  let calendars = {};
  for (const [key, value] of Object.entries(allCalendars.value)) {
    calendars[value.id] = value;
  }
  return calendars;
});

const layoutStorage = computed({
  get() {
    return parseInt(localStorage.getItem("layout"))
  },
  set(layoutId) {
    localStorage.setItem("layout", layoutId);
    currentLayout.value = parseInt(layoutId);
  },
})

const selectedCalendarsIdStorage = computed({
  get() {
    return JSON.parse(localStorage.getItem("calendars"))
  },
  set(calendarsIds) {
    localStorage.setItem("calendars", JSON.stringify(calendarsIds));
  },
})

// Helpers
// ======================================

function getKeyByValue(object, value) {
  return Object.keys(object).find(key => object[key] === value);
}
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

function showNewEventForm(event) {
  event.stopPropagation();
  currentPopup.value = currentPopup.value === AVAILABLE_POPUP.STORE_EVENT
    ? null
    : AVAILABLE_POPUP.STORE_EVENT;
}

function showNewCalendarForm(event) {
  event.stopPropagation();
  currentPopup.value = currentPopup.value === AVAILABLE_POPUP.STORE_CALENDAR
    ? null
    : AVAILABLE_POPUP.STORE_CALENDAR;
}

function showEditCalendarForm(event) {
  event.stopPropagation();
  currentPopup.value = currentPopup.value === AVAILABLE_POPUP.EDIT_CALENDAR
    ? null
    : AVAILABLE_POPUP.EDIT_CALENDAR;
}

function formatCurrentDateForDisplay(date, nextDays = 0) {
  const date2 = new Date(date.getFullYear(), date.getMonth(), date.getDate() + nextDays)
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    displayedDateManager.value = { dateStart: date };
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    const monday = useDate.getMonday(date);
    const sunday = useDate.getSunday(date);
    displayedDateManager.value = { dateStart: monday, dateEnd: sunday };
  } else if (currentLayout.value === AVAILABLE_LAYOUT.DAY) {
    displayedDateManager.value = { dateStart: date };
  } else if (currentLayout.value === AVAILABLE_LAYOUT.LIST) {
    displayedDateManager.value = { dateStart: date, dateEnd: date2 };
  }
}

// CRUD operations on API
// ======================================

async function storeCalendar(form) {
  const response = await useFetch({
    url: API.storeCalendar.path(),
    method: API.storeCalendar.method,
    data: form,
  });
  if (response.success === true) {
    try {
      const newId = response.data.reduce((acc, curr) => {
        if (curr.id > acc) acc = curr.id;
        return acc;
      }, 0);
      const newCalendar = {
        can_edit: true,
        events: [],
        id: newId,
        name: form.name
      }
      allCalendars.value.push(newCalendar);
      const userCalendars = {};
      allCalendars.value.forEach((calendar) => {
        userCalendars[calendar.id] = calendar.name;
      });
      calendarsNames.value = userCalendars;
    } catch (error) {
      console.log(error);
    }
  } else {
    console.log("error");
  }
}

async function storeEvent(form) {
  form = prepareFormBeforeSending(form);
  const response = await useFetch({
    url: API.storeEvent.path(),
    method: API.storeEvent.method,
    data: form,
  });
  if (response.success === true) {
    try {
      form.id = response.data.id;
      displayNewlyCreatedEvent(form);
    } catch (error) {
      console.log(error);
    }
  } else {
    console.log("error");
  }
}

async function deleteEvent(dayId, eventId, calendarId) {
  const response = await useFetch({
    url: API.deleteEvent.path(eventId),
    method: API.deleteEvent.method,
  });
  if (response.success === true) {
    try {
      const date = toSwissDate(dayId);
      const filtredEvents = events.value[date].filter(
        (event) => event.id !== eventId
      );
      events.value[date] = filtredEvents;
      allCalendars.value.forEach((calendar) => {
        if (calendar.id === calendarId) {
          calendar.events = filtredEvents;
        }
      })
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
  form.start_date = useDate.swissDateToYMD(form.start_date, "-");
  form.end_date = useDate.swissDateToYMD(form.end_date, "-");
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
      allCalendars.value.forEach((calendar) => {
        if (calendar.id == form.calendar_id) {
          calendar.events = othersEvents;
        }
      });
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

async function setCalendars(calendars, setIds = true) {
  const userCalendars = {};
  calendars.forEach((calendar) => {
    userCalendars[calendar.id] = calendar.name;
  });
  calendarsNames.value = userCalendars;
  allCalendars.value = calendars;
  if (setIds) {
    const storageValue = localStorage.getItem("calendars");
    currentsCalendarIds.value = storageValue && typeof JSON.parse(storageValue) == "object"
      ? JSON.parse(storageValue)
      : [calendars[0].id.toString()]
  }
}

async function deleteCalendar(calendarId) {
  const response = await useFetch({
    url: API.deleteCalendar.path(calendarId),
    method: API.deleteCalendar.method,
  });
  if (response.success === true) {
    try {
      const calendars = allCalendars.value.filter((calendar) => calendar.id !== calendarId);
      setCalendars(calendars, false);
    } catch (error) {
      console.log(error);
    }
  } else {
    console.log("error");
  }
}

async function updateCalendar(form) {
  const response = await useFetch({
    url: API.updateCalendar.path(form.calendar_id),
    method: API.updateCalendar.method,
    data: {
      name: form.name
    },
  });
  if (response.success === true) {
    try {
      const calendars = allCalendars.value.map((calendar) => {
        if (calendar.id == form.calendar_id) {
          calendar.name = form.name
        }
        return calendar;
      });
      setCalendars(calendars, false);
    } catch (error) {
      console.log(error);
    }
  } else {
    console.log("error");
  }
}

// Features
// ======================================

function displayNewlyCreatedEvent(event) {
  const calendarId = calendarIdWhereToAddTheNewEvent.value;
  allCalendars.value.forEach((calendar) => {
    if (calendar.id == calendarId) {
      const index = useDate.toSwissDate(event.start);
      const canEdit = calendar.can_edit;
      event["can_edit"] = canEdit;
      event["calendar_id"] = calendar.id;
      const ids = Object.keys(currentsCalendarIds.value)
        .map(function (key) {
          return currentsCalendarIds.value[key];
        });
      if (ids.includes(calendarId.toString())) {
        if (events.value[index]) {
          events.value[index].push(event);
        } else {
          events.value[index] = [event];
        }
      }
      allCalendars.value.forEach((calendar) => {
        if (calendar.id == calendarId) {
          calendar.events.push(event)
        }
      })
    }
  });
}

function actualPeriod() {
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    dates.value = useDate.getAllDaysInMonthAndBeginning(
      TODAY.getFullYear(),
      TODAY.getMonth()
    );
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    dates.value = useDate.getAllDaysInWeek(TODAY);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.DAY) {
    dates.value = useDate.getDaysFromDate(TODAY, 1);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.LIST) {
    dates.value = useDate.getDaysFromDate(TODAY, 30);
  }
  currDateCursor.value = TODAY;
  displayedDateManager.value = { dateStart: TODAY };
}

function nextPeriod() {
  const dateUnderCursor = new Date(currDateCursor.value);
  let nextPeriod;
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    nextPeriod = new Date(useDate.getMonthRelativeToDate(dateUnderCursor, 1));
    dates.value = useDate.getAllDaysInMonthAndBeginning(
      nextPeriod.getFullYear(),
      nextPeriod.getMonth()
    );
    formatCurrentDateForDisplay(nextPeriod);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    nextPeriod = new Date(useDate.getDaysRelativeToDate(dateUnderCursor, 7));
    const monday = useDate.getMonday(nextPeriod);
    dates.value = useDate.getAllDaysInWeek(monday, 7);
    formatCurrentDateForDisplay(monday, 7);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.LIST) {
    nextPeriod = new Date(useDate.getDaysRelativeToDate(dateUnderCursor, 30));
    dates.value = useDate.getDaysFromDate(nextPeriod);
    formatCurrentDateForDisplay(nextPeriod, 30);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.DAY) {
    nextPeriod = new Date(useDate.getDaysRelativeToDate(dateUnderCursor, 1));
    dates.value = useDate.getDaysFromDate(nextPeriod, 1);
    formatCurrentDateForDisplay(nextPeriod);
  }
  currDateCursor.value = nextPeriod;
}

function previousPeriod() {
  const dateUnderCursor = new Date(currDateCursor.value);
  let previousPeriod;
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    previousPeriod = new Date(useDate.getMonthRelativeToDate(dateUnderCursor, -1));
    dates.value = useDate.getAllDaysInMonthAndBeginning(
      previousPeriod.getFullYear(),
      previousPeriod.getMonth()
    );
    formatCurrentDateForDisplay(previousPeriod);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    previousPeriod = new Date(useDate.getDaysRelativeToDate(dateUnderCursor, -7));
    const monday = useDate.getMonday(previousPeriod);
    dates.value = useDate.getAllDaysInWeek(previousPeriod);
    formatCurrentDateForDisplay(monday, 7);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.LIST) {
    previousPeriod = new Date(useDate.getDaysRelativeToDate(dateUnderCursor, -30));
    dates.value = useDate.getDaysFromDate(previousPeriod);
    formatCurrentDateForDisplay(previousPeriod, 30);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.DAY) {
    previousPeriod = new Date(useDate.getDaysRelativeToDate(dateUnderCursor, -1));
    dates.value = useDate.getDaysFromDate(previousPeriod, 1);
    formatCurrentDateForDisplay(previousPeriod);
  }
  currDateCursor.value = previousPeriod;
}

function getEvents() {
  const calendars = [];
  for (const [key, value] of Object.entries(currentsCalendarIds.value)) {
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
      const index = useDate.toSwissDate(event.start);
      event.can_edit = canEdit;
      event.calendar_id = calendar.id;
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
  if (index == currentEventPopupIndex.value) {
    selectedDate.value = null;
    currentEventPopupIndex.value = null;
    currentPopup.value = null
  } else {
    selectedDate.value = dayIndex;
    currentEventPopupIndex.value = index;
    newEventPopup.value = sortEventsByDate(index);
    currentPopup.value = AVAILABLE_POPUP.SHOW_EVENT
  }
}

function showEventEditForm(startDate, id) {
  const date = useDate.toSwissDate(startDate);
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
  displayedDateManager.value = { dateStart: TODAY }
  currentLayout.value = layoutStorage.value || AVAILABLE_LAYOUT.MONTH
  const calendars = await getCalendars();
  await setCalendars(calendars);
  setEvents(getEvents());
  if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
    dates.value = useDate.getAllDaysInMonthAndBeginning(
      TODAY.getFullYear(),
      TODAY.getMonth()
    );
    formatCurrentDateForDisplay(currDateCursor.value);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    dates.value = useDate.getAllDaysInWeek(TODAY);
    const monday = useDate.getMonday(currDateCursor.value);
    formatCurrentDateForDisplay(monday, 7);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.LIST) {
    dates.value = useDate.getDaysFromDate(TODAY);
    formatCurrentDateForDisplay(currDateCursor.value, 30);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.DAY) {
    dates.value = useDate.getDaysFromDate(TODAY, 1);
    formatCurrentDateForDisplay(currDateCursor.value);
  }

  watch(currentsCalendarIds, () => {
    selectedCalendarsIdStorage.value = toRaw(currentsCalendarIds.value)
    console.log(toRaw(selectedCalendarsIdStorage.value))
    setEvents(getEvents());
  })

  watch(currentLayout, () => {
    layoutStorage.value = currentLayout.value;
    if (currentLayout.value === AVAILABLE_LAYOUT.MONTH) {
      dates.value = useDate.getAllDaysInMonthAndBeginning(
        currDateCursor.value.getFullYear(),
        currDateCursor.value.getMonth()
      );
      formatCurrentDateForDisplay(currDateCursor.value);
    } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
      dates.value = useDate.getAllDaysInWeek(currDateCursor.value);
      const monday = useDate.getMonday(currDateCursor.value);
      formatCurrentDateForDisplay(currDateCursor.value);
    } else if (currentLayout.value === AVAILABLE_LAYOUT.LIST) {
      dates.value = useDate.getDaysFromDate(currDateCursor.value);
      formatCurrentDateForDisplay(currDateCursor.value, 30);
    } else if (currentLayout.value === AVAILABLE_LAYOUT.DAY) {
      dates.value = useDate.getDaysFromDate(currDateCursor.value, 1);
      formatCurrentDateForDisplay(currDateCursor.value);
    }
  });
})();
</script>
<template>
  <div class="calendar">
    <!--====  Calendar Header  ====-->
    <div v-if="currentLayout === AVAILABLE_LAYOUT.MONTH">
      <h3>{{ displayedDateManager.year1 }}</h3>
    </div>

    <div v-if="currentLayout === AVAILABLE_LAYOUT.WEEK">
      <h3>{{ displayedDateManager.year1 }}</h3>
      <h3>Semaine {{ displayedDateManager.weekOfYear }}</h3>
      <p>{{ displayedDateManager.day1 }} {{ displayedDateManager.month1 }} {{ displayedDateManager.year1 }}
        <span v-show="displayedDateManager.day2"> - </span>{{ displayedDateManager.day2 }} {{
            displayedDateManager.month2
        }} {{ displayedDateManager.year2 }}
      </p>
    </div>

    <div v-if="currentLayout === AVAILABLE_LAYOUT.DAY">
      <h3>{{ displayedDateManager.day1 }} {{ displayedDateManager.month1 }} {{ displayedDateManager.year1 }}</h3>
    </div>

    <div v-if="currentLayout === AVAILABLE_LAYOUT.LIST">
      <h3>{{ displayedDateManager.year1 }}</h3>
      <p>{{ displayedDateManager.day1 }} {{ displayedDateManager.month1 }} {{ displayedDateManager.year1 }}
        <span v-show="displayedDateManager.day2"> - </span>{{ displayedDateManager.day2 }} {{
            displayedDateManager.month2
        }} {{ displayedDateManager.year2 }}
      </p>
    </div>

    <div class="calendar__choose">
      <FormKit v-model="currentsCalendarIds" type="checkbox" label="Calendrier" :options="calendarsNames" />
    </div>
    <header class="calendar__header">
      <button @click="previousPeriod">&lt;&lt;</button>
      <button @click="nextPeriod">&gt;&gt;</button>
      <button @click="currentLayout = AVAILABLE_LAYOUT.MONTH">Mois</button>
      <button @click="currentLayout = AVAILABLE_LAYOUT.WEEK">Semaines</button>
      <button @click="currentLayout = AVAILABLE_LAYOUT.LIST">List</button>
      <button @click="currentLayout = AVAILABLE_LAYOUT.DAY">Jour</button>
      <button @click="actualPeriod">Aujourd'hui</button>
      <button @click="showNewEventForm">Ajouter événement</button>
      <button @click="showNewCalendarForm">Ajouter un calendrier</button>
      <button @click="showEditCalendarForm">Editer les calendrier</button>
    </header>
    <!--====  Calendar days names  ====-->
    <div class="calendar__days-names">
      <div v-for="dayLabel in dayLabels">
        {{ dayLabel }}
      </div>
    </div>
    <!--====  Calendar no events message  ====-->
    <div class="calendar__no-events" v-show="currentLayout === AVAILABLE_LAYOUT.LIST">
      <p>Aucun événements pour cette période</p>
    </div>
    <!--====  Calendar days  ====-->
    <div class="calendar__days"
      :class="'calendar__days--' + getKeyByValue(AVAILABLE_LAYOUT, currentLayout).toLocaleLowerCase()">
      <div v-for="(day, index) in dates" class="calendar__day" @click="showCurrentEvent(day?.local, index)"
        :class="(selectedDate === index ? 'is-selected-day' : ''), day?.class, currentLayout === AVAILABLE_LAYOUT.LIST && !events.hasOwnProperty(day?.local) ? 'is-display-none' : ''"
        :key="index" :date-id="day?.local">
        <p class="calendar__day-number">{{ day?.dayOfMonthNumber }}</p>
        <p class="calendar__day-date">{{ day?.local }}</p>
        <div v-for="event in sortEventsByDate(day?.local)">
          <p class="calendar__event">{{ event.title }}</p>
        </div>
      </div>
    </div>
  </div>
  <!--====  Popup new event  ====-->
  <div class="popup popup--new-event" v-show="currentPopup === AVAILABLE_POPUP.STORE_EVENT">
    <FormKit type="form" v-model="newEventForm" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Enregistrer"
      @submit="storeEvent">
      <h2>Ajouter un événement</h2>
      <FormKit type="text" name="title" validation="required" label="Titre"
        :value="new Date().getHours() + ':' + new Date().getMinutes()" />
      <FormKit type="text" name="location" validation="required" label="Lieu" value="HEIG" />
      <FormKit type="textarea" name="description" validation="required" label="Description" value="..." />
      <FormKit type="time" name="start" label="Début" value="08:00" />
      <FormKit type="time" name="end" label="Fin" value="08:00" />
      <FormKit name="start_date" type="date" value="2022-06-01" label="Date de Début" validation="required" />
      <FormKit name="end_date" type="date" value="2022-06-01" label="Date de Fin" validation="required" />
      <FormKit v-model="calendarIdWhereToAddTheNewEvent" type="select" label="calendrier" name="calendar_id"
        validation="required">
        <option v-for="(name, id) in editableCalendarsNames" :value="id">
          {{ name }}
        </option>
      </FormKit>
    </FormKit>
  </div>
  <!--====  Popup store Calendar  ====-->
  <div class="popup popup--new-calendar" v-show="currentPopup === AVAILABLE_POPUP.STORE_CALENDAR">
    <FormKit type="form" v-model="newCalendarForm" :form-class="isSubmitted ? 'hide' : 'show'"
      submit-label="Enregistrer" @submit="storeCalendar">
      <h2>Ajouter un Calendrier</h2>
      <FormKit type="text" name="name" validation="required" label="Nom" />
    </FormKit>
  </div>
  <!--====  Popup Edit Calendar  ====-->
  <div class="popup popup--edit-calendar" v-show="currentPopup === AVAILABLE_POPUP.EDIT_CALENDAR">
    <h2>Editer les calendrier</h2>
    <div v-for="calendar in getCalendarsData">
      <FormKit v-if="calendar.can_edit" type="form" :form-class="isSubmitted ? 'hide' : 'show'"
        submit-label="Enregistrer" @submit="updateCalendar">
        <FormKit type="text" name="name" validation="required" :value="calendar.name" />
        <FormKit type="hidden" name="calendar_id" :value="calendar.id" />
        <button @click="deleteCalendar(calendar.id)">supprimer</button>
      </FormKit>
    </div>
  </div>
  <!--====  Popup edit event  ====-->
  <div class="popup popup--edit-events" v-show="currentPopup === AVAILABLE_POPUP.SHOW_EVENT">
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
        <button v-show="event.can_edit" @click="deleteEvent(event.start, event.id, event.calendar_id)">
          supprimer
        </button>
        <button v-show="event.can_edit" @click="showEventEditForm(event.start, event.id, event.calendar_id)">
          editer
        </button>
        <FormKit type="form" v-model="formUpdate" submit-label="Enregistrer" @submit="updateEvent"
          v-if="indexUnderEdition === event.id" :key="event.id">
          <FormKit type="text" name="title" validation="required" label="Titre" />
          <FormKit type="text" name="location" validation="required" label="Lieu" />
          <FormKit type="textarea" name="description" validation="required" label="Description" />
          <FormKit type="time" name="start" label="Début" />
          <FormKit type="time" name="end" label="Fin" />
          <FormKit name="start_date" type="hidden" :value="event.start" />
          <FormKit name="end_date" type="hidden" :value="event.start" />
          <FormKit name="id" type="hidden" :value="event.id" />
          <FormKit v-model="calendarIdWhereToAddTheNewEvent" type="select" label="calendrier" name="calendar_id"
            validation="required">
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
.is-display-none {
  display: none !important;
}

.calendar__no-events {
  position: absolute;
  z-index: -1;
  width: 100%;
  text-align: center;
}

.calendar {
  margin: 2rem;
  position: relative;
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

.calendar__days--list,
.calendar__days--day {
  grid-template-columns: 1fr;
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

.popup--edit-calendar {
  flex-direction: column;
}

.popup--edit-calendar :deep(.formkit-form) {
  flex-direction: row;
}

.popup__event {
  width: 100%;
  margin: 0 1rem;
}
</style>