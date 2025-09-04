import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap';

require("./bootstrap");

import Vue from "vue";
import VueRouter from "vue-router";
import { routes } from "./routes";
import User from "./helpers/User";
import Swal from "sweetalert2";
import Notification from "./helpers/Notification";
import CanvasJS from '@canvasjs/vue-charts'
import VTooltip from 'v-tooltip'

// Make sure devtools can see Vue (Vue 2)
Vue.config.productionTip = false;
Vue.config.devtools = true;
window.Vue = Vue;


window.User = User;
window.Swal = Swal;
window.Notification = Notification;

Vue.use(VueRouter);
Vue.use(CanvasJS);
Vue.use(require('vue-moment'));
Vue.use(VTooltip);

const router = new VueRouter({
    routes,
    mode: "history",
});

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

window.Toast = Toast;

const app = new Vue({
    router,
}).$mount("#app");
