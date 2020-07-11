<template>
    <div class="relative w-full h-screen overflow-x-hidden">
        <!-- Notifications -->
        <notifications :data="notifications.getAll()" @closeNotification="closeNotification" />

        <!-- Main Panel & Side Panel (Create or Update) -->
        <div class="md:flex md:flex-row-reverse">
            <!-- Side panel (Create or Update Reminder form) -->
            <div class="side-panel bg-gray-800" ref="sidePanel" :class="{ 'is-active': showSidePanel }">
                <create-or-update-reminder
                    :class="{ invisible: !showSidePanel }"
                    :method="updateInProgress ? 'PUT' : 'POST'"
                    :csrf="csrf"
                    :frequencies="frequencies"
                    :channels="channels"
                    :isUpdate="updateInProgress"
                    :reminder="reminderBeingUpdated"
                    @reminderAdded="onReminderAdded"
                    @reminderUpdated="onReminderUpdated"
                    @closed="showSidePanel = false"
                    @updateCancelled="clearUpdateState"
                    @formError="onCreateOrUpdateError"
                />
            </div>

            <!-- Main panel (Search box and Reminder list) -->
            <div class="main-panel flex-1" :class="{ 'side-panel-is-active': showSidePanel }">
                <div class="px-4 py-6 flex justify-end">
                    <!-- Search bar -->
                    <div class="relative hidden">
                        <input
                            type="text"
                            class="p-2 md:p-4 bg-white text-gray-500 rounded-full shadow outline-none appearance-none focus:shadow-outline"
                            name="search-reminders"
                            id="search-reminders"
                            placeholder="Coming soon..."
                        />
                        <div class="search-icon absolute top-0 left-0 text-gray-600">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                class="invisible md:visible  fill-current"
                                width="24px"
                                height="24px"
                            >
                                <path d="M0 0h24v24H0V0z" fill="none" />
                                <path
                                    d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"
                                />
                            </svg>
                        </div>
                    </div>

                    <!-- Toggle Create/Update Reminder form -->
                    <button
                        @click="showSidePanel = !showSidePanel"
                        id="show-side-panel-button"
                        class="flex items-center p-2 md:p-4 bg-white shadow rounded-full focus:outline-none focus:shadow-outline"
                        :class="{
                            'rotate-45': showSidePanel,
                            'text-red-500': showSidePanel,
                            'text-teal-400': !showSidePanel,
                        }"
                        :aria-pressed="String(showSidePanel)"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            class="fill-current"
                            width="24px"
                            height="24px"
                        >
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                    </button>
                </div>

                <!-- Upcoming reminders -->
                <h2 class="px-8 font-bold text-xs uppercase text-gray-700" v-show="upcomingReminders.length">
                    Upcoming ({{ upcomingReminders.length }})
                </h2>
                <div class="p-4 pt-0 pl-0 md:flex md:flex-wrap justify-start">
                    <div
                        class="p-4 pr-0 pb-0 w-full md:max-w-sm"
                        v-for="reminder in upcomingReminders"
                        :key="reminder.id + reminder.date"
                    >
                        <reminder
                            :reminder="reminder"
                            @setReminderToBeUpdated="setReminderToBeUpdated"
                            @archiveReminder="archiveReminder"
                        />
                    </div>
                </div>

                <!-- Recurring reminders -->
                <h2 class="px-8 mt-4 font-bold text-xs uppercase text-gray-700"  v-show="recurringReminders.length">
                    On Recurrence ({{ recurringReminders.length }})
                </h2>
                <div class="p-4 pt-0 pl-0 md:flex md:flex-wrap justify-start">
                    <div
                        class="p-4 pr-0 pb-0 w-full md:max-w-sm"
                        v-for="reminder in recurringReminders"
                        :key="reminder.id + reminder.date"
                    >
                        <reminder
                            :reminder="reminder"
                            @setReminderToBeUpdated="setReminderToBeUpdated"
                            @archiveReminder="archiveReminder"
                        />
                    </div>
                </div>

                <!-- Completed reminders -->
                <h2 class="px-8 mt-4 font-bold text-xs uppercase text-gray-700"  v-show="doneReminders.length">
                    Finito ({{ doneReminders.length }})
                </h2>
                <div class="p-4 pt-0 pl-0 md:flex md:flex-wrap justify-start">
                    <div
                        class="p-4 pr-0 pb-0 w-full md:max-w-sm"
                        v-for="reminder in doneReminders"
                        :key="reminder.id + reminder.date"
                    >
                        <reminder
                            :reminder="reminder"
                            @setReminderToBeUpdated="setReminderToBeUpdated"
                            @archiveReminder="archiveReminder"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Notifications, types } from "./Notifications/Notifications";
const smoothScroll = require("../Utils/smoothScroll");
export default {
    props: ["channels", "frequencies", "csrf"],
    data() {
        return {
            reminders: {
                action: window.App.routes["api.reminders.get"],
                archiveAction: window.App.routes["api.reminders.archive"],
                data: [],
            },
            notifications: new Notifications(),
            updateInProgress: false,
            reminderBeingUpdated: null,
            showSidePanel: false,
        };
    },
    mounted() {
        this.getReminders(this.reminders.action).then(({ data }) => {
            this.reminders.data = Object.values(data);
            // this.notifications.add("Loaded", types.info, false, 2);
        });
    },
    methods: {
        // Fetch users reminders from the API
        async getReminders(action) {
            const response = await fetch(action);
            return await response.json();
        },

        // Archive a reminder using the API
        async archiveReminder({ body, id }) {
            if (window.confirm(`Delete "${body}"?`)) {
                const action = `${this.reminders.archiveAction}/${id}`;
                await fetch(action, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        [this.csrf.name.key]: this.csrf.name.value,
                        [this.csrf.token.key]: this.csrf.token.value,
                        _METHOD: "DELETE",
                    }),
                });
                this.removeReminder(parseInt(id));
                this.notifications.add("Reminder archived", types.info);
            }
        },

        // Add provided reminder to list
        onReminderAdded(reminder) {
            this.reminders.data.push(reminder);
            this.notifications.add("Reminder added", types.success);
            this.showSidePanel = false;
            this.sortReminders();
        },

        // Set updated reminder's data and reset some state
        onReminderUpdated(reminder) {
            this.reminders.data = this.reminders.data.filter(r => r.id !== reminder.id);
            this.reminders.data.push({ ...reminder });
            this.clearUpdateState();
            this.showSidePanel = false;
            this.notifications.add("Reminder updated", types.success);
        },

        onCreateOrUpdateError(error) {
            this.notifications.add(error, types.error, true);
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

        // Displays the update form populated with the reminder being updated
        setReminderToBeUpdated(reminder) {
            this.updateInProgress = true;
            this.reminderBeingUpdated = { ...reminder };
            this.showSidePanel = true;

            setTimeout(() => {
                smoothScroll(this.$refs.sidePanel);
            }, 500);
        },

        // Handle an update cancel
        clearUpdateState() {
            this.updateInProgress = false;
            this.reminderBeingUpdated = false;
        },

        // Remove the notification with the provided id
        closeNotification(id) {
            this.notifications.remove(id);
        },
    },
    computed: {
        upcomingReminders: function() {
            return this.reminders.data.filter(reminder => !reminder.initial_reminder_run);
        },
        recurringReminders: function() {
            return this.reminders.data.filter(
                reminder => reminder.initial_reminder_run && reminder.is_recurring
            );
        },
        doneReminders: function() {
            return this.reminders.data.filter(
                reminder => reminder.initial_reminder_run && !reminder.is_recurring
            );
        },
    },
};
</script>

<style scoped>
#search-reminders {
    padding-left: 36px;
}
.search-icon {
    position: absolute;
    top: 50%;
    left: 24px;
    transform: translateY(-50%);
}
.main-panel {
    transition: all 0.2s ease-out;
}
.side-panel {
    display: none;
    transition: all 0.2s ease-out;
}
.side-panel.is-active {
    display: block;
}

@media screen and (min-width: 767px) {
    #search-reminders {
        padding-left: 72px;
    }
    #show-side-panel-button {
        transition: all 0.4s ease-out;
    }
    .main-panel {
        margin-right: -420px;
        transition: all 0.4s ease-out;
    }
    .main-panel.side-panel-is-active {
        margin-right: 0;
    }
    .side-panel {
        display: block;
        position: relative;
        min-height: 100vh;
        width: 420px;
        right: -420px;
        transition: right 0.4s ease-out;
    }
    .side-panel.is-active {
        right: 0;
    }
}
</style>
