import Vue from "vue";

Vue.component("addReminder", require('./Components/AddReminder.vue').default);
Vue.component("register", require('./Components/Register.vue').default);
Vue.component("login", require('./Components/Login.vue').default);

new Vue({
    el: "#app",
    data: {
        states: {
            UNLOADED: 0,
            LOADING: 1,
            LOADED: 2,
            ERROR: 9,
        },
        reminders: {
            data: [],
            state: false,
        },
    },
    methods: {
        init() {
            this.reminders.state = this.states.UNLOADED;
        },
    },
    // async mounted() {
    //     this.init();
    //     const response = await fetch("/api/reminders");
    //     if (!response.ok) {
    //         this.reminders.state = this.states.ERROR;
    //     } else {
    //         this.reminders.state = this.states.LOADING;
    //         this.reminders.data = await response.json();
    //         this.reminders.state = this.states.LOADED;
    //     }
    // },
});
