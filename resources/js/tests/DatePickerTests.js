var assert = require("assert");
const {
    isInThePast,
    ordinalDate,
    isFebruary,
    getDaysInMonth,
    monthHasThirtyDays,
    isLeapYear,
} = require("../Components/Datepicker/functions");

describe("DatePicker", function() {
    describe("isInThePast", function() {
        it("should return true when the provided date is in the past", function() {
            const now = new Date();
            const d = now.getDate();
            const m = now.getMonth();
            const y = now.getFullYear();
            assert.equal(true, isInThePast(1, 2, 2020));
            assert.equal(true, isInThePast(d - 1, m, y));
            assert.equal(true, isInThePast(d, m - 1, y));
            assert.equal(true, isInThePast(d, m, y - 1));
        });
        it("should return false when the provided date is in the future", function() {
            const now = new Date();
            const d = now.getDate();
            const m = now.getMonth();
            const y = now.getFullYear();
            assert.equal(false, isInThePast(d, m, y));
            assert.equal(false, isInThePast(d + 1, m, y));
            assert.equal(false, isInThePast(d, m + 1, y));
            assert.equal(false, isInThePast(d, m, y + 1));
        });
    });

    describe("ordinalDate", function() {
        it("returns the day of the month with the correct suffix", function() {
            assert.equal("1st", ordinalDate(1));
            assert.equal("2nd", ordinalDate(2));
            assert.equal("3rd", ordinalDate(3));
            assert.equal("4th", ordinalDate(4));
        });
    });

    describe("getDaysInMonth", function() {
        it("returns 28 when the month is February and not a leap year", function() {
            assert.equal(28, getDaysInMonth(1, 2011))
        })
        it("returns 29 when the month is February and is a leap year", function() {
            assert.equal(29, getDaysInMonth(1, 2020))
        })
        it("returns 30 when the month is Apr, Jun, Sept or Nov", function() {
            assert.equal(30, getDaysInMonth(3, 2020))
            assert.equal(30, getDaysInMonth(5, 2020))
            assert.equal(30, getDaysInMonth(8, 2020))
            assert.equal(30, getDaysInMonth(10, 2020))
        })
        it("returns 31 when the month is Jan, Mar, May, Jul, Aug, Oct or Dec", function() {
            assert.equal(31, getDaysInMonth(0, 2020))
            assert.equal(31, getDaysInMonth(2, 2020))
            assert.equal(31, getDaysInMonth(4, 2020))
            assert.equal(31, getDaysInMonth(6, 2020))
            assert.equal(31, getDaysInMonth(7, 2020))
            assert.equal(31, getDaysInMonth(9, 2020))
            assert.equal(31, getDaysInMonth(11, 2020))
        })
    })

    describe("monthHasThirtyDays", function() {
        it("returns true if given month has 30 days", function() {
            assert.equal(true, monthHasThirtyDays(3));
            assert.equal(true, monthHasThirtyDays(5));
            assert.equal(true, monthHasThirtyDays(8));
            assert.equal(true, monthHasThirtyDays(10));
        });
        it("returns false if given month does not have 30 days", function() {
            assert.equal(false, monthHasThirtyDays(0));
            assert.equal(false, monthHasThirtyDays(1));
            assert.equal(false, monthHasThirtyDays(2));
            assert.equal(false, monthHasThirtyDays(4));
            assert.equal(false, monthHasThirtyDays(6));
            assert.equal(false, monthHasThirtyDays(7));
            assert.equal(false, monthHasThirtyDays(9));
            assert.equal(false, monthHasThirtyDays(11));
        });
    });

    describe("isFebruary", function() {
        it("returns true if given month is 1", function() {
            assert.equal(true, isFebruary(1));
        });
        it("returns false if given month is not 1", function() {
            assert.equal(false, isFebruary(0));
            assert.equal(false, isFebruary(2));
        });
    });

    describe("isLeapYear", function() {
        it("returns true if the given year is a leap year", function() {
            assert.equal(true, isLeapYear(2016));
            assert.equal(true, isLeapYear(2020));
            assert.equal(true, isLeapYear(2024));
        });
        it("returns false if the given year is not a leap year", function() {
            assert.equal(false, isLeapYear(2011));
            assert.equal(false, isLeapYear(2033));
        });
    });
});
