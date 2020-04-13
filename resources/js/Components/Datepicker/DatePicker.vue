<template>
    <div class="date-picker rounded " :class="{ 'shadow-outline': isActive }">
        <div
            id="date-picker-formatted-date"
            class="selected-date px-4 py-3 w-full leading-tight tracking-widest bg-gray-700 text-gray-100"
            :class="{ rounded: !isActive, 'rounded-t': isActive, 'bg-teal-400': isActive }"
            @click="isActive = !isActive"
        >{{ formattedDate }}</div>

        <div class="dates rounded-b bg-white" :class="{ hidden: !isActive }">
            <!-- Months -->
            <div class="month-picker">
                <!-- Previous month arrow -->
                <div
                    class="arrows prev-month hover:bg-teal-200 hover:text-white"
                    @click="goToPreviousMonth()"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                        <path d="M0 0h24v24H0V0z" fill="none" opacity=".87" />
                        <path d="M17.51 3.87L15.73 2.1 5.84 12l9.9 9.9 1.77-1.77L9.38 12l8.13-8.13z" />
                    </svg>
                </div>

                <!-- This element holds the selected date -->
                <div class="month">{{ `${month} ${year}` }}</div>

                <!-- Next month arrow -->
                <div class="arrows next-month hover:bg-teal-200 hover:text-white" @click="goToNextMonth()">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                        <path d="M24 24H0V0h24v24z" fill="none" opacity=".87" />
                        <path d="M6.49 20.13l1.77 1.77 9.9-9.9-9.9-9.9-1.77 1.77L14.62 12l-8.13 8.13z" />
                    </svg>
                </div>
            </div>

            <!-- Days -->
            <div class="day-picker">
                <!-- Days heading row -->
                <div
                    class="day bg-gray-300 text-gray-600"
                    style="--aspect-ratio: 1/1;"
                    v-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
                    :key="day"
                >
                    {{ day.substr(0, 1) }}
                </div>

                <!-- Add any required blank cells for offsetting to the correct day column -->
                <div
                    class="day bg-gray-100 cursor-not-allowed"
                    style="--aspect-ratio: 1/1;"
                    v-for="offset in dayOffset"
                    :key="'offset-' + offset"
                ></div>

                <!-- Days -->
                <div
                    class="day border border-transparent cursor-pointer "
                    :class="{
                        'bg-teal-300 border-teal-500':
                            day == selectedDay &&
                            selectedMonth == visibleMonth &&
                            selectedYear == visibleYear,

                        'm-1 rounded-full hover:bg-teal-200 hover:border-teal-300': !dateInPast(
                            day,
                            visibleMonth,
                            visibleYear
                        ),

                        'bg-gray-100 text-gray-500 border-0 cursor-not-allowed': dateInPast(
                            day,
                            visibleMonth,
                            visibleYear
                        ),
                    }"
                    style="--aspect-ratio: 1/1;"
                    v-for="day in getDaysInMonth(visibleMonth, visibleYear)"
                    :key="day"
                    @click="selectDate(day)"
                >
                    {{ day }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const dateFunctions = require("./functions");
export default {
    props: ["userSelectedDate"],
    data() {
        return {
            selectedDate: null,
            selectedDay: null,
            selectedMonth: null,
            selectedYear: null,
            // Represents the month currently visible on the date picker (changed using arrows)
            visibleMonth: null,
            // Represents the year currently visible on the date picker (changed by cycling through months and lapping into prev/next year)
            visibleYear: null,
            isActive: false,
            months: [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ],
            days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
        };
    },
    created() {
        // If a pre-defined selected date is provided, use that.
        if (this.userSelectedDate) {
            this.selectedDate = this.userSelectedDate;
        } else {
            this.selectedDate = new Date();
        }
        this.selectedDay = this.selectedDate.getDate();
        this.visibleMonth = this.selectedMonth = this.selectedDate.getMonth();
        this.visibleYear = this.selectedYear = this.selectedDate.getFullYear();
            this.$emit('datePicked', [this.selectedDay, this.visibleMonth, this.visibleYear])

    },
    mounted() {
        //
    },
    methods: {
        goToNextMonth() {
            this.visibleMonth += 1;
            if (this.visibleMonth > 11) {
                this.visibleMonth = 0;
                this.visibleYear += 1;
            }
        },
        goToPreviousMonth() {
            this.visibleMonth -= 1;
            if (this.visibleMonth < 0) {
                this.visibleMonth = 11;
                this.visibleYear -= 1;
            }
        },

        /**
         * Handles the selection of a new Date by the user
         */
        selectDate(day) {
            // If date selected is in past then do nothing, just return.
            if (dateFunctions.isInThePast(day, this.visibleMonth, this.visibleYear)) return;

            this.selectedDate = new Date(this.visibleYear, this.visibleMonth, day);
            this.selectedDay = day;
            this.selectedMonth = this.visibleMonth;
            this.selectedYear = this.visibleYear;
            this.isActive = false;
            this.$emit('datePicked', [day, this.visibleMonth, this.visibleYear])
        },

        /**
         * Returns the given day of the month suffixed with "th", "st", "nd" or "rd".
         */
        ordinalDay(day) {
            return dateFunctions.ordinalDate(day);
        },

        /**
         * Returns true if the given day, month and year combo is in the past
         * based on today's date.
         */
        dateInPast(d, m, y) {
            return dateFunctions.isInThePast(d, m, y);
        },

        /**
         * This will return a number representing the number of days
         * in the currently visible month.
         */
        getDaysInMonth(month, year) {
            return dateFunctions.getDaysInMonth(month, year);
        },

        isLeapYear(year) {
            return dateFunctions.isLeapYear(year);
        },
    },

    computed: {
        /**
         * Returns a string representing the selected date for display on the UI.
         */
        formattedDate: function() {
            return `${this.days[this.selectedDate.getDay()].substr(0, 3)}, ${this.ordinalDay(
                this.selectedDate.getDate()
            )} ${this.months[this.selectedDate.getMonth()]} ${this.selectedYear}`;
        },

        /**
         * This is so that the first day of the visible month is aligned to the correct
         * day column in the calendar grid.
         */
        dayOffset: function() {
            // This provides the first day of the visible month + year.
            // getDay() returns 0 for Sunday, 1 for Monday, etc.
            // Subtract 1 from the date as we want our days to start at Monday, not Sunday.
            let offset = new Date(this.visibleYear, this.visibleMonth, 1).getDay() - 1;

            // If offset is less than 0 then the first day must be Sunday.
            // e.g. offset (-1) + this.days.length (7) = 6 blank grid cells
            // on the top row of the calendar.
            return offset < 0 ? offset + this.days.length : offset;
        },

        month: function() {
            return this.months[this.visibleMonth];
        },

        year: function() {
            return this.visibleYear;
        },
    },
};
</script>

<style>
/* Container */
.date-picker {
    position: relative;
    background-color: transparent;
    /* width: 350px; */
    width: 100%;
}

/* Toggles the active state of the date picker */
.date-picker .selected-date {
    /* text-align: center; */
    /* font-weight: 300; */
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease-out;
}

/* The month picker. Only visible when date picker is active */
.date-picker .month-picker {
    display: flex;
    justify-content: space-between;
    align-items: center;
    /* border-bottom: 2px solid #eee; */
}

.date-picker .month-picker .arrows {
    width: 50px;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #313131;
    font-size: 20px;
    cursor: pointer;
    user-select: none;
}

.date-picker .month-picker .arrows:active {
    background-color: #9ae6b4;
}

/* The day of the month picker. Only visible when the date picker is active */
.date-picker .day-picker {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-template-rows: repeat(5, 1fr);
    /* 7 days at 50px each */
    /* width: 350px; */
    /* 5 rows of days at 50px each */
    /* height: 250px; */
}

.date-picker .day-picker .day {
    display: flex;
    justify-content: center;
    align-items: center;
    user-select: none;
}

/* Hack to maintain square grid items */
/* https://css-tricks.com/aspect-ratios-grid-items/#article-header-id-1 */
.date-picker .day-picker > [style^="--aspect-ratio"]::before {
    content: "";
    display: inline-block;
    width: 1px;
    height: 0;
    padding-bottom: calc(100% / (var(--aspect-ratio)));
}
</style>
