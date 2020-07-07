<!--
    I don't think I've wired this up correctly. Here's why:
        I'm using $forceUpdate to re-render the template. This shouldn't be needed as Vue is built to do this for me and is smarter than me at deciding when updates are required. I'll need to learn more about Vue's reactivity system in order to sort this.

        The way I'm passing props to DatePicker and TimePicker and also updating those same props based on events emitted from the very same Components theoretically means I should have an inifite feedback loop going on here. However, Vue does seems to stop this from happening. I should still look into this and make it more robust (and readable/maintainable - cognitive load is increased because of this).
-->
<template>
    <div class="relative bg-gray-800">
        <!-- Edit Mode flag -->
        <div
            v-if="isUpdate"
            class="cancel-button bg-yellow-500 text-yellow-800 uppercase text-xs py-1 px-3 rounded-b shadow select-none"
        >
            Edit Mode
        </div>

        <!-- Close button -->
        <div class="hidden p-4 pb-0 text-gray-400 text-right hover:text-white" @click="$emit('closed')">
            <span class="hidden text-xs uppercase">Close</span>
            <svg
                class="fill-current inline-block"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="24px"
                height="24px"
            >
                <path d="M0 0h24v24H0V0z" fill="none" />
                <path
                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"
                />
            </svg>
        </div>

        <form class="w-full" @submit.prevent="processForm">
            <div class="px-4 py-6">
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
                <div class="mt-3">
                    <!-- Date Picker -->
                    <div class="px-4 py-2">
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
                    <div class="px-4 py-2">
                        <label
                            class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                            for="time"
                        >
                            Reminder Time
                        </label>
                        <time-picker :time="form.data.time" @timeChanged="setTime" />
                    </div>

                    <!-- Recurrence -->
                    <div class="px-4 py-2">
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

                    <!-- Channels -->
                    <div class="px-4 py-2">
                        <span class="block mb-2 uppercase tracking-wide text-gray-500 text-xs font-bold">
                            Remind me using
                        </span>
                        <div class="" v-for="channel in channels" :key="channel">
                            <label class="mb-2 inline-flex items-center">
                                <!-- <input
                                    type="checkbox"
                                    class="form-checkbox"
                                    v-model="form.data.selectedChannels[channel]"
                                /> -->
                                <input
                                    type="checkbox"
                                    class="form-checkbox"
                                    :value="channel"
                                    v-model="form.data.channels"
                                />
                                <span class="ml-2 uppercase text-xs text-white">{{ channel }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit/Cancel Button -->
                <div class="mt-3">
                    <div class="px-4 py-2 flex ml-auto">
                        <div class="">
                            <button
                                type="submit"
                                class="px-6 py-3 bg-blue-600 leading-tight text-teal-100 text-xs font-medium tracking-wider uppercase rounded focus:outline-none focus:shadow-outline hover:bg-blue-700"
                            >
                                {{ isUpdate ? "Update" : "Create" }}
                            </button>
                        </div>
                        <div v-show="isUpdate" @click.prevent="cancelUpdate" class="ml-4">
                            <a
                                href="/"
                                class="inline-block px-4 py-3 text-red-400 leading-tight text-xs font-medium tracking-wider uppercase cursor-pointer focus:outline-none focus:shadow-outline hover:text-red-500"
                            >
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
const Form = require("../Utils/Form");
const setupAutosizingTextArea = require("../Utils/setupAutosizingTextarea");
export default {
    props: ["method", "channels", "csrf", "frequencies", "isUpdate", "reminder"],
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
            const { id, body, reminder_date, is_recurring, channels } = reminder;
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
            this.form.set("channels", [...channels]);

            // We need the frequency to be equal to the key for binding to the select form element.
            this.form.set("frequency", "none"); // Default to no recurrence
            if (is_recurring) {
                const flippedFrequencies = Object.entries(this.frequencies).reduce((ret, entry) => {
                    const [key, value] = entry;
                    ret[value] = key;
                    return ret;
                }, {});
                frequency = flippedFrequencies[frequency];
                this.form.set("frequency", frequency);
            }

            // Make textarea fit the reminder's body
            setTimeout(this.updateSizeOfTextarea, 0);
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
                    channels: this.channels.length ? [this.channels[0]] : [], // First channel checked by default
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
