<template>
    <div class="relative w-full h-screen overflow-x-hidden">
        <!-- Notifications -->
        <notifications :data="notifications.getAll()" @closeNotification="closeNotification" />

        <div v-show="this.searchTerm">
            <div class="flex justify-between items-center px-4 py-2 bg-blue-500 text-white text-center">
                <span class="mr-2">{{ reminderDataResultsText }} </span>
                <button class="text-sm underline" @click.stop.prevent="clearSearchTerm">clear</button>
            </div>
        </div>

        <!-- Main Panel & Side Panel (Create or Update) -->
        <div class="flex flex-row-reverse">
            <!-- Side panel (Create or Update Reminder form) -->
            <div class="side-panel bg-gray-800" ref="sidePanel" :class="{ 'is-active': showSidePanel }">
                <create-or-update-reminder
                    ref="createOrUpdateForm"
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
                <!-- Search bar and Toggle button -->
                <div class="px-4 py-6 flex justify-between">
                    <!-- Search bar -->
                    <div class="relative mr-4">
                        <div class="search-icon flex top-0 left-0 text-gray-600">
                            <div v-show="!this.searchTerm">
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
                            <button v-show="this.searchTerm" @click.stop.prevent="clearSearchTerm">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="fill-current"
                                    width="24px"
                                    height="24px"
                                >
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path
                                        d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"
                                    />
                                </svg>
                            </button>
                        </div>
                        <input
                            type="text"
                            class="p-2 md:p-4 bg-white text-gray-500 rounded-full shadow outline-none appearance-none focus:shadow-outline"
                            name="search-reminders"
                            id="search-reminders"
                            placeholder="Search..."
                            v-model="searchTerm"
                            ref="searchForm"
                            @keydown.esc="clearSearchTerm"
                        />
                    </div>

                    <!-- Toggle Create/Update Reminder form -->
                    <button
                        @click="toggleSidePanel"
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
                        :key="reminder.id"
                    >
                        <reminder
                            :reminder="reminder"
                            @setReminderToBeUpdated="setReminderToBeUpdated"
                            @archiveReminder="archiveReminder"
                        />
                    </div>
                </div>

                <!-- Recurring reminders -->
                <h2
                    class="px-8 mt-4 font-bold text-xs uppercase text-gray-700"
                    v-show="recurringReminders.length"
                >
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
                <h2 class="px-8 mt-4 font-bold text-xs uppercase text-gray-700" v-show="doneReminders.length">
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
import Fuse from "fuse.js";
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
            fuse: null,
            searchTerm: "",
        };
    },
    mounted() {
        this.getReminders(this.reminders.action).then(({ data }) => {
            this.reminders.data = Object.values(data);
            this.setupSearch();
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
                // This removes the reminder from state
                this.removeReminder(parseInt(id));
                // We also need to update our search data now that we've removed an item
                this.setupSearch();
                this.notifications.add("Reminder archived", types.info);
            }
        },

        // Add the provided reminder
        addReminder(reminder) {
            this.reminders.data.push({ ...reminder });
        },

        // Remove the reminder with the provided id
        removeReminder(id) {
            this.reminders.data = this.reminders.data.filter(item => item.id !== id);
        },

        // Add provided reminder to list
        onReminderAdded(reminder) {
            // Push the new reminder onto the list
            this.reminders.data.push(reminder);
            // Re-sort
            this.sortReminders();
            // Hide the side panel now that the reminder has been added.
            this.showSidePanel = false;
            // We also need to update our search data now that we've removed an item
            this.setupSearch();
            this.notifications.add("Reminder added", types.success);
        },

        // Set updated reminder's data and reset some state
        onReminderUpdated(reminder) {
            // Have to remove the reminder first
            this.removeReminder(reminder.id);
            // Then re-add it to the list in order to get the state to update
            this.addReminder(reminder);
            // Re-sort reminders now that we've pushed the updated reminder
            this.sortReminders();
            // Update is done so clear the update state from the form and hide it
            this.clearUpdateState();
            this.showSidePanel = false;
            // We also need to update our search data now that we've removed an item
            this.setupSearch();
            this.notifications.add("Reminder updated", types.success);
        },

        // Show notification is there was an error creating or updating
        onCreateOrUpdateError(error) {
            this.notifications.add(error, types.error, true);
        },

        // Sort reminders
        sortReminders() {
            this.reminders.data = this.reminders.data.sort(
                (first, second) => parseInt(first.reminder_date) - parseInt(second.reminder_date)
            );
        },

        // Opens up the side panel which exposes the create/update form
        toggleSidePanel() {
            // If is is currently open, close it, else open it.
            this.showSidePanel ? this.closeSidePanel() : this.openSidePanel()
        },

        openSidePanel() {
            this.showSidePanel = true;
            // Focus body element of the side panel form.
            // Delay the focus to allow for side panel to transition in
            setTimeout(() => {
                this.$refs.createOrUpdateForm.$refs.body.focus();
            }, 500);
        },

        closeSidePanel() {
            this.showSidePanel = false
        },

        // Displays the update form populated with the reminder being updated
        setReminderToBeUpdated(reminder) {
            this.updateInProgress = true;
            this.reminderBeingUpdated = { ...reminder };
            this.openSidePanel();

            // Smoothly scroll the top of the form into view
            setTimeout(() => {
                smoothScroll(this.$refs.sidePanel);
            }, 500);
        },

        // Clear state for updating a reminder (on cancel or successful update)
        clearUpdateState() {
            this.updateInProgress = false;
            this.reminderBeingUpdated = false;
        },

        // Remove the notification with the provided id
        closeNotification(id) {
            this.notifications.remove(id);
        },

        // Initial setup of Search library. Also call for resetting search data (e.g. on create, update and delete)
        setupSearch() {
            this.fuse = new Fuse(this.reminders.data, { keys: ["body"], threshold: 0.4 });
        },

        // Clears the search term
        clearSearchTerm() {
            this.searchTerm = "";
        },
    },
    computed: {
        reminderData: function() {
            return this.searchTerm ? this.fuse.search(this.searchTerm).map(i => i.item) : this.reminders.data;
        },
        reminderDataResultsText: function() {
            const count = this.reminderData.length;
            return `Showing ${count} search result${count == 1 ? "" : "s"}`;
        },
        upcomingReminders: function() {
            return this.reminderData.filter(reminder => !reminder.initial_reminder_run);
        },
        recurringReminders: function() {
            return this.reminderData.filter(
                reminder => reminder.initial_reminder_run && reminder.is_recurring
            );
        },
        doneReminders: function() {
            return this.reminderData.filter(
                reminder => reminder.initial_reminder_run && !reminder.is_recurring
            );
        },
    },
};
</script>

<style>
#search-reminders {
    padding-left: 36px;
}
.search-icon {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
}
.main-panel {
    margin-right: -100%;
    transition: all 0.4s ease-out;
}
.main-panel.side-panel-is-active {
    margin-right: 0;
}
.side-panel {
    flex-shrink: 0;
    display: block;
    position: relative;
    min-height: 100vh;
    width: 100%;
    right: -100%;
    transition: right 0.4s ease-out;
}
.side-panel.is-active {
    right: 0;
}
@media screen and (min-width: 767px) {
    #search-reminders {
        padding-left: 72px;
    }
    .search-icon {
        left: 24px;
    }
    #show-side-panel-button {
        transition: all 0.4s ease-out;
    }
    .main-panel {
        margin-right: -420px;
    }
    .side-panel {
        width: 420px;
        right: -420px;
    }
    .side-panel #create-or-update-close-button {
        display: none;
    }
}
</style>
