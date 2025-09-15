import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap';
require('./bootstrap');

import Vue from 'vue';
import VueRouter from 'vue-router';
import axios from 'axios';                // <-- add this
import { routes } from './routes';
import User from './helpers/User';
import Swal from 'sweetalert2';
import Notification from './helpers/Notification';
import VTooltip from 'v-tooltip';

Vue.config.productionTip = false;
Vue.config.devtools = true;
window.Vue = Vue;

window.User = User;
window.Swal = Swal;
window.Notification = Notification;

Vue.use(VueRouter);
Vue.use(require('vue-moment'));
Vue.use(VTooltip);

const router = new VueRouter({ routes, mode: 'history' });

router.beforeEach((to, from, next) => {
    const isAuthed = User.loggedIn()
    const role = (localStorage.getItem('role') || '').toLowerCase()

    // require auth unless explicitly marked
    const requiresAuth = to.matched.some(r => r.meta?.auth !== false)

    // check roles
    const allowed = to.matched.every(r => {
        const roles = r.meta?.roles
        return !roles || roles.length === 0 || roles.map(x => String(x).toLowerCase()).includes(role)
    })

    if (requiresAuth && !isAuthed) {
        return next({ name: '/' }) // login
    }
    if (requiresAuth && !allowed) {
        return next({ name: 'home' }) // forbidden → dashboard
    }
    next()
})

const Toast = Swal.mixin({
    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
    didOpen: t => { t.onmouseenter = Swal.stopTimer; t.onmouseleave = Swal.resumeTimer; }
});
window.Toast = Toast;

/* ---------- ENSURE JWT HEADER IS SET ---------- */
(function setAuthHeaderFromStorage() {
    const token = localStorage.getItem('token');
    if (token) axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    else delete axios.defaults.headers.common['Authorization'];
})();

async function bootstrapPublicSettings() {
    try {
        const { data } = await axios.get('/api/settings/public')
        localStorage.setItem('cfg', JSON.stringify(data))
    } catch {
        localStorage.removeItem('cfg')
    }
}
bootstrapPublicSettings()

/* ---------- PRIME USER/ROLE CONTEXT ---------- */
async function bootstrapAuthContext() {
    try {
        if (!User.loggedIn()) {
            localStorage.removeItem('role');
            return;
        }

        // Role + user comes from auth endpoint (POST)
        const me = await axios.post('/api/auth/me');
        const roleName = me?.data?.user?.role?.name || me?.data?.role || '';
        if (roleName) localStorage.setItem('role', roleName);

        // (Optional) fetch employee record; requires JWT header already set
        // If you don’t need it here, you can skip this call.
        try {
            const emp = await axios.get('/api/employee/self');
            window.__employee = emp.data; // handy global if you want
        } catch (e) {
            // 401/404 just means no employee yet or not linked; ignore here
        }
    } catch {
        // token invalid or expired
        localStorage.removeItem('role');
    }
}
bootstrapAuthContext();

/* ---------- GLOBAL HELPERS (for Blade + Vue) ---------- */
Vue.mixin({
    methods: {
        userName() {
            // Primary: what your AppStorage writes now
            const u = localStorage.getItem('user')
            if (u) return u

            // Fallbacks (older code paths)
            if (window.User?.name) return window.User.name()
            return ''
        },
        role() {
            return localStorage.getItem('role') || ''
        },
        can(roles) {
            if (!roles || roles.length === 0) return true
            const r = (localStorage.getItem('role') || '').toLowerCase()
            return roles.map(x => String(x).toLowerCase()).includes(r)
        },
        cfg(key, def = '') {
            try {
                const o = JSON.parse(localStorage.getItem('cfg') || '{}')
                return (key ? o[key] : o) ?? def
            } catch { return def }
        },
        async logout() {
            try { await axios.post('/api/auth/logout') } catch { }
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            localStorage.removeItem('role')
            Notification?.success?.('Logged out')
            this.$router.push({ name: '/' })
            document.getElementById('sidebar')?.setAttribute('style', 'display:none')
            document.getElementById('topbar')?.setAttribute('style', 'display:none')
        },
        updateTitle(extra = '') {
            const cfg = JSON.parse(localStorage.getItem('cfg') || '{}')
            document.title = `${extra ? extra + ' - ' : ''}${cfg.company_name || 'BMS Tool'}`
        }
    }
});

new Vue({
    router, mounted() {
        const cfg = JSON.parse(localStorage.getItem('cfg') || '{}')
        document.title = cfg.company_name || 'BMS Tool'
    }
}).$mount('#app');
