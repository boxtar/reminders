<!--
    I don't think I've wired this up correctly. Here's why:
        I'm using $forceUpdate to re-render the template. This shouldn't be needed as Vue is built to do this for me and is smarter than me at deciding when updates are required. I'll need to learn more about Vue's reactivity system in order to sort this.

        The way I'm passing props to DatePicker and TimePicker and also updating those same props based on events emitted from the very same Components theoretically means I should have an inifite feedback loop going on here. However, Vue does seems to stop this from happening. I should still look into this and make it more robust (and readable/maintainable - cognitive load is increased because of this).
-->
<template>
    <div class="relative">
        <div
            v-if="isUpdate"
            class="cancel-button bg-yellow-500 text-yellow-800 uppercase text-xs py-1 px-3 rounded-b shadow select-none"
        >
            Edit Mode
        </div>
        <form class="w-full" @submit.prevent="processForm">
            <div class="px-4 py-6 bg-gray-800">
                <!-- Body -->
                <div class="px-4">
                    <label
                        class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                        for="body"
                        >Body</label
                    >
                    <textarea
                        name="body"
                        id="body"
                        rows="1"
                        class="py-3 px-4 block w-full bg-gray-700 text-white appearance-none rounded overflow-hidden leading-tight focus:outline-none focus:shadow-outline"
                        :class="{ 'border border-red-600': form.errors.has('body') }"
                        v-model="form.data.body"
                        @input="form.errors.clear('body')"
                        ref="body"
                    ></textarea>
                    <p class="mt-2 text-red-600 text-xs italic" v-show="form.errors.has('body')">
                        {{ form.errors.get("body") }}
                    </p>
                </div>

                <!-- Reminder Date, Time & Frequency -->
                <div class="mt-3 md:flex flex-wrap">
                    <!-- Date Picker -->
                    <div class="px-4 py-2 md:w-1/2 lg:w-1/3">
                        <label
                            class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                            for="date-picker"
                        >
                            Reminder Date
                        </label>
                        <date-picker
                            :customDate="form.data.date"
                            :customMonth="form.data.month"
                            :customYear="form.data.year"
                            @dateChanged="setDate"
                        ></date-picker>
                    </div>

                    <!-- Time Picker -->
                    <div class="px-4 py-2 md:w-1/2 lg:w-1/3">
                        <label
                            class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                            for="time"
                        >
                            Reminder Time
                        </label>
                        <time-picker :time="form.data.time" @timeChanged="setTime" />
                    </div>

                    <!-- Recurrence -->
                    <div class="px-4 py-2 md:w-1/2 lg:w-1/3">
                        <label
                            class="block  mb-2 uppercase tracking-wide text-gray-500 text-xs font-bold"
                            for="frequency"
                        >
                            Reminder Recurrence
                        </label>
                        <div class="relative">
                            <select
                                class="block appearance-none w-full bg-gray-700 text-white py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:shadow-outline"
                                id="frequency"
                                name="frequency"
                                v-model="form.data.frequency"
                                @keydown.enter.prevent
                            >
                                <option value="none">Don't Recur</option>
                                <option v-for="(frequency, value) in frequencies" :value="value" :key="value"
                                    >{{ frequency }}
                                </option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"
                            >
                                <svg
                                    class="fill-current h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit/Cancel Button -->
                <div class="mt-4 mr-4 flex justify-end items-center">
                    <div v-if="isUpdate" @click.prevent="cancelUpdate" class="">
                        <a
                            href="/"
                            class="inline-block px-4 py-3 text-red-500 border-b-2 border-red-500 leading-tight text-sm font-medium tracking-wider uppercase cursor-pointer focus:outline-none focus:shadow-outline hover:text-red-600 hover:border-red-500"
                        >
                            Cancel
                        </a>
                    </div>
                    <div class="ml-4">
                        <button
                            type="submit"
                            class="px-6 py-3 bg-teal-400 leading-tight text-teal-100 text-sm font-medium tracking-wider uppercase rounded focus:outline-none focus:shadow-outline hover:bg-teal-500"
                        >
                            {{ isUpdate ? "Update" : "Create" }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
const Form = require("../Utils/Form");
const setupAutosizingTextArea = require("../Utils/setupAutosizingTextarea");
const smoothScroll = require("../Utils/smoothScroll");
export default {
    props: ["method", "csrf", "frequencies", "isUpdate", "reminder"],
    data() {
        return {
            actionBase: window.App.routes["api.reminders.put"],
            form: null,
        };
    },
    created() {
        this.resetForm();
    },
    mounted() {
        setupAutosizingTextArea(this.$refs.body);
    },
    watch: {
        // Watchers receive new prop (1st) and old prop (2nd)
        reminder: function(newReminder) {
            newReminder ? this.setupForReminder(newReminder) : this.resetForm();
        },
    },
    methods: {
        async processForm() {
            let response = await this.form.send();
            // Error handler:
            if (response.errors) {
                this.$emit("formError", "Error: " + Object.values(response.errors)[0]);
                this.$forceUpdate();
                return;
            }
            // Success:
            this.resetForm();
            this.$refs.body.style.height = "auto";
            this.$emit(this.isUpdate ? "reminderUpdated" : "reminderAdded", response);
        },
        cancelUpdate() {
            this.resetForm();
            this.$emit("updateCancelled");
        },
        setDate(args) {
            const [date, month, year] = args;
            this.form.set("date", date);
            this.form.set("month", month);
            this.form.set("year", year);
        },
        setTime(time) {
            this.form.set("time", time);
        },
        padToTwoDigits(num) {
            return num < 10 ? `0${num}` : num;
        },
        setupForReminder(reminder) {
            const { id, body, reminder_date, is_recurring } = reminder;
            let { frequency } = reminder; // Yeah, we need to overwrite this with the key

            this.form.setAction(`${this.actionBase}/${id}`);
            this.form.setMethod(this.method);
            this.form.set("body", body);
            this.form.set("year", parseInt(reminder_date.substring(0, 4)));
            this.form.set("month", parseInt(reminder_date.substring(4, 6)));
            this.form.set("date", parseInt(reminder_date.substring(6, 8)));
            this.hour = this.padToTwoDigits(parseInt(reminder_date.substring(8, 10)));
            this.minute = this.padToTwoDigits(parseInt(reminder_date.substring(10, 12)));
            this.form.set("time", `${this.hour}:${this.minute}`);
            // We need the frequency to be equal to the key for binding to the select form element.
            if (is_recurring) {
                const flippedFrequencies = Object.entries(this.frequencies).reduce((ret, entry) => {
                    const [key, value] = entry;
                    ret[value] = key;
                    return ret;
                }, {});
                frequency = flippedFrequencies[frequency];
            }
            this.form.set("frequency", is_recurring ? frequency : "none");
            setTimeout(this.updateSizeOfTextarea, 0);
            smoothScroll();
        },
        updateSizeOfTextarea() {
            const element = this.$refs.body;
            element.style.height = "auto"; // Yes, this is required for it to work when text is all removed.
            element.style.height = element.scrollHeight + "px";
        },
        resetForm() {
            this.form = new Form(
                {
                    body: "",
                    frequency: "none",
                    [this.csrf.name.key]: this.csrf.name.value,
                    [this.csrf.token.key]: this.csrf.token.value,
                },
                this.actionBase,
                this.method
            );
            // To get defaults from Datepicker and Timepicker back
            this.$forceUpdate();
            setTimeout(this.updateSizeOfTextarea, 0);
        },
    },
};
</script>

<style>
.cancel-button {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
}
</style>
