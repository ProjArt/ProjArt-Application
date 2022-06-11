const TODAY = new Date();
const DATE_OPTION = ["fr-ch", { year: "numeric", month: "long" }];
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
const CSS = {
    currentDay: "is-current-day",
    clickable: "is-clickable-day",
};

/**
 * @description get a formated object to diplay an interval on date
 * @export
 * @param {date} dateStart
 * @param {date} dateEnd
 * @return {object} {year: number, month: number, day: number}
 */
export function getDateIntervalFormat(dateStart, dateEnd) {
    const year1 = dateStart.getFullYear();
    const year2 = dateEnd.getFullYear();
    const month1 = dateStart.getMonth();
    const month2 = dateEnd.getMonth();
    const day1 = dateStart.getDate();
    const day2 = dateEnd.getDate();
    return {
        year: year1 === year2 ? year1 : `${year1} - ${year2}`,
        month: month1 === month2 ? month1 : `${month1} - ${month2}`,
        day: day1 === day2 ? day1 : `${day1} - ${day2}`,
        interval: `${day1} ${month1} - ${day2} ${month2}`,
    };
}

/**
 * @description get a formated object to diplay a date
 * @export
 * @param {*} date
 * @return {object} {year: number, month: number, day: number}}
 */
export function getDateFormat(date) {
    return {
        year: date.getFullYear(),
        month: date.getMonth(),
        day: date.getDate(),
        weekNumber: getWeekYearNumber(date),
    };
}

/**
 * @description get the week number in year relative to a date
 * @export
 * @param {*} d
 * @return {number}
 */
export function getWeekYearNumber(d) {
    const target = new Date(d.valueOf());
    const dayNr = (d.getDay() + 6) % 7;
    target.setDate(target.getDate() - dayNr + 3);
    const jan4 = new Date(target.getFullYear(), 0, 4);
    const dayDiff = (target - jan4) / 86400000;
    const weekNr = 1 + Math.ceil(dayDiff / 7);
    return weekNr - 1;
}

/**
 * @description convert an anglo-saxon week number to a swiss week number
 * @export
 * @param {number} day
 * @return {number}
 */
export function toSwissDay(day) {
    return day - 1 < 0 ? 6 : day - 1; // si dimanche, on ajoute sinon on retire un jour
}

export function toBritishDay(day) {
    return day + 1 > 6 ? 0 : day + 1; // si samedi, on ajoute sinon on retire un jour
}

export function formatDayObject(ref) {
    return {
        class: setDayCssClass(ref),
        local: ref.toLocaleDateString(),
        dayOfMonthNumber: ref.getDate(),
        dayOfWeekNumber: toSwissDay(ref.getDay()),
        monthNumber: ref.getMonth(),
    };
}

export function getAllDaysInMonth(year, month) {
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

/**
 * @description Get monday date of the week
 * @param {date} - The date to get the Monday of.
 * @returns {date} - The date of the Monday of the week that the date passed in is in.
 */
export function getMonday(date) {
    date = new Date(date);
    const day = date.getDay();
    const diff = date.getDate() - day + (day == 0 ? -6 : 1);
    return new Date(date.setDate(diff));
}

/**
 * @description Get sunday date of the week
 * @param {date} - The date to get the Sunday of.
 * @returns {date} The date of the Sunday of the week that the date passed in is in.
 */
export function getSunday(date) {
    date = new Date(date);
    const day = date.getDay();
    const diff = date.getDate() - day + (day == 0 ? -6 : 1);
    return new Date(date.setDate(diff + 6));
}

export function toSwissDate(date) {
    date = date.split(" ")[0];
    date = date.split("-");
    date = `${date[2]}/${date[1]}/${date[0]}`;
    return date;
}

export function toEventTime(startDate, endDate) {
    const timeStart = startDate.split(" ")[1];
    const timePartsStart = timeStart.split(":");
    if (typeof endDate === "undefined") {
        return `${timePartsStart[0]}:${timePartsStart[1]}`;
    } else {
        const timeEnd = endDate.split(" ")[1];
        const timePartsEnd = timeEnd.split(":");
        return `${timePartsStart[0]}:${timePartsStart[1]} - ${timePartsEnd[0]}:${timePartsEnd[1]}`;
    }
}

export function toEventDate(startDate) {
    if (typeof startDate === "undefined") {
        return "";
    } else {
        const date = startDate.split(" ")[0];
        const newDate = new Date(date);
        let month = MONTH_LABELS[newDate.getMonth()];
        month =
            month.substring(0, 1).toUpperCase() +
            month.substring(1, 3).toLowerCase() +
            ".";
        let day = newDate.getDate().toString();
        day = day.replace(/^0+/, "");
        const year = newDate.getFullYear().toString().substring(2);
        return `${day} ${month} ${year}`;
    }
}

export function getDaysRelativeToDate(date, numberOfDays) {
    return date.setDate(date.getDate() + parseInt(numberOfDays));
}

export function getMonthRelativeToDate(date, numberOfMonths) {
    return date.setMonth(date.getMonth() + parseInt(numberOfMonths));
}

export function swissDateToYMD(dateString, separator) {
    const datesParts = dateString.split("/");
    const year = datesParts[2];
    const month = datesParts[1];
    const day = datesParts[0];
    return year + separator + month + separator + day;
}

export function checkIfBothDatesAreInSameMonth(date1, date2) {
    const date1Month = date1.getMonth();
    const date2Month = date2.getMonth();
    return date1Month === date2Month;
}

export function getDaysFromDate(choosenDate, numberOfDays = 30) {
    const date = new Date(
        choosenDate.getFullYear(),
        choosenDate.getMonth(),
        choosenDate.getDate()
    );
    const dates = {};
    for (let i = 0; i < numberOfDays; i++) {
        if (i != 0) date.setDate(date.getDate() + 1);
        const key = date.toLocaleDateString();
        dates[key] = formatDayObject(date);
    }
    return dates;
}

export function getAllDaysInWeek(choosenDate) {
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

export function setDayCssClass(date) {
    const today = new Date(
        TODAY.getFullYear(),
        TODAY.getMonth(),
        TODAY.getDate()
    );
    if (typeof date === "undefined") return "";
    else if (today.toLocaleDateString() === date.toLocaleDateString())
        return `${CSS.currentDay}`;
    else return "";
}

export function getAllDaysInMonthAndBeginning(year, month) {
    const days = [];
    const daysInMonth =
        typeof year !== "undefined" && typeof month !== "undefined"
            ? getAllDaysInMonth(year, month)
            : getAllDaysInMonth(TODAY.getFullYear(), TODAY.getMonth());

    // get data from the previous month
    let previousYear = month === 0 ? year - 1 : year;
    let previousMonth = month === 0 ? 11 : month - 1;
    const daysInPreviousMonth =
        typeof year !== "undefined" && typeof month !== "undefined"
            ? getAllDaysInMonth(previousYear, previousMonth)
            : getAllDaysInMonth(
                  TODAY.getFullYear() - (month === 0 ? 1 : 0),
                  TODAY.getMonth() - (month === 0 ? 1 : 0)
              );

    for (const [index, date] of Object.entries(daysInMonth)) {
        let i = 0;
        if (index === Object.keys(daysInMonth)[0]) {
            const day = Object.entries(daysInMonth)[0][1];

            // get the keys (30/06/20XX) needed of the previous month to fill the beginning of the month
            const daysInPreviousMonthToTake = Object.keys(
                daysInPreviousMonth
            ).slice(-1 * day.dayOfWeekNumber);
            for (let i = 0; i < day.dayOfWeekNumber; i++) {
                // push the data with the key of the previous month
                days.push(daysInPreviousMonth[daysInPreviousMonthToTake[i]]);
            }
        }
        days.push(date);
    }

    let nextYear = month === 11 ? year + 1 : year;
    let nextMonth = month === 11 ? 0 : month + 1;
    // get data from the next month
    const daysInNextMonth =
        typeof year !== "undefined" && typeof month !== "undefined"
            ? getAllDaysInMonth(nextYear, nextMonth)
            : getAllDaysInMonth(
                  TODAY.getFullYear() + (month == 11 ? 1 : 0),
                  TODAY.getMonth() == 11
                      ? TODAY.getMonth()
                      : TODAY.getMonth() + 1
              );
    const numberOfDayToTake =
        6 - Object.entries(daysInMonth).slice(-1)[0][1].dayOfWeekNumber;
    const daysInNextMonthToTake = Object.keys(daysInNextMonth).slice(
        0,
        numberOfDayToTake
    );

    for (let i = 0; i < daysInNextMonthToTake.length; i++) {
        // get the keys (30/06/20XX) needed of the next month to fill the end of the month
        days.push(daysInNextMonth[daysInNextMonthToTake[i]]);
    }
    return days;
}
