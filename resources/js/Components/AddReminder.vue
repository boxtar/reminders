<template>
    <form :action="this.form.action" class="w-full" method="POST">
        <div class="px-4 py-6 bg-gray-800">
            <!-- Body -->
            <div class="px-4">
                <label class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2" for="body"
                    >Add a new reminder</label
                >
                <input
                    class="appearance-none block w-full bg-gray-700 text-white rounded py-3 px-4 leading-tight focus:outline-none focus:shadow-outline"
                    id="body"
                    type="text"
                    name="body"
                    v-model="form.body"
                    placeholder="Do the things..."
                />
            </div>

            <!-- Reminder Fields -->
            <div class="mt-3 md:flex flex-wrap">
                <!-- Frequency -->
                <div class="px-4 py-2 md:w-1/3">
                    <label
                        class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                        for="frequency"
                    >
                        Frequency
                    </label>
                    <div class="relative">
                        <select
                            class="block appearance-none w-full bg-gray-700 text-white py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:shadow-outline"
                            id="frequency"
                            name="frequency"
                            v-model="form.frequency"
                        >
                            <option
                                v-for="(frequency, value) in dates.frequencies"
                                :value="value"
                                :key="value"
                                >{{ frequency }}</option
                            >
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

                <!-- Day -->
                <div class="px-4 py-2 md:w-1/3">
                    <label
                        class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                        for="day"
                    >
                        Day
                    </label>
                    <div class="relative">
                        <select
                            class="block appearance-none w-full bg-gray-700 text-white py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:shadow-outline"
                            id="day"
                            name="day"
                            v-model="form.day"
                        >
                            <option value="-99">-</option>
                            <option v-for="(day, value) in dates.days" :value="value" :key="value">{{
                                day
                            }}</option>
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

                <!-- Date -->
                <div class="px-4 py-2 md:w-1/3">
                    <label
                        class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                        for="date"
                    >
                        Date
                    </label>
                    <div class="relative">
                        <select
                            class="block appearance-none w-full bg-gray-700 text-white py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:shadow-outline"
                            id="date"
                            name="date"
                            v-model="form.date"
                        >
                            <option value="-99">-</option>
                            <option
                                v-for="(date, value) in dates.dates"
                                :value="value + 1"
                                :key="value + 1"
                                >{{ date }}</option
                            >
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

                <!-- Time -->
                <div class="px-4 py-2 md:w-1/3">
                    <label
                        class="block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                        for="time"
                    >
                        Time
                    </label>
                    <div class="relative">
                        <select
                            class="block appearance-none w-full bg-gray-700 text-white py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:shadow-outline"
                            id="time"
                            name="time"
                            v-model="form.time"
                        >
                            <option v-for="time in dates.times" :value="time" :key="time">{{ time }}</option>
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

                <!-- Run Once? -->
                <div class="px-4 py-2 md:w-1/3">
                    <label
                        class="hidden md:invisible md:block uppercase tracking-wide text-gray-500 text-xs font-bold mb-2"
                        for="run_once"
                        >Run Once?</label
                    >
                    <div class="py-3 flex items-center">
                        <input type="checkbox" name="run_once" id="run_once" class="form-checkbox" />
                        <label
                            class="uppercase tracking-wide text-gray-500 text-xs font-bold ml-4"
                            for="run_once"
                            >Run Once?</label
                        >
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
</template>

<script>
export default {
    props: ["dates"],
    data() {
        return {
            form: {
                action: undefined,
                body: "",
                frequency: "",
                day: -99,
                date: -99,
                time: "",
                runOnce: "",
                errors: {},
            },
        };
    },
    mounted() {
        this.form.action = window.App.routes["reminders.store"];
        this.form.frequency = Object.keys(this.dates.frequencies)[0];
        this.form.time = this.dates.times[0];
    },
};
</script>

<style></style>
