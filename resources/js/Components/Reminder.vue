<template>
    <div class="relative reminder-card h-full shadow bg-white flex flex-col justify-between overflow-hidden">
        <!-- Body and other Info -->
        <div class="px-6 pt-4 pb-10">
            <!-- Indicator (has initial reminder run?) -->
            <div class="absolute p-4 top-0 right-0">
                <div class="relative h-8 w-8">
                    <div class="indicator-dot absolute">
                        <div
                            class="rounded-full"
                            :class="{
                                'bg-red-400': reminder.initial_reminder_run && !reminder.is_recurring,
                                'bg-green-400': !reminder.initial_reminder_run,
                            }"
                            style="width: 10px;height: 10px;"
                        ></div>
                    </div>
                    <div class="absolute" v-show="reminder.initial_reminder_run && reminder.is_recurring">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            class="text-blue-400 fill-current"
                            width="24px"
                            height="24px"
                        >
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path
                                d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="px-4 w-full">
                <span class="hidden mt-2 text-xs uppercase text-gray-500">Body</span>
                <span class="text-gray-800 font-bold">{{ reminder.body }}</span>
            </div>

            <!-- Reminder and Recurrence Information -->
            <div class="w-full mt-1">
                <div class="md:flex flex-wrap">
                    <div class="px-4">
                        <!-- Initial Reminder -->
                        <div class="flex items-center">
                            <span class="block text-xs">
                                <span class="text-gray-500">{{
                                    `${reminder.day.substr(0, 3)} ${reminder.date} ${reminder.month.substr(
                                        0,
                                        3
                                    )} ${reminder.year} at ${pad(reminder.hour || 0)}:${pad(
                                        reminder.minute || 0
                                    )}`
                                }}</span>
                            </span>
                        </div>

                        <!-- Recurrence -->
                        <div class="flex items-center">
                            <span class="block  text-xs">
                                <span class="text-gray-500">{{ reminder.frequency }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Options Dialog -->
        <div
            class="options-dialog relative bg-gray-800 w-full"
            :class="{ 'is-active': optionsOpen }"
            @click.stop.prevent="toggleOptionsDialog"
        >
            <!-- Toggle -->
            <div
                class="toggle-container absolute w-16 h-16 flex items-start justify-center bg-gray-800 rounded-full"
                @click.stop.prevent="toggleOptionsDialog"
            >
                <button
                    @click.stop.prevent="toggleOptionsDialog"
                    class="toggle-button mt-1 rounded-full focus:outline-none focus:shadow-outline"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        class="fill-current inline text-white"
                        width="28px"
                        height="28px"
                    >
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M12 8l-6 6 1.41 1.41L12 10.83l4.59 4.58L18 14l-6-6z" />
                    </svg>
                </button>
            </div>

            <!-- Actions -->
            <div
                class="actions flex h-full overflow-hidden opacity-100"
                :class="{ 'hidden opacity-0': !optionsOpen }"
            >
                <!-- Edit -->
                <div class="">
                    <button
                        class="action-button h-full flex items-center justify-center hover:bg-teal-500 focus:bg-teal-500  focus:outline-none focus:shadow-outline"
                        @click.stop.prevent="$emit('setReminderToBeUpdated', reminder)"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            class="fill-current text-white"
                            width="24px"
                            height="24px"
                        >
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path
                                d="M14.06 9.02l.92.92L5.92 19H5v-.92l9.06-9.06M17.66 3c-.25 0-.51.1-.7.29l-1.83 1.83 3.75 3.75 1.83-1.83c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.2-.2-.45-.29-.71-.29zm-3.6 3.19L3 17.25V21h3.75L17.81 9.94l-3.75-3.75z"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Archive -->
                <div class="">
                    <button
                        class="action-button h-full flex items-center justify-center hover:bg-red-600 focus:bg-red-600 focus:outline-none focus:shadow-outline"
                        @click="$emit('archiveReminder', reminder)"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            class="fill-current text-white"
                            width="24px"
                            height="24px"
                        >
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path
                                d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-3.5l-1-1zM18 7H6v12c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7z"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["reminder"],
    data() {
        return {
            optionsOpen: false,
        };
    },
    methods: {
        toggleOptionsDialog() {
            this.optionsOpen = !this.optionsOpen;
        },
        // Pad a number to 2 digits
        pad(number) {
            if (number < 10) return "0" + number.toString();
            else return number;
        },
    },
};
</script>

<style scoped>
.reminder-card {
    border-radius: 1rem;
}
.reminder-card span {
    transition: color 0.1s ease-out;
}
.indicator-dot {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.options-dialog {
    border-radius: 0 0 1rem 1rem;
    height: 6px;
    transition: height 0.4s ease-out, background-color 0.2s;
}
.options-dialog.is-active {
    height: 2.75rem;
}
.options-dialog:hover,
.options-dialog.is-active {
    background-color: #3182ce;
}
.options-dialog:hover .toggle-container,
.options-dialog.is-active .toggle-container {
    background-color: #3182ce;
}
.options-dialog .toggle-container {
    top: 0;
    left: 50%;
    transform: translate(-50%, -45%);
    transition: background-color 0.2s;
}
.options-dialog .toggle-container .toggle-button svg {
    transition: transform 0.4s ease-out;
}
.options-dialog.is-active .toggle-container .toggle-button svg {
    transform: rotate(180deg);
}
.options-dialog .toggle-container .toggle-button:focus svg,
.options-dialog .toggle-container .toggle-button:hover svg {
    color: white;
}
.actions {
    transition: all 0.4s;
}
.actions .action-button {
    width: 3rem;
}
</style>
