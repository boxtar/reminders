<template>
    <div>
        <!-- Notifications -->
        <notifications :data="notifications.getAll()" @closeNotification="closeNotification" />

        <!-- Add Reminder Component -->
        <add-reminder :frequencies="frequencies" :csrf="csrf" @reminderAdded="addReminder" />

        <!-- Reminder List -->
        <div class="p-4 pt-0 pl-0 md:flex md:flex-wrap justify-start">
            <div
                class="p-4 pr-0 pb-0 md:w-1/2 lg:w-1/3"
                v-for="reminder in reminders.data"
                :key="reminder.id"
            >
                <div class="px-4 py-6 h-full bg-white rounded shadow flex flex-col justify-between">
                    <!-- Body -->
                    <div class="px-4 w-full">
                        <span class="hidden mt-2 block text-xs uppercase text-gray-500">Body</span>
                        <span class="text-xl text-gray-800">{{ reminder.body }}</span>
                    </div>
                    <!-- Reminder and Recurrence Information -->
                    <div class="w-full mt-4">
                        <div class="md:flex flex-wrap">
                            <div class="px-4 mt-2">
                                <!-- Initial Reminder -->
                                <div class="flex items-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="#a0aec0"
                                        width="24px"
                                        height="24px"
                                    >
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path
                                            d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6zM7.58 4.08L6.15 2.65C3.75 4.48 2.17 7.3 2.03 10.5h2c.15-2.65 1.51-4.97 3.55-6.42zm12.39 6.42h2c-.15-3.2-1.73-6.02-4.12-7.85l-1.42 1.43c2.02 1.45 3.39 3.77 3.54 6.42z"
                                        />
                                    </svg>
                                    <span class="block ml-2 tracking-wider text-sm">
                                        <span class="text-teal-400">{{
                                            `${reminder.day.substr(0, 3)} ${
                                                reminder.date
                                            } ${reminder.month.substr(0, 3)} ${reminder.year} at ${pad(
                                                reminder.hour || 0
                                            )}:${pad(reminder.minute || 0)}`
                                        }}</span>
                                    </span>
                                </div>

                                <!-- Recurrence -->
                                <div class="mt-2 flex items-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="#a0aec0"
                                        width="24px"
                                        height="24px"
                                    >
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path
                                            d="M12 6v3l4-4-4-4v3c-4.42 0-8 3.58-8 8 0 1.57.46 3.03 1.24 4.26L6.7 14.8c-.45-.83-.7-1.79-.7-2.8 0-3.31 2.69-6 6-6zm6.76 1.74L17.3 9.2c.44.84.7 1.79.7 2.8 0 3.31-2.69 6-6 6v-3l-4 4 4 4v-3c4.42 0 8-3.58 8-8 0-1.57-.46-3.03-1.24-4.26z"
                                        />
                                    </svg>
                                    <span class="block ml-2 tracking-wider text-sm">
                                        <span class="text-teal-400">{{ reminder.frequency }}</span>
                                    </span>
                                </div>

                                <!-- Archive Button -->
                                <div class="mt-2 flex items-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="#a0aec0"
                                        width="24px"
                                        height="24px"
                                    >
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path
                                            d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-3.5l-1-1zM18 7H6v12c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7z"
                                        />
                                    </svg>
                                    <form
                                        :action="`${reminders.archiveAction}/${reminder.id}`"
                                        @submit="archiveReminder"
                                    >
                                        <button
                                            type="submit"
                                            class="block ml-2 tracking-wider text-sm text-red-300 hover:text-red-400"
                                        >
                                            Archive
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Notifications, types } from "./Notifications/Notifications";

export default {
    props: ["frequencies", "csrf"],
    data() {
        return {
            reminders: {
                action: window.App.routes["api.reminders.get"],
                archiveAction: window.App.routes["api.reminders.archive"],
                data: [],
            },
            notifications: new Notifications(),
        };
    },
    created() {},
    mounted() {
        this.getReminders(this.reminders.action).then(({ data }) => {
            this.reminders.data = Object.values(data);
            this.notifications.add("Loaded", types.info, false, 2);
        });
    },
    methods: {
        // Fetch users reminders from the API
        async getReminders(action) {
            const response = await fetch(action);
            return await response.json();
        },

        // Archive a reminder using the API
        async archiveReminder(e) {
            e.preventDefault();
            let response = await fetch(e.target.action, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    [this.csrf.name.key]: this.csrf.name.value,
                    [this.csrf.token.key]: this.csrf.token.value,
                    _METHOD: "DELETE",
                }),
            });
            response = await response.json();
            this.removeReminder(parseInt(response.data.id));
            this.notifications.add("Reminder archived", types.info);
        },

        // Add provided reminder to list
        addReminder(reminder) {
            this.reminders.data.push(reminder);
            this.notifications.add("Reminder added", types.success);
            this.sortReminders();
        },

        // Sort reminders
        sortReminders() {
            this.reminders.data = this.reminders.data.sort(
                (first, second) => parseInt(first.reminder_date) - parseInt(second.reminder_date)
            );
        },

        // Remove the reminder with the provided id
        removeReminder(id) {
            this.reminders.data = this.reminders.data.filter(item => item.id !== id);
        },

        // Remove the notification with the provided id
        closeNotification(id) {
            this.notifications.remove(id);
        },

        // Pad a number to 2 digits
        pad(number) {
            if (number < 10) return "0" + number.toString();
            else return number;
        },
    },
};
</script>
