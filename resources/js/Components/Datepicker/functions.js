/**
 * Returns the given day of the month suffixed with "th", "st", "nd" or "rd".
 */
const ordinalDate = day => {
    const ends = ["th", "st", "nd", "rd", "th", "th", "th", "th", "th", "th"];
    if (day % 100 >= 11 && day % 100 <= 13) return day + "th";
    return day + ends[day % 10];
};

const getDaysInMonth = (month, year) => {
    if (isFebruary(month)) {
        return isLeapYear(year) ? 29 : 28;
    } else if (monthHasThirtyDays(month)) {
        return 30;
    } else {
        return 31;
    }
};

// In Javascript, months are 0 based. Jan = 0, Feb = 2, etc.
const isFebruary = month => month == 1;

// Apr = 3, June = 5, Sept = 8, Nov = 10
const monthHasThirtyDays = month => [3, 5, 8, 10].includes(month);

// I Google'd for this function.
const isLeapYear = year => {
    return year % 100 === 0 ? year % 400 === 0 : year % 4 === 0;
};

/**
 * Returns true if the given day, month and year combo is in the past
 * based on today's date.
 */
const isInThePast = (d, m, y) => {
    const now = new Date();
    const yearNow = now.getFullYear();
    const monthNow = now.getMonth();
    const dayNow = now.getDate();

    if (yearNow > y) return true;
    if (yearNow < y) return false;
    if (yearNow == y && monthNow > m) return true;
    if (yearNow == y && monthNow < m) return false;
    if (yearNow == y && monthNow == m && dayNow > d) return true;
    if (yearNow == y && monthNow == m && dayNow <= d) return false;
};

module.exports = {
    isInThePast,
    getDaysInMonth,
    isFebruary,
    monthHasThirtyDays,
    isLeapYear,
    ordinalDate,
};
