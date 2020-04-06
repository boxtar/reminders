<template>
    <div>
        <!-- Notifications -->
        <notifications :data="notifications.getAll()" @closeNotification="closeNotification" />

        <!-- Add Reminder Component -->
        <add-reminder :dates="dates" :csrf="csrf" @reminderAdded="addReminder" />

        <!-- Reminder List -->
        <div class="p-4 pt-0 pl-0 md:flex md:flex-wrap justify-start">
            <div
                class="p-4 pr-0 pb-0 md:w-1/2 lg:w-1/3"
                v-for="reminder in reminders.data"
                :key="reminder.id"
            >
                <div class="px-4 py-6 h-full bg-white rounded shadow">
                    <!-- Body -->
                    <div class="px-4">
                        <span class="hidden mt-2 block text-xs uppercase text-gray-500">Body</span>
                        <span class="text-2xl text-gray-800">{{ reminder.body }}</span>
                    </div>
                    <!-- Reminder and Recurrence Information -->
                    <div class="md:flex flex-wrap">
                        <div class="px-4 mt-2">
                            <span class="block tracking-wider text-xs text-gray-500">
                                Reminder:
                                <span class="text-teal-400">{{
                                    `${reminder.date} ${reminder.month} ${reminder.year} @ ${pad(
                                        reminder.hour || 0
                                    )}:${pad(reminder.minute || 0)}`
                                }}</span>
                            </span>
                            <span class="block tracking-wider text-xs text-gray-500">
                                Recurrence: <span class="text-teal-400">{{ reminder.frequency }}</span>
                            </span>
                        </div>
                    </div>
                    <!-- Archive Button -->
                    <div class="flex justify-end items-center">
                        <form :action="`${reminders.archiveAction}/${reminder.id}`" @submit="archiveReminder">
                            <button
                                type="submit"
                                class="px-4 py-1 uppercase text-xs tracking-wider text-red-400 focus:outline-none focus:shadow-outline"
                            >
                                Archive
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Notifications, types } from "./Notifications/Notifications";

export default {
    props: ["dates", "csrf"],
    data() {
        return {
            reminders: {
                action: window.App.routes["api.reminders.get"],
                archiveAction: window.App.routes["api.reminders.archive"],
                data: [],
            },
            notifications: new Notifications,
        };
    },
    created() {},
    mounted() {
        this.getReminders(this.reminders.action).then(({ data }) => {
            this.reminders.data = Object.values(data);
            this.notifications.add("Loaded", types.info, false, 2)
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
            this.reminders.data.unshift(reminder);
            this.notifications.add("Reminder added", types.success);
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
