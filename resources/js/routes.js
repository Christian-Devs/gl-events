let login = require('./components/auth/LoginView.vue').default;
let register = require('./components/auth/RegisterView.vue').default;
let forget = require('./components/auth/ForgetView.vue').default;
let home = require('./components/HomeView.vue').default;
let logout = require('./components/auth/LogoutView.vue').default;

let storeemployee = require('./components/employee/AddEmployeeView.vue').default;
let employee = require('./components/employee/AllEmployeesView.vue').default;
let editemployee = require('./components/employee/EditEmployeeView.vue').default;

let storesupplier = require('./components/supplier/CreateSupplier.vue').default;
let supplier = require('./components/supplier/AllSuppliers.vue').default;
let editsupplier = require('./components/supplier/EditSupplier.vue').default;

let storejobcard = require('./components/job_cards/CreateJobcard.vue').default;
let jobcards = require('./components/job_cards/AllJobcards.vue').default;
let editjobcard = require('./components/job_cards/EditJobcards.vue').default;

export const routes = [
    { path: '/', component: login, name: '/'},
    { path: '/register', component: register, name: 'register'},
    { path: '/forgotPassword', component: forget, name: 'forgotPassword'},
    { path: '/logout', component: logout, name: 'logout'},
    { path: '/home', component: home, name: 'home'},

    { path: '/employees', component: employee, name: 'employees'},
    { path: '/add-employee', component: storeemployee, name: 'add-employee'},
    { path: '/edit-employee/:id', component: editemployee, name: 'edit-employee'},

    { path: '/suppliers', component: supplier, name: 'suppliers'},
    { path: '/add-supplier', component: storesupplier, name: 'add-supplier'},
    { path: '/edit-supplier/:id', component: editsupplier, name: 'edit-supplier'},

    { path: '/jobcards', component: jobcards, name: 'jobcards'},
    { path: '/add-jobcard', component: storejobcard, name: 'add-jobcard'},
    { path: '/edit-jobcard/:id', component: editjobcard, name: 'edit-jobcard'}
];