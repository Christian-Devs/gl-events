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

/* ---------- AUTO-LOGOUT WITH WARNING POPUP ---------- *
 * Insert this near the end of app.js (before new Vue(...).$mount('#app'))
 * Uses: axios, Swal (sweetalert2), Toast (optional)
 * Call startAutoLogout(minutes) to start (the snippet calls with 60 min by default)
 ********************************************************/

/* ---------- AUTO-LOGOUT WITH INACTIVITY GRACE PERIOD ---------- */
(function () {
    const INACTIVITY_BEFORE_START_MINUTES = 5; // start watcher after 5 minutes of inactivity
    const AUTOLOGOUT_MINUTES = 60;             // total inactivity minutes before logout
    const WARNING_SECONDS = 60;                // seconds before logout to show warning
    const POLL_INTERVAL_MS = 10_000;           // check every 10 seconds

    let lastActivityAt = Date.now();
    let pollIntervalId = null;
    let warningOpen = false;
    let warningTimerInterval = null;

    function refreshLastActivity() { lastActivityAt = Date.now(); }

    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(evt =>
        window.addEventListener(evt, refreshLastActivity, { passive: true })
    );

    async function performLogout(reason = 'Logged out due to inactivity') {
        try { await axios.post('/api/auth/logout'); } catch (e) { }
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        localStorage.removeItem('role');
        delete axios.defaults.headers.common['Authorization'];

        if (window.Toast) {
            try { Toast.fire({ icon: 'info', title: reason }) } catch (e) { }
        } else {
            alert(reason);
        }
        window.location.href = '/';
    }

    function showWarningModal(secondsLeft, onStay, onLogout) {
        if (warningOpen) return;
        warningOpen = true;
        const htmlId = 'auto-logout-countdown';
        const html = `<div>You're about to be logged out due to inactivity.<br/>Signing out in <strong id="${htmlId}">${secondsLeft}</strong> seconds.</div>`;

        Swal.fire({
            title: 'Still there?',
            html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Stay signed in',
            cancelButtonText: 'Logout now',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCloseButton: false,
            didOpen: () => {
                let secs = secondsLeft;
                const elCounter = document.getElementById(htmlId);
                warningTimerInterval = setInterval(() => {
                    secs -= 1;
                    if (elCounter) elCounter.textContent = String(secs);
                    if (secs <= 0) {
                        clearInterval(warningTimerInterval);
                        warningTimerInterval = null;
                        try { Swal.close(); } catch { }
                        warningOpen = false;
                        onLogout();
                    }
                }, 1000);
            },
            willClose: () => {
                if (warningTimerInterval) { clearInterval(warningTimerInterval); warningTimerInterval = null; }
                warningOpen = false;
            }
        }).then(result => {
            if (result.isConfirmed) onStay();
            else if (result.dismiss === Swal.DismissReason.cancel) onLogout();
        }).catch(() => { warningOpen = false; });
    }

    function startAutoLogoutWithGrace(inactivityBeforeStartMinutes = INACTIVITY_BEFORE_START_MINUTES,
        totalMinutes = AUTOLOGOUT_MINUTES,
        warningSeconds = WARNING_SECONDS) {
        if (pollIntervalId) clearInterval(pollIntervalId);
        lastActivityAt = Date.now();

        pollIntervalId = setInterval(async () => {
            const token = localStorage.getItem('token');
            if (!token) return;

            const idleMs = Date.now() - lastActivityAt;
            const idleMinutes = idleMs / 1000 / 60;

            // only start full auto-logout countdown after initial grace period
            if (idleMinutes < inactivityBeforeStartMinutes) return;

            const timeoutMs = totalMinutes * 60 * 1000;
            const timeLeftMs = timeoutMs - idleMs;

            if (timeLeftMs <= 0) {
                clearInterval(pollIntervalId);
                await performLogout('You were logged out due to inactivity.');
                return;
            }

            if (timeLeftMs <= warningSeconds * 1000 && !warningOpen) {
                const secsLeft = Math.ceil(timeLeftMs / 1000);
                showWarningModal(secsLeft,
                    () => { refreshLastActivity(); if (!pollIntervalId) startAutoLogoutWithGrace(inactivityBeforeStartMinutes, totalMinutes, warningSeconds); },
                    async () => { clearInterval(pollIntervalId); await performLogout('You were logged out due to inactivity.'); }
                );
            }
        }, POLL_INTERVAL_MS);
    }

    window.resetAutoLogoutActivity = refreshLastActivity;
    window.startAutoLogout = startAutoLogoutWithGrace;
    window.stopAutoLogout = () => { if (pollIntervalId) clearInterval(pollIntervalId); };

    // optionally, start automatically (or call after login)
    startAutoLogoutWithGrace(INACTIVITY_BEFORE_START_MINUTES, AUTOLOGOUT_MINUTES, WARNING_SECONDS);
})();




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
