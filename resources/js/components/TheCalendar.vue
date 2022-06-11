
<script setup>
import { ref, computed, toRaw, watch, onMounted } from "vue";
import useFetch from "../composables/useFetch";
import * as useDate from "../composables/useDate";
import { API } from "../stores/api";
import useSwipe from "../composables/useSwipe";
import useLog from "../composables/useLog";

useSwipe({
  onSwipeLeft: () => {
    nextPeriod();
  },
  onSwipeRight: () => {
    previousPeriod();
  },
});

// Constants
// ======================================

const TODAY = new Date();
const DAY_LABELS = ["L", "M", "M", "J", "V", "S", "D"];
const DAY_LABELS_SHORT = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];
const MONTH_LABELS = ["JANVIER", "FEVRIER", "MARS", "AVRIL", "MAI", "JUIN", "JUILLET", "AOUT", "SEPTEMBRE", "OCTOBRE", "NOVEMBRE", "DECEMBRE",];
const DATE_OPTION = ["fr-ch", { year: "numeric", month: "long" }];
const AVAILABLE_LAYOUT = { MONTH: 0, WEEK: 1, LIST: 3, DAY: 4 };
const AVAILABLE_POPUP = {
  STORE_EVENT: 0,
  MONTH_EVENTS: 1,
  CALENDAR_OPTIONS: 2,
  FILTER: 3,
  EVENT: 5,
  EDIT_EVENT: 6,
};
const AVAILABLE_CALENDAR_POPUP = {
  STORE: 0,
  EDIT: 1,
  SHARE: 2,
};
const EVENT_POPUP = 0
const eventPopup = ref(null)

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
const currentPopup = ref(null);
const users = ref([]);
const userSearch = ref("");
const searchedUser = ref([]);
const usersForm = ref({});
const newEventStart = ref("08:00");
const selectedEvent = ref(null);
const currentCalendarPopupOption = ref(AVAILABLE_CALENDAR_POPUP.STORE);

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
        weekOfYear: useDate.getWeekYearNumber(dateStart),
      };
    } else {
      displayedDate.value = {
        year1: dateStart.getFullYear(),
        month1: MONTH_LABELS[dateStart.getMonth()],
        day1: dateStart.getDate(),
        year2: dateEnd.getFullYear(),
        month2: MONTH_LABELS[dateEnd.getMonth()],
        day2: dateEnd.getDate(),
        weekOfYear: useDate.getWeekYearNumber(dateStart),
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
    return parseInt(localStorage.getItem("layout"));
  },
  set(layoutId) {
    localStorage.setItem("layout", layoutId);
    currentLayout.value = parseInt(layoutId);
  },
});

const selectedCalendarsIdStorage = computed({
  get() {
    return JSON.parse(localStorage.getItem("calendars"));
  },
  set(calendarsIds) {
    localStorage.setItem("calendars", JSON.stringify(calendarsIds));
  },
});

const getCurrentDateForForm = computed(() => {
  const dateParts = TODAY.toLocaleDateString().split("/");
  const date = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
  return date.replace(/\//g, "-");
});


// Helpers
// ======================================

function getKeyByValue(object, value) {
  return Object.keys(object).find((key) => object[key] === value);
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
  delete rawForm.start_date;
  return rawForm;
}

function showNewEventForm(event) {
  event.stopPropagation();
  currentPopup.value =
    currentPopup.value === AVAILABLE_POPUP.STORE_EVENT
      ? null
      : AVAILABLE_POPUP.STORE_EVENT;
}

function showShareCalendarForm(event) {
  event.stopPropagation();
  currentPopup.value =
    currentPopup.value === AVAILABLE_POPUP.SHARE_CALENDAR
      ? null
      : AVAILABLE_POPUP.SHARE_CALENDAR;
}

function showNewCalendarForm(event) {
  event.stopPropagation();
  currentPopup.value =
    currentPopup.value === AVAILABLE_POPUP.STORE_CALENDAR
      ? null
      : AVAILABLE_POPUP.STORE_CALENDAR;
}

function showEditCalendarForm(event) {
  event.stopPropagation();
  currentPopup.value =
    currentPopup.value === AVAILABLE_POPUP.EDIT_CALENDAR
      ? null
      : AVAILABLE_POPUP.EDIT_CALENDAR;
}

function formatCurrentDateForDisplay(date, nextDays = 0) {
  const date2 = new Date(
    date.getFullYear(),
    date.getMonth(),
    date.getDate() + nextDays
  );
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
        name: form.name,
      };
      allCalendars.value.push(newCalendar);
      const userCalendars = {};
      allCalendars.value.forEach((calendar) => {
        userCalendars[calendar.id] = calendar.name;
      });
      calendarsNames.value = userCalendars;
      currentsCalendarIds.value.push(newId);
      selectedCalendarsIdStorage.value = toRaw(currentsCalendarIds.value);
    } catch (error) {
      console.log(error);
    }
  } else {
    useLog('storeCalendar: Request failed with status code ' + response.status, 'error');
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
    useLog('storeEvent: Request failed with status code ' + response.status, 'error');
  }
}

async function deleteEvent(dayId, eventId, calendarId) {
  eventPopup.value = null;
  const response = await useFetch({
    url: API.deleteEvent.path(eventId),
    method: API.deleteEvent.method,
  });
  if (response.success === true) {
    try {
      const date = useDate.toSwissDate(dayId);
      const filtredEvents = events.value[date].filter(
        (event) => event.id !== eventId
      );
      events.value[date] = filtredEvents;
      allCalendars.value.forEach((calendar) => {
        if (calendar.id === calendarId) {
          calendar.events = filtredEvents;
        }
      });
      const newEvents = sortEventsByDate(date);
      newEventPopup.value = newEvents;
    } catch (error) {
      console.log(error);
    }
  } else {
    useLog('deleteEvent: Request failed with status code ' + response.status, 'error');
  }
}

async function updateEvent(form) {
  useLog('updateEvent', 'success');
  const date = form.start_date;
  form.start_date = useDate.swissDateToYMD(form.start_date, "-");
  form.end_date = useDate.swissDateToYMD(form.start_date, "-");
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
      currentPopup.value = AVAILABLE_POPUP.MONTH_EVENTS;
      eventPopup.value = null;
    } catch (error) {
      console.log(error);
    }
  } else {
    useLog('updateEvent: Request failed with status code ' + response.status, 'error');
  }
}

async function getCalendars() {
  const response = await useFetch({
    url: API.getEvents.path(),
    method: API.getEvents.method,
  });
  if (response.success === true) {
    console.log("calendars", response.data);
    return response.data;
  } else {
    useLog('getCalendars: Request failed with status code ' + response.status, 'error');
    return [];
  }
}

async function setCalendars(calendars, setIds = true) {
  const userCalendars = {};
  try {
    calendars.forEach((calendar) => {
      userCalendars[calendar.id] = calendar.name;
    });
    calendarsNames.value = userCalendars;
    allCalendars.value = calendars;
    if (setIds) {
      const storageValue = localStorage.getItem("calendars");
      currentsCalendarIds.value =
        storageValue && typeof JSON.parse(storageValue) == "object"
          ? JSON.parse(storageValue)
          : [calendars[0].id.toString()];
    }
  } catch (error) {
    useLog('setCalendars: Request failed with status code ' + response.status, 'error');
  }
}

async function deleteCalendar(calendarId) {
  const response = await useFetch({
    url: API.deleteCalendar.path(calendarId),
    method: API.deleteCalendar.method,
  });
  if (response.success === true) {
    try {
      const calendars = allCalendars.value.filter(
        (calendar) => calendar.id !== calendarId
      );
      const newIds = calendars.map((calendar) => calendar.id);
      selectedCalendarsIdStorage.value = newIds;
      currentsCalendarIds.value = newIds;
      setCalendars(calendars, false);
    } catch (error) {
      console.log(error);
    }
  } else {
    useLog('deleteCalendar: Request failed with status code ' + response.status, 'error');
  }
}

async function updateCalendar(form) {
  const response = await useFetch({
    url: API.updateCalendar.path(form.calendar_id),
    method: API.updateCalendar.method,
    data: {
      name: form.name,
    },
  });
  if (response.success === true) {
    try {
      const calendars = allCalendars.value.map((calendar) => {
        if (calendar.id == form.calendar_id) {
          calendar.name = form.name;
        }
        return calendar;
      });
      setCalendars(calendars, false);
    } catch (error) {
      console.log(error);
    }
  } else {
    useLog('updateCalendar: Request failed with status code ' + response.status, 'error');
  }
}

async function getAllUsers() {
  const response = await useFetch({
    url: API.getClassrooms.path(),
    method: API.getClassrooms.method,
  });
  if (response.success === true) {
    return response.data.classrooms;
  } else {
    console.log(response, "error: no users");
    return [];
  }
}

async function setAllUsers() {
  const classrooms = await getAllUsers();
  const allUsers = classrooms.map((classroom) => {
    return classroom.users.map((user) => {
      return user;
    });
  });
  users.value = [...allUsers].flat();
}

async function shareCalendar(form) {
  console.log(form);
  const response = await useFetch({
    url: API.shareCalendar.path(form.calendar_id),
    method: API.shareCalendar.method,
    data: { form },
  });
  if (response.success === true) {
  } else {
    useLog('shareCalendar: Request failed with status code ' + response.status, 'error');
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
      const ids = Object.keys(currentsCalendarIds.value).map(function (key) {
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
          calendar.events.push(event);
        }
      });
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
    previousPeriod = new Date(
      useDate.getMonthRelativeToDate(dateUnderCursor, -1)
    );
    dates.value = useDate.getAllDaysInMonthAndBeginning(
      previousPeriod.getFullYear(),
      previousPeriod.getMonth()
    );
    formatCurrentDateForDisplay(previousPeriod);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    previousPeriod = new Date(
      useDate.getDaysRelativeToDate(dateUnderCursor, -7)
    );
    const monday = useDate.getMonday(previousPeriod);
    dates.value = useDate.getAllDaysInWeek(previousPeriod);
    formatCurrentDateForDisplay(monday, 7);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.LIST) {
    previousPeriod = new Date(
      useDate.getDaysRelativeToDate(dateUnderCursor, -30)
    );
    dates.value = useDate.getDaysFromDate(previousPeriod);
    formatCurrentDateForDisplay(previousPeriod, 30);
  } else if (currentLayout.value === AVAILABLE_LAYOUT.DAY) {
    previousPeriod = new Date(
      useDate.getDaysRelativeToDate(dateUnderCursor, -1)
    );
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
    currentPopup.value = null;
  } else {
    selectedDate.value = dayIndex;
    currentEventPopupIndex.value = index;
    newEventPopup.value = sortEventsByDate(index);
    currentPopup.value = AVAILABLE_POPUP.MONTH_EVENTS;
  }
}

function showEventEditForm(startDate, id) {
  const date = useDate.toSwissDate(startDate);
  /* eventPopup.value = null */
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
    id: id,
  };
}

// At startup
// ======================================

(async function startUp() {
  displayedDateManager.value = { dateStart: TODAY };
  currentLayout.value = layoutStorage.value || AVAILABLE_LAYOUT.MONTH;
  const calendars = await getCalendars();
  await setCalendars(calendars);
  await setAllUsers();
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
    selectedCalendarsIdStorage.value = toRaw(currentsCalendarIds.value);
    console.log(toRaw(selectedCalendarsIdStorage.value));
    setEvents(getEvents());
  });

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
      formatCurrentDateForDisplay(monday);
    } else if (currentLayout.value === AVAILABLE_LAYOUT.LIST) {
      dates.value = useDate.getDaysFromDate(currDateCursor.value);
      formatCurrentDateForDisplay(currDateCursor.value, 30);
    } else if (currentLayout.value === AVAILABLE_LAYOUT.DAY) {
      dates.value = useDate.getDaysFromDate(currDateCursor.value, 1);
      formatCurrentDateForDisplay(currDateCursor.value);
    }
  });

  watch(userSearch, () => {
    const resultOfsearch = [];
    if (userSearch.value != "") {
      users.value.forEach((user) => {
        if (
          user.username
            .toLowerCase()
            .includes(toRaw(userSearch.value.toLowerCase()))
        ) {
          resultOfsearch.push(user.username);
        }
      });
    }
    searchedUser.value = resultOfsearch;
  });
})();
</script>
<template>
  <div class="calendar">

    <!--==================================-->
    <!-- Calendar Header -->
    <!--==================================-->

    <div class="calendar__wrapper-date" v-if="currentLayout === AVAILABLE_LAYOUT.MONTH">
      <h1 class="calendar__date">
        <span>{{ displayedDateManager.year1 }}</span>
        <span>{{ displayedDateManager.day1 }}
          {{ displayedDateManager.month1 }}</span>
      </h1>
    </div>

    <div class="calendar__wrapper-date" v-if="currentLayout === AVAILABLE_LAYOUT.WEEK">
      <h1 class="calendar__date">
        <span>{{ displayedDateManager.year1 }}</span>
        <p>
          {{ displayedDateManager.day1 }} {{ displayedDateManager.month1 }}
          {{ displayedDateManager.year1 }}
          <span v-show="displayedDateManager.day2"> - </span>{{ displayedDateManager.day2 }} {{
              displayedDateManager.month2
          }}
          {{ displayedDateManager.year2 }}
        </p>
      </h1>
    </div>

    <div class="calendar__wrapper-date" v-if="currentLayout === AVAILABLE_LAYOUT.DAY">
      <h1 class="calendar__date">
        <span>{{ displayedDateManager.year1 }}</span>
        <span>{{ displayedDateManager.day1 }}
          {{ displayedDateManager.month1 }}</span>
      </h1>
    </div>

    <div class="calendar__wrapper-date" v-if="currentLayout === AVAILABLE_LAYOUT.LIST">
      <h1 class="calendar__date">{{ displayedDateManager.year1 }}</h1>
      <p>
        {{ displayedDateManager.day1 }} {{ displayedDateManager.month1 }}
        {{ displayedDateManager.year1 }}
        <span v-show="displayedDateManager.day2"> - </span>{{ displayedDateManager.day2 }} {{
            displayedDateManager.month2
        }}
        {{ displayedDateManager.year2 }}
      </p>
    </div>

    <!--====  calendar navigations  ====-->
    <header class="calendar__header">
      <button @click="currentPopup = AVAILABLE_POPUP.FILTER"><span class="material-icons">filter_alt</span></button>
      <button @click="actualPeriod"><span class="material-icons">today</span></button>
      <button @click="showNewEventForm"><span class="material-icons">add_circle_outline</span></button>
      <button @click="currentPopup = AVAILABLE_POPUP.CALENDAR_OPTIONS"><span
          class="material-icons">edit_calendar</span></button>
    </header>
    <!--====  Calendar days names  ====-->
    <div class="calendar__days-names">
      <div v-show="currentLayout === AVAILABLE_LAYOUT.MONTH" v-for="dayLabel in dayLabels" class="calendar__days-name">
        {{ dayLabel }}
      </div>
    </div>
    <!--====  Calendar no events message  ====-->
    <div class="calendar__no-events" v-show="currentLayout === AVAILABLE_LAYOUT.LIST">
      <p>Aucun événements <br /> pour cette période</p>
    </div>

    <!--==================================-->
    <!-- Calendar layout -->
    <!--==================================-->

    <!--====  Calendar days MONTH  ====-->
    <div v-if="currentLayout === AVAILABLE_LAYOUT.MONTH" class="calendar__days" :class="
      'calendar__days--' +
      getKeyByValue(AVAILABLE_LAYOUT, currentLayout).toLocaleLowerCase()
    ">
      <div v-for="(day, index) in dates" class="calendar__day" @click="showCurrentEvent(day?.local, index)" :class="
        selectedDate === index ? 'is-selected-day' : '',
        day?.class,
        currentLayout === AVAILABLE_LAYOUT.MONTH &&
          MONTH_LABELS[day?.monthNumber] !== displayedDateManager.month1
          ? 'is-other-month'
          : ''
      " :key="index" :date-id="day?.local">
        <p class="calendar__day-number" :class="events[day?.local]?.length >= 1 ? 'is-events' : ''">
          {{ day?.dayOfMonthNumber }}
        </p>
        <div class="calendar__dotes">
          <div v-show="eventId < 5" v-for="(event, eventId) in sortEventsByDate(day?.local)" class="calendar__dot">
          </div>
        </div>
      </div>
    </div>
    <!--====  Calendar days WEEK  ====-->
    <div v-if="currentLayout === AVAILABLE_LAYOUT.WEEK" class="calendar__days" :class="
      'calendar__days--' +
      getKeyByValue(AVAILABLE_LAYOUT, currentLayout).toLocaleLowerCase()
    ">
      <div v-for="(day, index) in dates" class="calendar__day" @click="showCurrentEvent(day?.local, index)"
        :class="(selectedDate === index ? 'is-selected-day' : '', day?.class)" :key="index" :date-id="day?.local">
        <div class="calendar__day-header">
          <p class="calendar__day-number" :class="events[day?.local]?.length >= 1 ? 'is-events' : ''">
            <span class="calendar__day-of-month">{{
                day?.dayOfMonthNumber
            }}</span>
            <span class="calendar__day-of-week">{{
                DAY_LABELS_SHORT[day.dayOfWeekNumber]
            }}</span>
          </p>
          <div class="calendar__dotes">
            <div v-show="eventId < 5" v-for="(event, eventId) in sortEventsByDate(day?.local)" class="calendar__dot">
            </div>
          </div>
        </div>
        <div class="calendar__events">
          <div v-for="(event, eventId) in sortEventsByDate(day?.local)" class="calendar__event">
            <p class="event__title">{{ event.title }}</p>
            <p class="event__time">
              {{ useDate.toEventTime(event.start, event.end) }}
            </p>
            <p class="event__location">{{ event.location }}</p>
          </div>
        </div>
      </div>
    </div>
    <!--====  Calendar days LIST  ====-->
    <div v-if="currentLayout === AVAILABLE_LAYOUT.LIST" class="calendar__days" :class="
      'calendar__days--' +
      getKeyByValue(AVAILABLE_LAYOUT, currentLayout).toLocaleLowerCase()
    ">
      <div v-for="(day, index) in dates" class="calendar__day" @click="showCurrentEvent(day?.local, index)" :class="
        (selectedDate === index ? 'is-selected-day' : '',
          day?.class,
          currentLayout === AVAILABLE_LAYOUT.LIST &&
            !events.hasOwnProperty(day?.local)
            ? 'is-display-none'
            : '')
      " :key="index" :date-id="day?.local">
        <article class="calendar__events">
          <div v-for="(event, eventId) in sortEventsByDate(day?.local)" class="calendar__event" :data-date="
            day?.dayOfMonthNumber +
            ' ' +
            DAY_LABELS_SHORT[day?.dayOfWeekNumber]
          ">
            <p class="calendar__event-text">{{ event.title }}</p>
            <p class="calendar__event-text">{{ event.start }}</p>
          </div>
        </article>
      </div>

    </div>
    <!--====  Calendar days DAY  ====-->
    <div v-if="currentLayout === AVAILABLE_LAYOUT.DAY" class="calendar__days" :class="
      'calendar__days--' +
      getKeyByValue(AVAILABLE_LAYOUT, currentLayout).toLocaleLowerCase()
    ">
      <div v-for="(day, index) in dates" class="calendar__day" @click="showCurrentEvent(day?.local, index)"
        :class="(selectedDate === index ? 'is-selected-day' : '', day?.class)" :key="index" :date-id="day?.local">
        <article class="calendar__events">
          <div v-for="(event, eventId ) in sortEventsByDate(day?.local)" class="calendar__event"
            :data-time="useDate.toEventTime(event?.start)" :class="event.calendar_id == 1 ? 'is-heig-calendar' : ''"
            :data-bullet="useDate.toEventTime(events[day?.local][eventId - 1]?.start || ' ') != useDate.toEventTime(event.start) ? 'true' : 'false'">

            <p class="event__header">
              <span class="event__title">{{ event.title }}</span>
              <span class="event__location">{{ event.location }}</span>
              <span v-if="event.calendar_id == 1" class="event__time">{{
                  useDate.toEventTime(event.start, event.end)
              }}</span>
              <span v-if="event.calendar_id !== 1" class="event__time">{{
                  useDate.toEventTime(event.start)
              }}</span>
            </p>
            <p v-show="event.calendar_id !== 1" class="event__description">
              {{ event.description }}
            </p>
            <p v-show="event.calendar_id !== 1" class="event__calendar">
              <span class="material-icons">calendar_month</span><span>{{ calendarsNames[event.calendar_id] }}</span>
            </p>
          </div>
        </article>
      </div>
    </div>
  </div>

  <!--==================================-->
  <!-- Popup new event -->
  <!--==================================-->

  <div class="popup popup--new-event" v-show="currentPopup === AVAILABLE_POPUP.STORE_EVENT">
    <button @click="currentPopup = null" class="popup__close"><span class="material-icons">close</span></button>
    <h2 class="popup__title">
      <span>Ajouter un événement</span>
    </h2>
    <FormKit type="form" v-model="newEventForm" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Enregistrer"
      @submit="storeEvent">
      <FormKit type="text" name="title" validation="required" label="Titre" placeholder="Titre" />
      <FormKit type="text" name="location" validation="required" label="Lieu" placeholder="Lieu" />
      <FormKit type="textarea" name="description" validation="required" label="Description" placeholder="Description..."
        rows="5" />
      <FormKit type="time" name="start" label="Début" v-model="newEventStart" />
      <FormKit type="time" name="end" validation="required" label="Fin" :min="newEventStart" :value="newEventStart" />
      <FormKit name="start_date" type="date" :value="getCurrentDateForForm" label="Date de Début" validation="required"
        :min="getCurrentDateForForm" />
      <FormKit v-model="calendarIdWhereToAddTheNewEvent" type="select" label="calendrier" name="calendar_id"
        validation="required">
        <option v-for="(name, id) in editableCalendarsNames" :value="id">
          {{ name }}
        </option>
      </FormKit>
      <div class="popup__button-wrapper">
        <button class="button button--cancel" @click.prevent="currentPopup = null">Annuler</button>
        <button class="button button--save" type="submit">Enregistrer</button>
      </div>
    </FormKit>
  </div>

  <!--==================================-->
  <!-- Popup Event -->
  <!--==================================-->

  <div class="popup popup--event" v-if="eventPopup === EVENT_POPUP">
    <button @click="eventPopup = null" class="popup__close"><span class="material-icons">close</span></button>
    <h1 class="event__title">
      <span>{{ selectedEvent.title }}</span>
    </h1>
    <div class="event__wrapper">
      <p class="event__date">{{ useDate.toEventDate(selectedEvent.start) }}</p>
      <p class="event__location">Lieu: {{ selectedEvent.location }}</p>
      <p class="event__start">Début {{ useDate.toEventTime(selectedEvent.start) }}</p>
      <p class="event__end">Fin {{ useDate.toEventTime(selectedEvent.end) }}</p>
      <p class="event__calendar">{{ calendarsNames[selectedEvent.calendar_id] }}<span
          class="material-icons">calendar_month</span> </p>
    </div>
    <hr />
    <p class="event__description">{{ selectedEvent.description }}</p>
    <div class="popup__button-wrapper">
      <button class="button button--edit" v-show="selectedEvent.can_edit"
        @click="showEventEditForm(selectedEvent.start, selectedEvent.id, selectedEvent.calendar_id), currentPopup = AVAILABLE_POPUP.EDIT_EVENT">
        Modifier
      </button>
      <button class="button button--delete" v-show="selectedEvent.can_edit"
        @click="deleteEvent(selectedEvent.start, selectedEvent.id, selectedEvent.calendar_id)">
        supprimer
      </button>
    </div>
  </div>

  <!--==================================-->
  <!-- Popup Calendar -->
  <!--==================================-->

  <div class="popup popup--calendar" v-show="currentPopup === AVAILABLE_POPUP.CALENDAR_OPTIONS">
    <button @click="currentPopup = null" class="popup__close"><span class="material-icons">close</span></button>
    <h2 class="popup__subtitle">Options de calendrier</h2>
    <div class="popup__choose">
      <FormKit v-model="currentCalendarPopupOption" type="radio" :options="[
        { label: 'Créer', value: 0 },
        { label: 'Modifier', value: 1 },
        { label: 'Partager', value: 2 },
      ]" :sections-schema="{
  input: {
    attrs: {
      class: { 'material-icons': 'material-icons' }
    },
  }
}" />
    </div>
    <hr />
    <!--====  Popup share Calendar  ====-->
    <div class="popup--share-calendar" v-show="currentCalendarPopupOption == AVAILABLE_CALENDAR_POPUP.SHARE">
      <h1>Partager un Calendrier</h1>
      <FormKit type="search" placeholder="prenom.nom..." label="Search" v-model="userSearch" />
      <FormKit type="form" v-model="usersForm" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Partager"
        @submit="shareCalendar">
        <FormKit type="checkbox" name="users" :options="searchedUser" validation="required"
          v-if="searchedUser.length > 0" />
        <FormKit type="select" label="calendrier" name="calendar_id" :options="calendarsNames" />
        <FormKit type="checkbox" label="Droit de modification" name="can_own" />
      </FormKit>
    </div>
    <!--====  Popup Edit Calendar  ====-->
    <div class="popup--edit-calendar" v-show="currentCalendarPopupOption == AVAILABLE_CALENDAR_POPUP.EDIT">
      <h1 class="popup__title">Editer les calendrier</h1>
      <div v-for="calendar in getCalendarsData">
        <FormKit v-if="calendar.can_edit" type="form" :form-class="isSubmitted ? 'hide' : 'show'"
          submit-label="Enregistrer" @submit="updateCalendar">
          <FormKit v-model="calendarIdWhereToAddTheNewEvent" type="select" label="calendrier" name="calendar_id"
            validation="required" />
          <FormKit type="text" label="Nom" name="name" validation="required" :value="calendar.name" />
          <FormKit type="color" value="#00FF00" label="Couleur" />
          <button class="button" @click="deleteCalendar(calendar.id)">supprimer</button>
        </FormKit>
        <div class="popup__button-wrapper">
          <button class="button button--edit"
            @click="showEventEditForm(selectedEvent.start, selectedEvent.id, selectedEvent.calendar_id), currentPopup = AVAILABLE_POPUP.EDIT_EVENT">
            Modifier
          </button>
          <button class="button button--delete"
            @click="deleteEvent(selectedEvent.start, selectedEvent.id, selectedEvent.calendar_id)">
            supprimer
          </button>
        </div>
      </div>
    </div>
    <!--====  Popup store Calendar  ====-->
    <div class="popup--new-calendar" v-show="currentCalendarPopupOption == AVAILABLE_CALENDAR_POPUP.STORE">
      <h1 class="popup__title">
        <span>Ajouter un Calendrier</span>
      </h1>
      <FormKit type="form" v-model="newCalendarForm" :form-class="isSubmitted ? 'hide' : 'show'"
        submit-label="Enregistrer" @submit="storeCalendar">
        <FormKit type="text" name="name" validation="required" label="Nom" />
        <div class="popup__button-wrapper">
          <button class="button button--cancel" @click.prevent="currentPopup = null">Annuler</button>
          <button class="button button--save" type="submit">Enregistrer</button>
        </div>
      </FormKit>
    </div>
  </div>

  <!--==================================-->
  <!-- Popup Filter -->
  <!--==================================-->

  <div class="popup popup--filter" v-show="currentPopup === AVAILABLE_POPUP.FILTER">
    <button @click="currentPopup = null" class="popup__close"><span class="material-icons">close</span></button>
    <h1 class="popup__title">
      <span>Filtres</span>
    </h1>
    <hr>
    <h3 class="popup__subtitle">Affichage</h3>
    <ul class="popup__layout-options">
      <li class="popup__layout-option">
        <button :class="currentLayout == AVAILABLE_LAYOUT.MONTH ? 'is-selected-layout' : ''"
          class="popup__layout-button" @click="currentLayout = AVAILABLE_LAYOUT.MONTH"><span
            class="material-icons">calendar_month</span><span class="popup__text">Mois</span></button>
      </li>
      <li class="popup__layout-option">
        <button :class="currentLayout == AVAILABLE_LAYOUT.WEEK ? 'is-selected-layout' : ''" class="popup__layout-button"
          @click="currentLayout = AVAILABLE_LAYOUT.WEEK"><span
            class="material-icons is-90-deg">calendar_view_week</span><span class="popup__text">Semaine</span></button>
      </li>
      <li class="popup__layout-option">
        <button :class="currentLayout == AVAILABLE_LAYOUT.LIST ? 'is-selected-layout' : ''" class="popup__layout-button"
          @click="currentLayout = AVAILABLE_LAYOUT.LIST"><span class="material-icons">event_note</span><span
            class="popup__text">List</span></button>
      </li>
      <li class="popup__layout-option">
        <button :class="currentLayout == AVAILABLE_LAYOUT.DAY ? 'is-selected-layout' : ''" class="popup__layout-button"
          @click="currentLayout = AVAILABLE_LAYOUT.DAY"><span class="material-icons">date_range</span><span
            class="popup__text">Jour</span></button>
      </li>
    </ul>
    <hr>
    <h3 class="popup__subtitle">Mes calendriers</h3>
    <div class="calendar__choose">
      <FormKit v-model="currentsCalendarIds" type="checkbox" label="Calendrier" :options="calendarsNames"
        :sections-schema="{
          input: {
            attrs: {
              class: { 'material-icons': 'material-icons' }
            },
          }
        }" />
    </div>
  </div>

  <!--==================================-->
  <!-- Popup month events -->
  <!--==================================-->

  <div class="popup popup--month-events" v-show="currentLayout === AVAILABLE_LAYOUT.MONTH">
    <h2 class="popup__title">
      <p>A venir</p>
      <p>{{ displayedDateManager.day1 }} {{ displayedDateManager.month1 }} {{ displayedDateManager.year1 }}</p>
    </h2>
    {{ currDateCursor }}
    <div class="popup__events">
      <article class="popup__event" v-for="(event, index) in newEventPopup"
        @click="eventPopup = EVENT_POPUP; selectedEvent = event">
        <div class="event">
          <div class="event__infos">
            <p class="event__dot"></p>
            <div class="event__wrapper">
              <p class="event__title">{{ event.title }}</p>
              <p class="event__date">{{ useDate.toEventDate(event.start) }}</p>
            </div>
            <p class="event__time">{{ useDate.toEventTime(event.start) }}</p>
          </div>
        </div>
      </article>
    </div>
  </div>

  <!--==================================-->
  <!-- Popup edit event -->
  <!--==================================-->

  <div class="popup popup--edit-event" v-if="currentPopup === AVAILABLE_POPUP.EDIT_EVENT">
    <button @click="currentPopup = null" class="popup__close"><span class="material-icons">close</span></button>
    <h1 class="event__title">
      <span>Modifier un événement</span>
    </h1>
    <article class="popup__event">
      <FormKit type="form" v-model="formUpdate" submit-label="Enregistrer" @submit="updateEvent">
        <FormKit type="text" name="title" validation="required" label="Titre" />
        <FormKit type="text" name="location" validation="required" label="Lieu" />
        <FormKit type="textarea" name="description" validation="required" label="Description" />
        <FormKit type="time" name="start" label="Début" />
        <FormKit type="time" name="end" label="Fin" />
        <FormKit name="end_date" type="hidden" :value="formUpdate.start" />
        <FormKit name="start_date" type="hidden" :value="formUpdate.start" />
        <FormKit name="id" type="hidden" :value="formUpdate.id" />
        <FormKit v-model="calendarIdWhereToAddTheNewEvent" type="select" label="calendrier" name="calendar_id"
          validation="required">
          <option v-for="(name, id) in editableCalendarsNames" :value="id">
            {{ name }}
          </option>
        </FormKit>
        <div class="popup__button-wrapper">
          <button class="button button--cancel">
            Annuler
          </button>
          <button class="button button--edit" type="submit">
            Modifier
          </button>
        </div>
      </FormKit>
    </article>
  </div>

</template>
<style lang="scss" scoped>
// ==========================================================================
//*1# Abstracts
// ==========================================================================
$dot-size: 4px;
$calendar-gap: 2rem;
$border-radius: 6px;
$border-width: 3px;
$heig-planning-color: var(--information-color);
$header-height: 60px;
$footer-height: 50px;
$date-wrapper-height: 6rem;
$navigation-height: 3rem;
@import '../../sass/abstracts/mixins';

:global(.main--calendar) {
  display: grid;
  grid-template-rows: 1fr 0.82fr;
  grid-auto-flow: column;
  height: 100%;
}

@mixin day-number-month-layout {
  font-family: "Poppins";
  font-style: normal;
  font-weight: 600;
  font-size: 1.8rem;
  line-height: 2.7rem;
  text-transform: uppercase;
  color: var(--text-color);
}

// ==========================================================================
//*1# Calendar
// ==========================================================================

.calendar {
  position: relative;
  margin: 0 var(--column-gap);
}

:deep(.formkit-label) {
  @include font-title-subject(var(--text-color), left);
}

.popup__button-wrapper {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  grid-auto-flow: column;
  grid-gap: $border-radius;
  width: calc(100% - 2rem);
  padding: 0 1rem;
  margin: 0 0 2.4rem 0;
  position: absolute;
  justify-content: center;
  bottom: 0;
  box-sizing: border-box;
}

.button {
  @include button;
  width: 100%;
  margin: 0 auto;
  max-width: 16rem;
}

.button--cancel {
  grid-column: 1 / 4;
}

.button--edit {
  grid-column: 5 / 8;
}

.button--delete {
  grid-column: 1 / 4;
}

.button--save {
  grid-column: 5 /8;
}

hr {
  border-top: 1px solid var(--primary-color);
  width: 100%;
}

:deep(.formkit-input) {
  width: 100%;
  background-color: var(--information-color);
  border: none;
  border-radius: $border-radius;
  min-height: 4.1rem;
}

:deep(.formkit-input[type="submit"]) {
  display: none;
}

:deep(.formkit-outer) {
  width: 100%;
}

:deep(.formkit-form) {
  width: 100%;
}

//*2# Calendar date
// ==========================================================================

.calendar__date {
  display: flex;
  justify-content: space-between;
  margin: 0;
  align-items: center;
  height: $date-wrapper-height;
  @include font-h1(var(--text-color), unset)
}

//*2# Calendar Header
// ==========================================================================

.calendar__header {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  grid-gap: $calendar-gap;
  height: $navigation-height;

  button {
    border: none;
    background: none;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--accent-color);
  }

  button:nth-child(1) {
    grid-column: 1 / 2;
  }

  button:nth-child(2) {
    grid-column: 3 / 4;
  }

  button:nth-child(3) {
    grid-column: 5 / 6;
  }

  button:nth-child(4) {
    grid-column: 7 / 8;
  }
}

//*2# Calendar days
// ==========================================================================

.calendar__days-names {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  @include font-h3-uppercase(var(--text-color), center);
  grid-gap: $calendar-gap;
  margin: 3rem 0;
}

.calendar__days-name {
  text-align: center;
}

.calendar__day {
  max-width: 100%;
  position: relative;
  align-items: center;
  border: 1px solid #ccc;
  display: flex;
  flex-direction: column;
  font-size: 1.5rem;
  background-color: transpartent;
  color: black;
}

.calendar__dotes {
  display: grid;
  margin: 0 auto;
  grid-auto-flow: column;
  grid-gap: $dot-size;
}

.calendar__dot {
  background-color: red;
  width: $dot-size;
  height: $dot-size;
  border-radius: 50%;
}

//*2# Calendar days MONTH
// ==========================================================================

.calendar__days--month {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  grid-gap: $calendar-gap;

  .calendar__day {
    border: none;
  }

  .calendar__day-number {
    width: 2.8rem;
    height: 2.8rem;
    margin: 0 0 0.5rem 0;
    border: 3px solid transparent;
    border-radius: 50%;
    @include font-text-calendar(var(--text-color), center);
  }

  .is-events {
    background-color: red;
    border-radius: 50%;
    color: white;
  }

  .is-selected-day .calendar__day-number {
    border: 3px solid var(--text-color) !important;
  }
}

//*2# Calendar days WEEK
// ==========================================================================

.calendar__days--week {
  display: grid;
  grid-template-columns: 1fr;
  grid-gap: 0;

  .calendar__day {
    display: grid;
    grid-template-columns: 1fr 6fr;
    grid-auto-flow: column;
    grid-gap: $calendar-gap;
    padding: 1rem;
    border: none;
    border-bottom: 1px solid var(--primary-color);
    min-height: 7.5rem;
  }

  .calendar__day-number {
    display: flex;
    flex-direction: column;
  }

  .calendar__events {
    display: flex;
    grid-column: 2/3;
    overflow-y: scroll;
  }

  .calendar__day-header {
    grid-column: 1 /2;
    display: flex;
    flex-direction: column;
  }

  .calendar__day-of-month {
    color: var(--primary-color);
  }

  .calendar__dotes {
    margin: 0 auto 0 0;
  }

  .calendar__event {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 100px;
    background: var(--information-color);
    border-radius: $border-radius;
    margin: 0 $calendar-gap 0 0;
  }
}

//*2# Calendar days LIST
// ==========================================================================

.calendar__days--list {
  grid-template-columns: 1fr;
  overflow: scroll;
  /* height: calc(100vh - calendarHeaderSize); */

  .calendar__day {
    flex-direction: row;
    border: none;
    position: relative;
    text-align: center;
    margin: 0 0 0 6rem;
  }

  .calendar__day-number {
    width: 4rem;
    display: flex;
    flex-direction: column;
    position: absolute;
    left: -6rem;
  }

  .calendar__events {
    width: 100%;
    border-left: $border-width solid var(--text-color);
  }

  .calendar__event {
    display: flex;
    justify-content: space-between;
    padding: 1rem;
    margin: 1rem 0 0 3.8rem;
    background-color: red;
    color: white;
    border-radius: $border-radius;
    text-overflow: ellipsis;
    position: relative;
  }

  .calendar__event:first-child::before {
    content: attr(data-date);
    position: absolute;
    left: -10rem;
    width: 36px;
    top: 2rem;
    height: 30px;
    display: flex;
    align-items: center;
    @include font-calendar-list-date(var(--text-color), center);
  }

  .calendar__event:first-child::after {
    $size: 1.6rem;
    content: "";
    position: absolute;
    width: $size;
    height: $size;
    border-radius: 50%;
    background-color: var(--text-color);
    top: calc(50% - ($size / 2));
    left: calc((3.8rem * -1) - ($size/2) - 1px);
  }

  .calendar__event-text {
    @include font-calendar-month-name-time-event(white, unset)
  }

  .is-current-day {
    background-color: unset;
  }
}

//*2# Calendar days DAY
// ==========================================================================

.calendar__days--day {
  grid-template-columns: 1fr;

  .calendar__day {
    flex-direction: row;
    border: none;
    position: relative;
    text-align: center;
    margin: 0 0 0 6rem;
  }

  .calendar__day-number {
    width: 4rem;
    display: flex;
    flex-direction: column;
    position: absolute;
    left: -6rem;
  }

  .calendar__events {
    width: 100%;
    border-left: $border-width solid var(--text-color);
  }

  .calendar__event {
    display: flex;
    color: var(--text-color);
    flex-direction: column;
    justify-content: space-between;
    padding: 1rem;
    margin: 1rem 0 0 3.8rem;
    background-color: red;
    border-radius: $border-radius;
    text-overflow: ellipsis;
    position: relative;
  }

  .calendar__event:not(.is-heig-calendar) {
    color: white;
  }

  .calendar__event[data-bullet="true"]::before {
    $height: 30px;
    content: attr(data-time);
    position: absolute;
    left: -10rem;
    width: 36px;
    top: calc(50% - ($height / 2));
    height: $height;
    display: flex;
    align-items: center;
    @include font-calendar-month-name-time-event(var(--text-color), center);
  }

  .calendar__event[data-bullet="true"]::after {
    $size: 1.6rem;
    content: "";
    position: absolute;
    width: $size;
    height: $size;
    border-radius: 50%;
    background-color: var(--text-color);
    top: calc(50% - ($size / 2));
    left: calc((3.8rem * -1) - ($size/2) - 1px);
  }

  .event__header {
    display: grid;
    width: 100%;
    grid-template-columns: repeat(5, 1fr);
    @include font-calendar-month-name-time-event(var(--text-color), unset)
  }

  .event__title {
    grid-column: 1 /3;
  }

  .event__location {
    grid-column: 3 / 4;
  }

  .event__description {
    @include font-calendar-day-description-event(white, left)
  }

  .event__time {
    grid-column: 4 / 6;
    width: fit-content;
    margin: 0 0 0 auto;
  }

  .event__calendar {
    margin: 0 0 0 auto;
    display: flex;
    align-items: center;
    @include font-calendar-day-description-event(white, right)
  }

  .is-current-day {
    background-color: unset;
  }

  .is-heig-calendar {
    background-color: $heig-planning-color;
  }
}

//*2# Calendar day
// ==========================================================================

.calendar__day-date {
  font-size: 0.8rem;
  text-align: left;
  padding: 0.5rem;
  opacity: 0.5;
}

//*2# Events
// ==========================================================================

.calendar__no-events {
  position: absolute;
  z-index: -1;
  width: 100%;
  text-align: center;
}

.event__infos {
  display: flex;
  padding: 0.5rem;
}

.event__wrapper {
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.event__title,
.event__date {
  margin: 0.2rem 0;
}

.event__time {
  background-color: red;
  height: 1.8rem;
  border-radius: 16px;
  color: white;
  padding: 0 0.5rem;
  margin: auto 0;
  text-align: center;
}

.event__dot {
  width: $dot-size;
  height: $dot-size;
  background-color: red;
  border-radius: 50%;
  margin: auto 0.5rem;
}

// ==========================================================================
//*1# Popup
// ==========================================================================

.popup {
  width: 100%;
  height: 100vh;
  padding: 1rem;
  box-sizing: border-box;
  position: absolute;
  background-color: white;
  z-index: 200;
  top: 0;
  display: flex;
  flex-direction: column;
}

.popup__close {
  color: var(--accent-color);
  background-color: transparent;
  border: none;
  margin: 0 0 0 auto;
}


.popup__title {
  width: 100%;
  box-sizing: border-box;
  @include font-h1(var(--text-color), left)
}

//*2# Popup Event
// ==========================================================================

.popup--event {

  .event__wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: $calendar-gap ;
  }

  p {
    margin: 1rem 0;
  }

  .event__title {
    @include font-h1(var(--accent-color), left);
    margin: 1rem 0 0 0;
  }

  .event__start {
    @include font-title-subject(var(--text-color), left)
  }

  .event__end {
    @include font-title-subject(var(--text-color), right)
  }

  .event__date {
    @include font-title-subject(var(--text-color), left)
  }

  .event__location {
    @include font-title-subject(var(--text-color), right)
  }

  .event__description {
    @include font-title-subject(var(--text-color), left)
  }

  .event__calendar {
    @include font-title-subject(var(--accent-color), left);
    display: flex;
    align-items: center;
  }

  .event__calendar span {
    font-size: 2rem;
    width: 2rem rem;
    height: 2rem;
  }
}

//*2# Popup Filter
// ==========================================================================

.popup--filter {

  .popup__subtitle {
    @include font-h1-menu-burger(var(--text-color), center);
    margin: 0 auto 3rem auto;
  }

  .popup__layout-options {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 40%;
    margin: 0 auto;
    padding: 0;
  }

  :deep(.formkit-fieldset) {
    width: 100%;
  }

  .popup__layout-option {
    list-style: none;
    margin: 0 0 1rem 0;
  }

  .popup__layout-button {
    width: 100%;
    display: flex;
    align-items: center;
    background-color: transparent;
    border: none;
    @include font-text-calendar-content(var(--text-color), center);
  }

  hr {
    border-top: 1px solid var(--text-color);
    width: 100%;
    margin: 3rem 0;
  }

  .material-icons {
    padding: 0 0.5rem 0;
  }

  .is-90-deg {
    transform: rotate(90deg);
  }

  .is-selected-layout {
    border: none;
    border-radius: $border-radius;
    background-color: var(--accent-color);
    color: white !important;
  }
}

//*3# calendar choose
// ====================================== 

.calendar__choose {
  display: flex;
  width: 40%;
  margin: 0 auto;
  position: relative;
  right: -2.5rem;
  top: -2.5rem;

  :deep(.formkit-options) {
    list-style: none;
  }

  :deep(.formkit-input) {
    width: 0;
  }

  :deep(.formkit-input) {
    width: 0;
  }

  :deep(.formkit-wrapper) {
    position: relative;
  }

  :deep(input[type="checkbox"]) {
    width: 0
  }

  :deep(.formkit-legend) {
    display: none;
  }

  :deep(.formkit-fieldset) {
    border: none;
  }

  :deep(.formkit-options) {
    padding: 0;
    margin: 0;
  }

  :deep(.formkit-label) {
    @include font-text-calendar-content(var(--text-color), left);
  }

  :deep(.material-icons::before) {
    content: "check_box_outline_blank";
    font-size: 2rem;
    width: 2rem;
    height: 2rem;
    position: absolute;
    top: 2.4rem;
    left: -2.4rem;
  }

  :deep(.material-icons:checked::before) {
    content: "check_box";
  }
}

//*2# Popup month events
// ==========================================================================
.popup--month-events {
  overflow: hidden;
  background-color: var(--information-color);
  position: relative;
  height: unset;
  z-index: 1;
  padding: 1rem;

  .popup__title {
    display: flex;
    justify-content: space-between;
    text-align: left;
    padding: 1rem;
    margin: 0;
  }

  .popup__title p {
    margin: 0;
  }

  .popup__events {
    overflow-y: scroll;
    width: 100%;
    padding: 0 0 calc(var(--app-footer-height) + 0.5rem) 0;
  }

  :deep(.formkit-form) {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  :deep(.formkit-option .formkit-wrapper) {
    display: grid;
    grid-template-columns: auto 1fr;
  }

  .event__title {
    @include font-calendar-month-name-time-event(var(--text-color), left);
  }

  .event__date {
    @include font-calendar-month-date-event(var(--inactive-color), left);
  }

  .event__time {
    @include font-calendar-month-name-time-event(white, center);
    padding: 0.5rem;
    width: 5rem;
  }

  .calendar__event {
    font-size: 0.7rem;
    text-align: left;
  }

  .popup--share-calendar {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .popup__event {
    display: grid;
    width: 100%;
    grid-template-columns: 1fr;
    grid-gap: $calendar-gap;
    margin: 0 0 1rem 0;
  }

  .event {
    margin: 0 1rem;
    background-color: white;
    border-radius: 6px;
  }
}

//*2# Popup edit event
// ==========================================================================

.popup--edit-event {

  .event__title {
    @include font-h1(var(--text-color), left);
  }
}

//*2# Popup add event
// ==========================================================================
.popup--new-event {

  :deep(.formkit-input[type="submit"]) {
    display: none;
  }
}

// ==========================================================================
//*1# Calendar popup options
// ==========================================================================

.popup--calendar {

  .popup__subtitle {
    @include font-h2(var(--text-color), left);
  }

  :deep(.formkit-fieldset) {
    border: none;
  }

  :deep(.popup__choose .formkit-wrapper) {
    display: flex;
  }

  :deep(.popup__choose .formkit-inner) {
    padding: 0 1rem;
  }

  :deep(.popup__choose .formkit-input) {
    width: 2.4rem;
  }

  :deep(.formkit-options) {
    list-style: none;
    display: flex;
    justify-content: space-between;
    padding: 0;
  }

  :deep(.formkit-wrapper) {
    align-items: center;
  }

  :deep(input[type="radio"]) {
    width: 0;
    position: relative;
  }

  :deep(fieldset .material-icons::before) {
    content: "radio_button_unchecked";
    font-size: 2.4rem;
    width: 2.4rem;
    height: 2.4rem;
    position: absolute;
    top: -0.5rem;
    left: -2rem;
    color: var(--accent-color);
  }

  :deep(fieldset .material-icons:checked::before) {
    content: "radio_button_checked";
  }
}

// ==========================================================================
//*1# Utility
// ==========================================================================

.is-display-none {
  display: none !important;
}

.is-display-none {
  display: none !important;
}

.is-clickable-day {
  cursor: pointer;
  pointer-events: all;
  background-color: white;
}

.is-current-day {
  cursor: pointer;
  pointer-events: all;
  background-color: rgba(var(--rgb-inactive-color), 0.1);
}

.is-other-month {
  opacity: 0.5;
}

.is-selected-day {}
</style>