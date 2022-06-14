<script setup>
import { ref, computed, toRaw, watch, onMounted } from "vue";
import useFetch from "../composables/useFetch";
import * as useDate from "../composables/useDate";
import { API } from "../stores/api";
import useSwipe from "../composables/useSwipe";
import useLog from "../composables/useLog";
import { useLoading } from "../composables/useLoading";
import { usePopup } from "../composables/usePopup";
import theEmptyPage from "./TheEmptyPage";
import { reset } from '@formkit/core'
import useToast from "../composables/useToast";

onMounted(() => {
  useSwipe({
    element: document.querySelector(".main--calendar"),
    onSwipeLeft: () => {
      nextPeriod();
    },
    onSwipeRight: () => {
      previousPeriod();
    },
  });
});

function waitingForData() {
  useLoading({
    waitFor: async () => {
      await useFetch({
        url: API.updateAllGaps.path(),
        method: API.updateAllGaps.method,
      });
    },
    then: async () => {
      location.reload();
    },
  });
}

// Constants
// ======================================

const TODAY = new Date();
const DAY_LABELS = ["L", "M", "M", "J", "V", "S", "D"];
const DAY_LABELS_SHORT = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];
const MONTH_LABELS = [
  "JANVIER",
  "FEVRIER",
  "MARS",
  "AVRIL",
  "MAI",
  "JUIN",
  "JUILLET",
  "AOUT",
  "SEPTEMBRE",
  "OCTOBRE",
  "NOVEMBRE",
  "DECEMBRE",
];
const DATE_OPTION = ["fr-ch", { year: "numeric", month: "long" }];
const AVAILABLE_LAYOUT = { MONTH: 0, WEEK: 1, LIST: 3, DAY: 4 };
const AVAILABLE_CALENDAR_POPUP = {
  STORE: "calendar_store_calendar",
  EDIT: "calendar_edit_calendar",
  SHARE: "calendar_share_calendar",
};
const EVENT_POPUP = 0;
const AVAILABLE_POPUP = {
  STORE_EVENT: "store_event",
  STORE_CALENDAR: "store_calendar",
  SHOW_EVENT: "show_event",
  EDIT_CALENDAR: "edit_calendar",
  SHARE_CALENDAR: "share_calendar",
  FILTER: "filter",
  EVENT: "event",
  CALENDAR_OPTIONS: "calendar_options",
};

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
const userToShare = ref([]);
const usersForm = ref({});
const newEventStart = ref("08:00");
const selectedEvent = ref(null);
const currentCalendarPopupOption = ref(AVAILABLE_CALENDAR_POPUP.STORE);
const eventPopup = ref(null);

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

function deleteEventPopup(dayId, eventId, calendarId) {
  usePopup({
    title: "Supprimer l'événement ?",
    body: "",
    buttons: [
      {
        title: "Annuler",
        onClick: () => { },
        main: false,
      },
      {
        title: "Supprimer",
        onClick: async () => {
          deleteEvent(dayId, eventId, calendarId);
          useToast("L'événement a été supprimé", "success");
        },
        main: true,
      },
    ],
  });
}

function deleteCalendarPopup(calendarId) {
  usePopup({
    title: "Supprimer le calendrier ?",
    body: "",
    buttons: [
      {
        title: "Annuler",
        onClick: () => { },
        main: false,
      },
      {
        title: "Supprimer",
        onClick: async () => {
          deleteCalendar(calendarId);
          useToast("Le calendrier a été supprimé", "success");
        },
        main: true,
      },
    ],
  });
}

function truncate(str, n) {
  return (str.length > n) ? str.substr(0, n - 1) + '&hellip;' : str;
};

function getKeyByValue(object, value) {
  return Object.keys(object).find((key) => object[key] === value);
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
      currentPopup.value = null;
      useToast("Le calendrier a été créé", "success");
    } catch (error) {
      console.log(error);
    }
  } else {
    useLog(
      "storeCalendar: Request failed with status code " + response.status,
      "error"
    );
  }
}

async function storeEvent(form) {
  form = prepareFormBeforeSending(form);
  const response = await useFetch({
    url: API.storeEvent.path(),
    method: API.storeEvent.method,
    data: form,
  });
  console.log({ response });
  if (response.success === true) {
    try {
      form.id = response.data.id;
      displayNewlyCreatedEvent(form);
      reset('newEvent');
      useToast("L'événement a été créé", "success");
    } catch (error) {
      console.log(error);
    }
  } else {
    useLog(
      "storeEvent: Request failed with status code " + response.status,
      "error"
    );
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
    useLog(
      "deleteEvent: Request failed with status code " + response.status,
      "error"
    );
  }
}

async function updateEvent(form) {
  useLog("updateEvent", "success");
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
      useToast("L'événement a été modifié", "success");
      currentPopup.value = null;
    } catch (error) {
      console.log(error);
    }
  } else {
    useLog(
      "updateEvent: Request failed with status code " + response.status,
      "error"
    );
  }
}

async function getCalendars() {
  const response = await useFetch({
    url: API.getEvents.path(),
    method: API.getEvents.method,
  });
  console.log(response);
  if (response.success === true) {
    return response.data;
  } else {
    useLog(
      "getCalendars: Request failed with status code " + response.status,
      "error"
    );
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
          : [calendars.filter((c) => c.name == "Horaires")[0].id.toString()];
    }
  } catch (error) {
    useLog(
      "setCalendars: Error " + error,
      "error"
    );
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
    useLog(
      "deleteCalendar: Request failed with status code " + response.status,
      "error"
    );
  }
}

async function updateCalendar(form) {
  const response = await useFetch({
    url: API.updateCalendar.path(form.calendar_id),
    method: API.updateCalendar.method,
    data: {
      name: form.name,
      color: form.color,
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
      initData();
      useToast("Le calendrier a été mis à jour", "success");
    } catch (error) {
      console.log(error);
    }
  } else {
    useLog(
      "updateCalendar: Request failed with status code " + response.status,
      "error"
    );
  }
}

async function getAllUsers() {
  const response = await useFetch({
    url: API.getUsers.path(),
    method: API.getUsers.method,
  });
  if (response.success === true) {
    return response.data;
  } else {
    console.log(response, "error: no users");
    return [];
  }
}

async function setAllUsers() {
  const allUsers = await getAllUsers();
  /* const allUsers = classrooms.map((classroom) => {
    return classroom.users.map((user) => {
      return user;
    });
  }); */
  users.value = [...allUsers].flat();
}

async function shareCalendar(form) {
  const response = await useFetch({
    url: API.shareCalendar.path(form.calendar_id),
    method: API.shareCalendar.method,
    data: {
      users: form.users,
      can_own: form.can_own ?? false,
      calendar_id: form.calendar_id,
    },
  });
  if (response.success === true) {
    useToast("Le calendrier a bien été partagé.", "success");
    currentPopup.value = null;
  } else {
    useLog(
      "shareCalendar: Request failed with status code " + response.status,
      "error"
    );
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
      event['color'] = calendar.color;
      const ids = Object.keys(currentsCalendarIds.value).map(function (key) {
        return currentsCalendarIds.value[key];
      });
      if (ids.includes(calendarId)) {
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
    currDateCursor.value = TODAY;
    displayedDateManager.value = { dateStart: TODAY };
  } else if (currentLayout.value === AVAILABLE_LAYOUT.WEEK) {
    dates.value = useDate.getAllDaysInWeek(TODAY);
    const monday = useDate.getMonday(TODAY);
    const sunday = useDate.getSunday(TODAY);
    currDateCursor.value = TODAY;
    displayedDateManager.value = { dateStart: monday, dateEnd: sunday };
  } else if (currentLayout.value === AVAILABLE_LAYOUT.DAY) {
    dates.value = useDate.getDaysFromDate(TODAY, 1);
    currDateCursor.value = TODAY;
    displayedDateManager.value = { dateStart: TODAY };
  } else if (currentLayout.value === AVAILABLE_LAYOUT.LIST) {
    dates.value = useDate.getDaysFromDate(TODAY, 30);
    currDateCursor.value = TODAY;
    const todayPlus30 = useDate.getDaysRelativeToDate(TODAY, 30);
    displayedDateManager.value = { dateStart: TODAY, dateEnd: todayPlus30[0] };
  }
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
  selectedDate.value = "";
  newEventPopupRef.value = null;
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
  selectedDate.value = "";
  newEventPopupRef.value = null;
}

function getEvents() {
  console.log("get event calendars", allCalendars.value);
  try {
    const calendars = [];
    for (const [key, value] of Object.entries(currentsCalendarIds.value)) {
      allCalendars.value.forEach((calendar) => {
        if (calendar.id == value) {
          calendars.push(calendar);
        }
      });
    }
    return calendars;
  } catch (e) {
    console.log("ERROR", e);
  }
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
  } else {
    selectedDate.value = dayIndex;
    currentEventPopupIndex.value = index;
    newEventPopup.value = sortEventsByDate(index);
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

function checkIsListLayoutAsEvent() {
  const days = toRaw(dates.value)
  let hasNotEvent = true;
  Object.values(days).forEach((day) => {
    if (toRaw(typeof events.value[day.local] != 'undefined' && events.value[day.local]?.length > 0)) {
      hasNotEvent = false;
    }
  })
  return hasNotEvent
}

// At startup
// ======================================

async function initData() {
  const calendars = await getCalendars();
  if (calendars.length == 0) {
    waitingForData();
  }
  console.log("calendars init data", calendars);
  await setCalendars(calendars);
  await setAllUsers();
  setEvents(getEvents());
  console.log("calendars", calendars);
}

(async function startUp() {
  displayedDateManager.value = { dateStart: TODAY };
  currentLayout.value = layoutStorage.value || AVAILABLE_LAYOUT.MONTH;
  await initData();
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
  newEventPopup.value = events.value[TODAY.toLocaleDateString()]

  watch(currentsCalendarIds, () => {
    selectedCalendarsIdStorage.value = toRaw(currentsCalendarIds.value);
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
            .includes(toRaw(userSearch.value.toLowerCase())) &&
          user.is_shareable
        ) {
          if (!userToShare.value.includes(user.username)) {
            resultOfsearch.push(user.username);
          }
        }
      });
    }
    searchedUser.value = [...userToShare.value, ...resultOfsearch];
  });
})();
</script>
<template>
  <div class="calendar" :class="'calendar--' + getKeyByValue(AVAILABLE_LAYOUT, currentLayout).toLowerCase()">

    <!--==================================-->
    <!-- Calendar Header -->
    <!--==================================-->

    <div class="calendar__wrapper-date" v-if="currentLayout === AVAILABLE_LAYOUT.MONTH">
      <h1 class="calendar__date">
        <span>{{ displayedDateManager.month1 }} {{ displayedDateManager.year1 }}</span>
      </h1>
      <div class="calendar__button-period">
        <button @click="previousPeriod" data-name="précédent" style="all:unset;">
          <span class="material-icons">arrow_back</span>
        </button>
        <button @click="nextPeriod" data-name="suivant" style="all:unset;">
          <span class="material-icons">arrow_forward</span>
        </button>
      </div>
    </div>

    <div class="calendar__wrapper-date" v-if="currentLayout === AVAILABLE_LAYOUT.WEEK">
      <h1 class="calendar__date">
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
        <span>{{ displayedDateManager.day1 }} {{ displayedDateManager.month1 }} {{ displayedDateManager.year1 }}</span>
      </h1>
    </div>

    <div class="calendar__wrapper-date" v-if="currentLayout === AVAILABLE_LAYOUT.LIST">
      <h1 class="calendar__date">
        {{ displayedDateManager.day1 }} {{ displayedDateManager.month1 }} {{ displayedDateManager.year1 }}
        <span class="date-separator" v-show="displayedDateManager.day2"> - </span>
        {{ displayedDateManager.day2 }} {{ displayedDateManager.month2 }} {{ displayedDateManager.year2 }}
      </h1>
    </div>

    <!--====  calendar navigations  ====-->
    <header class="calendar__header">
      <button @click="currentPopup = AVAILABLE_POPUP.FILTER" data-name="filtres">
        <span class="material-icons">filter_alt</span>
      </button>

     <!--  <button @click="previousPeriod" data-name="précédant">
        <span class="material-icons">arrow_back</span>
      </button> -->

      <button @click="actualPeriod" data-name="aujourd'hui">
        <span class="material-icons">today</span>
      </button>
      <button @click="showNewEventForm" data-name="ajouter">
        <span class="material-icons">add_circle_outline</span>
      </button>

      <!-- <button @click="nextPeriod" data-name="suivant">
        <span class="material-icons">arrow_forward</span>
      </button> -->

      <button @click="currentPopup = AVAILABLE_POPUP.CALENDAR_OPTIONS" data-name="éditer">
        <span class="material-icons">edit_calendar</span>
      </button>
    </header>
    <!--====  Calendar days names  ====-->
    <div class="calendar__days-names">
      <div v-show="currentLayout === AVAILABLE_LAYOUT.MONTH" v-for="dayLabel in dayLabels" class="calendar__days-name">
        {{ dayLabel }}
      </div>
    </div>
    <!--====  Calendar no events message  ====-->
    <div class="calendar__no-events" v-show="currentLayout === AVAILABLE_LAYOUT.LIST">
      <p>
        Aucun événements <br />
        pour cette période
      </p>
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
        <p class="calendar__day-number">
          {{ day?.dayOfMonthNumber }}
        </p>
        <div class="calendar__dotes">
          <div v-show="eventId < 5" v-for="(event, eventId) in sortEventsByDate(day?.local)" :key="eventId"
            class="calendar__dot" :style="{ 'background-color': event.color }"></div>
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
            <div v-show="eventId < 5" v-for="(event, eventId) in sortEventsByDate(day?.local)" class="calendar__dot"
              :style="'background-color:' + event.color">
            </div>
          </div>
        </div>
        <div class="calendar__events">
          <div v-for="(event, eventId) in sortEventsByDate(day?.local)" class="calendar__event"
            :class="event.calendar_id == 1 ? 'is-heig-event' : ''"
            :style="event.calendar_id == 1 ? 'background-color: var(--information-color)' : { 'background-color': event.color }"
            @click="selectedEvent = event; eventPopup = EVENT_POPUP;">
            <p class="event__title">
              {{ truncate(event.title, 30) }}</p>
            <p class="event__time"
              :style="event.calendar_id == 1 ? { 'background-color': event.color } : 'background-color: var(--information-color)'">
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
      <the-empty-page v-if="checkIsListLayoutAsEvent()"
        text="Tu n'as aucun événement de programmé, tu peux profiter de ton temps libre !!!"
        image="/images/no_event.svg" />
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
          " :style="{ 'background-color': event.color }" @click="selectedEvent = event; eventPopup = EVENT_POPUP;">
            <p class="calendar__event-text">{{ event.title }}</p>
            <p class="calendar__event-text">{{ useDate.toEventTime(event.start) }}</p>
          </div>
        </article>
      </div>
    </div>
    <!--====  Calendar days DAY  ====-->
    <div v-if="currentLayout === AVAILABLE_LAYOUT.DAY" class="calendar__days" :class="
      'calendar__days--' +
      getKeyByValue(AVAILABLE_LAYOUT, currentLayout).toLocaleLowerCase()
    ">
      <the-empty-page v-if="sortEventsByDate(dates[Object.keys(dates)[0]]?.local)?.length == 0"
        text="Tu n'as aucun événement de programmé, tu peux profiter de ta journée !!!" image="/images/no_event.svg" />
      <div v-for="(day, index) in dates" class="calendar__day" @click="showCurrentEvent(day?.local, index)"
        :class="(selectedDate === index ? 'is-selected-day' : '', day?.class)" :key="index" :date-id="day?.local">

        <article class="calendar__events">
          <div v-for="(event, eventId) in sortEventsByDate(day?.local)" class="calendar__event"
            :data-time="useDate.toEventTime(event?.start)" :class="event.calendar_id == 1 ? 'is-heig-calendar' : ''"
            :data-bullet="
              useDate.toEventTime(
                events[day?.local][eventId - 1]?.start || ' '
              ) != useDate.toEventTime(event.start)
                ? 'true'
                : 'false'
            "
            :style="event.calendar_id == 1 ? 'background-color: var(--information-color)' : { 'background-color': event.color }"
            @click="selectedEvent = event; eventPopup = EVENT_POPUP;">
            <p class="event__header">
              <span class="event__title">{{ event.title }}</span>
              <span class="event__location">{{ event.location }}</span>
              <span v-if="event.calendar_id == 1" class="event__time" :style="'background-color:' + event.color">{{
                  useDate.toEventTime(event.start, event.end)
              }}</span>
              <span v-if="event.calendar_id !== 1" class="event__time" :style="'background-color:transparent'">{{
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
    <button @click="currentPopup = null" class="popup__close">
      <span class="material-icons">close</span>
    </button>
    <h2 class="popup__title">
      <span>Ajouter un événement</span>
    </h2>
    <FormKit type="form" v-model="newEventForm" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Enregistrer"
      @submit="storeEvent" id="newEvent">
      <FormKit type="text" name="title" validation="required" label="Titre" placeholder="Titre" />
      <FormKit type="text" name="location" validation="required" label="Lieu" placeholder="Lieu" />
      <FormKit type="textarea" name="description" validation="required" label="Description" placeholder="Description..."
        rows="3" />
      <FormKit type="time" name="start" label="Début" v-model="newEventStart" />
      <FormKit type="time" name="end" validation="required" label="Fin" :min="newEventStart" :value="newEventStart" />
      <FormKit name="start_date" type="date" :value="getCurrentDateForForm" label="Date de Début" validation="required"
        :min="getCurrentDateForForm" />
      <FormKit v-model="calendarIdWhereToAddTheNewEvent" type="select" label="calendrier" name="calendar_id"
        validation="required">
        <option v-for="(name, id) in editableCalendarsNames" :value="id">
          {{ name }}
        </option>
        <p class="is-submited">Evénement crée</p>
      </FormKit>
      <div class="popup__button-wrapper">
        <button class="button button--cancel is-secondary-button"
          @click.prevent="reset('newEvent'); currentPopup = null">
          Annuler
        </button>
        <button class="button button--save" type="submit">Enregistrer</button>
      </div>
    </FormKit>
  </div>

  <!--==================================-->
  <!-- Popup Event -->
  <!--==================================-->

  <div class="popup popup--event" v-if="eventPopup === EVENT_POPUP">
    <button @click="eventPopup = null" class="popup__close">
      <span class="material-icons">close</span>
    </button>
    <h1 class="event__title" :style="'color:' + selectedEvent.color">
      <span>{{ selectedEvent.title }}</span>
    </h1>
    <div class="event__wrapper">
      <p class="event__date">{{ useDate.toEventDate(selectedEvent.start) }}</p>
      <p class="event__location">Lieu: {{ selectedEvent.location }}</p>
      <p class="event__start">
        Début {{ useDate.toEventTime(selectedEvent.start) }}
      </p>
      <p class="event__end">Fin {{ useDate.toEventTime(selectedEvent.end) }}</p>
      <p class="event__calendar" :style="'color:' + selectedEvent.color">
        {{ calendarsNames[selectedEvent.calendar_id]
        }}<span class="material-icons">calendar_month</span>
      </p>
    </div>
    <hr />
    <p class="event__description">{{ selectedEvent.description }}</p>
    <div class="popup__button-wrapper">
      <button class="button button--edit" v-show="selectedEvent.can_edit" @click.prevent="
  showEventEditForm(
    selectedEvent.start,
    selectedEvent.id,
    selectedEvent.calendar_id
  );
currentPopup = AVAILABLE_POPUP.EDIT_EVENT;
      ">
        Modifier
      </button>
      <button class="button button--delete" v-show="selectedEvent.can_edit" @click.prevent="
        deleteEventPopup(
          selectedEvent.start,
          selectedEvent.id,
          selectedEvent.calendar_id
        )
      ">
        supprimer
      </button>
    </div>
  </div>

  <!--==================================-->
  <!-- Popup Calendar -->
  <!--==================================-->

  <div class="popup popup--calendar" v-show="currentPopup === AVAILABLE_POPUP.CALENDAR_OPTIONS">
    <button @click="currentPopup = ''" class="popup__close">
      <span class="material-icons">close</span>
    </button>
    <h2 class="popup__subtitle">Options de calendrier</h2>
    <div class="popup__choose">
      <FormKit v-model="currentCalendarPopupOption" type="radio" :options="[
        { label: 'Créer', value: AVAILABLE_CALENDAR_POPUP.STORE },
        { label: 'Modifier', value: AVAILABLE_CALENDAR_POPUP.EDIT },
        { label: 'Partager', value: AVAILABLE_CALENDAR_POPUP.SHARE },
      ]" :sections-schema="{
  input: {
    attrs: {
      class: { 'material-icons': 'material-icons' },
    },
  },
}" />
    </div>
    <hr />
    <!--====  Popup store Calendar  ====-->
    <div class="popup--new-calendar" v-show="currentCalendarPopupOption == AVAILABLE_CALENDAR_POPUP.STORE">
      <h1 class="popup__title">
        <span>Créer un Calendrier</span>
      </h1>
      <FormKit id="storeCalendar" type="form" v-model="newCalendarForm" :form-class="isSubmitted ? 'hide' : 'show'"
        submit-label="Enregistrer" @submit="storeCalendar">
        <FormKit type="text" name="name" validation="required" label="Nom" placeholder="Nom du calendrier" />
        <div class="popup__button-wrapper">
          <button class="button button--cancel is-secondary-button"
            @click.prevent="reset('storeCalendar'), currentCalendarPopupOption = null">
            Annuler
          </button>
          <button class="button button--save" type="submit">Enregistrer</button>
        </div>
      </FormKit>
    </div>

    <!--====  Popup Edit Calendar  ====-->
    <div class="popup--edit-calendar" v-show="currentCalendarPopupOption === AVAILABLE_CALENDAR_POPUP.EDIT">
      <h1 class="popup__title">Editer les calendrier</h1>
      <div v-for="calendar in getCalendarsData" class="calendar__edit" :key="calendar.id">
        <FormKit v-if="calendar.can_edit" type="form" :form-class="isSubmitted ? 'hide' : 'show'"
          submit-label="Enregistrer" @submit="updateCalendar">
          <FormKit type="hidden" name="calendar_id" :value="calendar.id" />
          <FormKit type="text" label="Nom" name="name" validation="required" :value="calendar.name" />
          <FormKit type="color" name="color" :value="calendar.color" label="Couleur" />
          <div class="calendar__edit-button-wrapper">
            <button class="button button--delete" @click.prevent="deleteCalendarPopup(calendar.id)">
              Supprimer
            </button>
            <button class="button button--save button" type="submit">
              Mettre à jour
            </button>
          </div>
        </FormKit>
      </div>
    </div>

    <!--====  Popup share Calendar  ====-->
    <div class="popup--share-calendar" v-show="currentCalendarPopupOption === AVAILABLE_CALENDAR_POPUP.SHARE">
      <h1 class="popup__title">Partager un Calendrier</h1>
      <FormKit type="search" placeholder="prenom.nom..." label="Rechercher un utilisateur" v-model="userSearch" />
      <FormKit type="form" v-model="usersForm" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Partager"
        @submit="shareCalendar">
        <FormKit type="checkbox" name="users" :options="searchedUser" v-model="userToShare" validation="required"
          v-if="searchedUser.length > 0" :sections-schema="{
            options: {
              attrs: {
                class: ['checkbox__options'],
              },
            },
            wrapper: {
              attrs: {
                class: ['checkbox__wrapper'],
              },
            },
            label: {
              attrs: {
                class: ['checkbox__label'],
              },
            },
            input: {
              attrs: {
                class: {
                  'material-icons': 'material-icons',
                  'my-checkbox': 'my-checkbox',
                },
              },
            },
          }" />

        <FormKit type="select" label="Calendrier" name="calendar_id" :options="editableCalendarsNames" />
        <div class="my-checkbox">
          <FormKit type="checkbox" label="Droit de modification" name="can_own" :sections-schema="{
            input: {
              attrs: {
                class: ['material-icons'],
              },
            },
          }" />
        </div>
        <div class="popup__button-wrapper">
          <button class="button button--save" type="submit">Partager</button>
        </div>
      </FormKit>
    </div>
  </div>

  <!--==================================-->
  <!-- Popup Filter -->
  <!--==================================-->

  <div class="popup popup--filter" v-show="currentPopup === AVAILABLE_POPUP.FILTER">
    <button @click="currentPopup = null" class="popup__close">
      <span class="material-icons">close</span>
    </button>
    <h1 class="popup__title">
      <span>Filtres</span>
    </h1>
    <hr />
    <h3 class="popup__subtitle">Affichage</h3>
    <ul class="popup__layout-options">
      <li class="popup__layout-option">
        <button :class="
          currentLayout == AVAILABLE_LAYOUT.MONTH ? 'is-selected-layout' : ''
        " class="popup__layout-button" @click="currentLayout = AVAILABLE_LAYOUT.MONTH; currentPopup = null">
          <span class="material-icons">calendar_month</span><span class="popup__text">Mois</span>
        </button>
      </li>
      <li class="popup__layout-option">
        <button :class="
          currentLayout == AVAILABLE_LAYOUT.WEEK ? 'is-selected-layout' : ''
        " class="popup__layout-button" @click="currentLayout = AVAILABLE_LAYOUT.WEEK; currentPopup = null">
          <span class="material-icons is-90-deg">calendar_view_week</span><span class="popup__text">Semaine</span>
        </button>
      </li>
      <li class="popup__layout-option">
        <button :class="
          currentLayout == AVAILABLE_LAYOUT.LIST ? 'is-selected-layout' : ''
        " class="popup__layout-button" @click="currentLayout = AVAILABLE_LAYOUT.LIST; currentPopup = null">
          <span class="material-icons">event_note</span><span class="popup__text">List</span>
        </button>
      </li>
      <li class="popup__layout-option">
        <button :class="
          currentLayout == AVAILABLE_LAYOUT.DAY ? 'is-selected-layout' : ''
        " class="popup__layout-button" @click="currentLayout = AVAILABLE_LAYOUT.DAY; currentPopup = null">
          <span class="material-icons">date_range</span><span class="popup__text">Jour</span>
        </button>
      </li>
    </ul>
    <hr />
    <h3 class="popup__subtitle">Mes calendriers</h3>
    <div class="calendar__choose">
      <FormKit v-model="currentsCalendarIds" type="checkbox" label="Calendrier" :options="calendarsNames"
        :sections-schema="{
          input: {
            attrs: {
              class: { 'material-icons': 'material-icons' },
            },
          },
        }" />
    </div>
  </div>

  <!--==================================-->
  <!-- Popup month events -->
  <!--==================================-->

  <div class="popup popup--month-events" v-show="currentLayout === AVAILABLE_LAYOUT.MONTH">
    <h2 class="popup__title">
      <p>A venir</p>
      <p>
        {{ useDate.toEventDate(newEventPopup?.['0']?.start) }}
      </p>
    </h2>
    <div class="popup__events">
      <article class="popup__event" v-for="(event, index) in newEventPopup" @click="
  selectedEvent = event;
eventPopup = EVENT_POPUP;
      ">
        <div class="event">
          <div class="event__infos">
            <p class="event__dot" :style="'background-color:' + event.color"></p>
            <div class="event__wrapper">
              <p class="event__title">{{ event.title }}</p>
              <p class="event__date">
                {{ event.location }}
              </p>
            </div>
            <p class="event__time" :style="'background-color:' + event.color">{{ useDate.toEventTime(event.start) }}</p>
          </div>
        </div>
      </article>
    </div>
  </div>

  <!--==================================-->
  <!-- Popup edit event -->
  <!--==================================-->

  <div class="popup popup--edit-event" v-if="currentPopup === AVAILABLE_POPUP.EDIT_EVENT">
    <button @click="currentPopup = null" class="popup__close">
      <span class="material-icons">close</span>
    </button>
    <h1 class="event__title">
      <span>Modifier un événement</span>
    </h1>
    <article class="popup__event">
      <FormKit id="editEvent" type="form" v-model="formUpdate" submit-label="Enregistrer" @submit="updateEvent">
        <FormKit type="text" name="title" validation="required" label="Titre" />
        <FormKit type="text" name="location" validation="required" label="Lieu" />
        <FormKit type="textarea" name="description" validation="required" label="Description" rows="3" />
        <FormKit type="time" name="start" label="Début" />
        <FormKit type="time" name="end" label="Fin" />
        <FormKit name="end_date" type="hidden" :value="formUpdate.start" />
        <FormKit name="start_date" type="hidden" :value="formUpdate.start" />
        <FormKit name="id" type="hidden" :value="formUpdate.id" />
        <FormKit v-model="calendarIdWhereToAddTheNewEvent" type="select" label="calendrier" name="calendar_id"
          validation="required" disabled>
          <option v-for="(name, id) in editableCalendarsNames" :value="id">
            {{ name }}
          </option>
        </FormKit>
        <div class="popup__button-wrapper">
          <button class="button button--cancel" @click="currentPopup = null">Annuler</button>
          <button class="button button--edit" type="submit">Modifier</button>
        </div>
      </FormKit>
    </article>
  </div>
</template>
