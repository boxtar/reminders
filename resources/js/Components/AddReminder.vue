<template>
    <div>
        <form :action="this.form.action" class="w-full" method="POST" @submit.prevent="processForm">
            <div class="px-4 py-6 bg-gray-800">
                <!-- Csrf -->
                <div class="hidden">
                    <input
                        type="hidden"
                        :name="csrf && csrf.name && csrf.name.key"
                        :value="csrf && csrf.name && csrf.name.value"
                    />
                    <input
                        type="hidden"
                        :name="csrf && csrf.token && csrf.token.key"
                        :value="csrf && csrf.token && csrf.token.value"
                    />
                </div>

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
                        :class="{ 'border border-red-600': hasError('body') }"
                        v-model="form.body.value"
                        @input="clearError('body')"
                        ref="body"
                    ></textarea>
                    <p class="mt-2 text-red-600 text-xs italic" v-show="hasError('body')">
                        {{ getError("body") }}
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
                        <date-picker @datePicked="setDate"></date-picker>
                    </div>

                    <!-- Time Picker -->
                    <div class="px-4 py-2 md:w-1/2 lg:w-1/3">
                        <label
                            class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                            for="time"
                        >
                            Reminder Time
                        </label>
                        <time-picker @input="setTime" />
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
                                v-model="form.frequency"
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

                <!-- Submit Button -->
                <div class="flex justify-end items-center">
                    <div class="mt-4 mr-4">
                        <button
                            type="submit"
                            class="px-6 py-3 bg-teal-400 leading-tight text-teal-100 text-sm font-medium tracking-wider uppercase rounded focus:outline-none focus:shadow-outline"
                        >
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
const Errors = require("../Utils/Errors");
const FormInput = require("../Utils/FormInput");
export default {
    props: ["frequencies", "csrf"],
    data() {
        return {
            form: {
                action: window.App.routes["api.reminders.store"],
                body: new FormInput("body", ""),
                frequency: "none",
                date: -99,
                month: -99,
                time: "",
            },
            errors: new Errors(),
        };
    },
    mounted() {
        // Auto resizing textarea
        this.setupAutoSizingBodyElement();
    },
    methods: {
        setDate(args) {
            const [day, month, year] = args;
            this.form.date = day;
            this.form.month = month;
            this.form.year = year;
        },
        setTime(time) {
            this.form.time = time;
        },
        getError(key) {
            return this.errors.get(key);
        },
        hasError(key) {
            return this.errors.has(key);
        },
        clearError(key) {
            this.errors.clear(key);
        },
        processForm() {
            if (this.validateForm()) {
                this.sendForm(this.form.action, this.setupForm()).then(({ data }) => {
                    this.form.body.value = "";
                    this.$refs.body.style.height = "auto";
                    this.$emit("reminderAdded", data);
                }).catch(() => {
                    this.$emit("reminderError", "Sorry, there was a problem.");
                })
            }
        },
        validateForm() {
            // Clear any residual errors
            this.errors.clear();

            // Body validation
            if (this.form.body.value.length == 0) {
                this.errors.set(
                    this.form.body.name,
                    "You must provide a body (e.g. Retrieve hobbits from Isengard)"
                );
            }

            // Return true if no validation errors, false otherwise.
            return !this.errors.any();
        },
        setupForm() {
            return JSON.stringify({
                body: this.form.body.value,
                date: this.form.date,
                month: this.form.month,
                year: this.form.year,
                time: this.form.time,
                frequency: this.form.frequency,
                [this.csrf.name.key]: this.csrf.name.value,
                [this.csrf.token.key]: this.csrf.token.value,
            });
        },
        async sendForm(action, data) {
            const response = await fetch(action, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: data,
                redirect: "follow",
            });
            return await response.json();
        },

        setupAutoSizingBodyElement() {
            // https://stackoverflow.com/a/5346855
            const body = this.$refs.body;
            function resize() {
                body.style.height = "auto"; // Dunno why this is needed?
                body.style.height = body.scrollHeight + "px";
            }
            /* 0-timeout to get the already changed text */
            function delayedResize() {
                setTimeout(resize, 0);
            }
            body.addEventListener("change", resize);
            body.addEventListener("keydown", delayedResize);
            body.addEventListener("paste", delayedResize);
            body.addEventListener("cut", delayedResize);
            body.addEventListener("drop", delayedResize);
        },
    },
};
</script>

<style></style>
