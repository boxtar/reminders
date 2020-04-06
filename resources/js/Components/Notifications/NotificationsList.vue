<template>
    <div class="px-2 pt-2 fixed top-0 right-0 w-full z-50 lg:w-1/3">
        <div
            v-for="notification in data"
            :key="notification.id"
            @click="$emit('closeNotification', notification.id)"
            :class="
                `pl-8 py-4 mb-2 rounded flex items-center justify-between cursor-pointer ${notification.type.text} ${notification.type.bg}`
            "
        >
            <!-- Notification Text -->
            <div class="flex items-center tracking-tight">
                <span v-html="notification.text"></span>
            </div>

            <!-- Close icon / Timer -->
            <div
                v-if="notification.sticky"
                class="px-8 py-4 -my-4 text-2xl cursor-pointer"
                @click="$emit('closeNotification', notification.id)"
            >
                &cross;
            </div>
            <div v-else class="px-8 py-4 -my-4 cursor-pointer">
                <div class="w-5">
                    <svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
                        <circle
                            fill="none"
                            cx="25"
                            cy="25"
                            :r="radius"
                            stroke-width="6px"
                            stroke="white"
                            :stroke-dasharray="`${percentageRemaining(notification)} ${circumference}`"
                            style="transition: all 1s linear;"
                        />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["data"],
    data() {
        return {
            radius: 22,
        };
    },
    methods: {
        percentageRemaining({ timeElapsed, lifetime }) {
            return this.circumference - (timeElapsed / lifetime) * this.circumference;
        },
    },
    computed: {
        circumference() {
            return 2 * Math.PI * this.radius;
        },
    },
};
</script>

<style></style>
