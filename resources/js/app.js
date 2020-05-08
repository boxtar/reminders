import Vue from "vue";

Vue.component("reminderList", require("./Components/ReminderList.vue").default);
// Vue.component("updateReminder", require('./Components/UpdateReminder.vue').default);
// Vue.component("addReminder", require('./Components/AddReminder.vue').default);
Vue.component("createOrUpdateReminder", require("./Components/CreateOrUpdateReminder.vue").default);
Vue.component("datePicker", require("./Components/DatePicker/DatePicker.vue").default);
Vue.component("timePicker", require("./Components/TimePicker/TimePicker.vue").default);
Vue.component("register", require("./Components/Register.vue").default);
Vue.component("login", require("./Components/Login.vue").default);

Vue.component("notifications", require("./Components/Notifications/NotificationsList.vue").default);

new Vue({
    el: "#app",
    data: {
        //
    },
    methods: {
        //
    },
});
