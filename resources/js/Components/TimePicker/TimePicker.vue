<template>
    <div class="flex items-center">
        <!-- Hour -->
        <div class="time-component-container flex items-center justify-center flex-1 bg-gray-700 rounded">
            <!-- Increase hour button -->
            <div class="decrease flex-1 px-2 md:px-4 py-3 select-none" @click="decreaseHour">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20px" height="20px">
                    <path d="M0 0h24v24H0V0z" fill="none" opacity=".87" />
                    <path d="M17.51 3.87L15.73 2.1 5.84 12l9.9 9.9 1.77-1.77L9.38 12l8.13-8.13z" />
                </svg>
            </div>
            <!-- Hour control -->
            <input
                type="number"
                :value="hour"
                @input="onHourChange"
                @keydown.enter.prevent
                class="appearance-none block w-full bg-gray-700 text-white text-center rounded px-2 py-3 leading-tight focus:outline-none focus:shadow-outline"
            />
            <!-- Decrease hour button -->
            <div class="increase flex-1 px-2 md:px-4 py-3 select-none" @click="increaseHour">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20px" height="20px">
                    <path d="M24 24H0V0h24v24z" fill="none" opacity=".87" />
                    <path d="M6.49 20.13l1.77 1.77 9.9-9.9-9.9-9.9-1.77 1.77L14.62 12l-8.13 8.13z" />
                </svg>
            </div>
        </div>

        <!-- Colon Separator -->
        <div class="px-2 text-gray-600 text-xl font-bold select-none">:</div>

        <!-- Minute -->
        <div class="time-component-container flex items-center justify-center flex-1 bg-gray-700 rounded">
            <!-- Increase minute button -->
            <div class="decrease flex-1 px-2 md:px-4 py-3 select-none" @click="decreaseMinute">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20px" height="20px">
                    <path d="M0 0h24v24H0V0z" fill="none" opacity=".87" />
                    <path d="M17.51 3.87L15.73 2.1 5.84 12l9.9 9.9 1.77-1.77L9.38 12l8.13-8.13z" />
                </svg>
            </div>
            <!-- Minute control -->
            <input
                type="number"
                v-model="minute"
                @input="onMinuteChange"
                @keydown.enter.prevent
                class="appearance-none block w-full bg-gray-700 text-white text-center rounded px-2 py-3 leading-tight focus:outline-none focus:shadow-outline"
            />
            <!-- Decrease minute button -->
            <div class="increase flex-1 px-2 md:px-4 py-3 select-none" @click="increaseMinute">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20px" height="20px">
                    <path d="M24 24H0V0h24v24z" fill="none" opacity=".87" />
                    <path d="M6.49 20.13l1.77 1.77 9.9-9.9-9.9-9.9-1.77 1.77L14.62 12l-8.13 8.13z" />
                </svg>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            hour: 12,
            minute: 0,
            // Steps to increase hours in
            hourStep: 1,
            // Steps to increase minutes in
            minuteStep: 1,
        };
    },
    mounted() {
        const now = new Date();
        this.hour = now.getHours();
        this.minute = now.getMinutes();
        this.emitTime();
    },
    methods: {
        // Handle change to hour (@input event)
        onHourChange(evt) {
            this.hour = parseInt(evt.target.value);
            this.constrainHour();
            this.emitTime();
        },
        // Handle change to minute (@input event)
        onMinuteChange(evt) {
            this.minute = parseInt(evt.target.value);
            this.constrainMinute();
            this.emitTime();
        },
        decreaseHour() {
            this.hour = parseInt(this.hour);
            this.hour -= this.hourStep;
            this.constrainHour();
            this.emitTime();
        },
        increaseHour() {
            this.hour = parseInt(this.hour);
            this.hour += this.hourStep;
            this.constrainHour();
            this.emitTime();
        },
        decreaseMinute() {
            this.minute = parseInt(this.minute);
            this.minute -= this.minuteStep;
            this.constrainMinute();
            this.emitTime();
        },
        increaseMinute() {
            this.minute = parseInt(this.minute);
            this.minute += this.minuteStep;
            this.constrainMinute();
            this.emitTime();
        },
        constrainHour() {
            if (this.hour < 0) this.hour = 0;
            else if (this.hour > 23) this.hour = 23;
        },
        constrainMinute() {
            if (this.minute < 0) this.minute = 0;
            else if (this.minute >= 60) this.minute = 59;
        },
        emitTime() {
            const hour = this.padToTwoDigits(this.hour);
            const minute = this.padToTwoDigits(this.minute);
            this.$emit("input", `${hour}:${minute}`);
        },
        padToTwoDigits(num) {
            return num < 10 ? `0${num}` : num;
        },
    },
};
</script>

<style>
/* Hide number type input spinners (increment and decrement buttons) */
.time-component-container input[type="number"]::-webkit-inner-spin-button,
.time-component-container input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.time-component-container .increase,
.time-component-container .decrease {
    fill: #1a202c;
    cursor: pointer;
    opacity: 0.38;
    transition: all 0.2s ease-out;
}
.time-component-container .increase:hover,
.time-component-container .decrease:hover {
    opacity: 1;
    fill: #4fd1c5;
}
.time-component-container .increase svg,
.time-component-container .decrease svg {
    margin: 0 auto;
}
</style>
