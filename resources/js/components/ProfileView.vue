<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Employee Profile</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link to="/employees">Employees</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">{{ fullName || 'Profile' }}</li>
            </ol>
        </div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'edit' }" @click.prevent="activeTab = 'edit'"
                    href="#">Edit Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'payslips' }"
                    @click.prevent="activeTab = 'payslips'" href="#">Payslips</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'leave' }" @click.prevent="activeTab = 'leave'"
                    href="#">Leave Applications</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content p-3 bg-white border border-top-0">

            <!-- Edit Profile -->
            <div v-show="activeTab === 'edit'">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="saveProfile">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>First name</label>
                                    <input class="form-control" v-model="form.first_name">
                                    <small class="text-danger" v-if="errors.first_name">{{ errors.first_name[0]
                                    }}</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Last name</label>
                                    <input class="form-control" v-model="form.last_name">
                                    <small class="text-danger" v-if="errors.last_name">{{ errors.last_name[0] }}</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Email</label>
                                    <input type="email" class="form-control" v-model="form.email">
                                    <small class="text-danger" v-if="errors.email">{{ errors.email[0] }}</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Phone</label>
                                    <input class="form-control" v-model="form.phone">
                                    <small class="text-danger" v-if="errors.phone">{{ errors.phone[0] }}</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>ID / Passport</label>
                                    <input class="form-control" v-model="form.id_number">
                                    <small class="text-danger" v-if="errors.id_number">{{ errors.id_number[0] }}</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Birthdate</label>
                                    <input type="date" class="form-control" v-model="form.birthdate">
                                    <small class="text-danger" v-if="errors.birthdate">{{ errors.birthdate[0] }}</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Start date</label>
                                    <input type="date" class="form-control" v-model="form.start_date">
                                    <small class="text-danger" v-if="errors.start_date">{{ errors.start_date[0]
                                    }}</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Pay frequency</label>
                                    <select class="form-control" v-model="form.pay_frequency">
                                        <option value="monthly">Monthly</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="fortnightly">Fortnightly</option>
                                    </select>
                                    <small class="text-danger" v-if="errors.pay_frequency">{{ errors.pay_frequency[0]
                                    }}</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Payment method</label>
                                    <select class="form-control" v-model="form.payment_method">
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="eft_manual">EFT (manual)</option>
                                    </select>
                                    <small class="text-danger" v-if="errors.payment_method">{{ errors.payment_method[0]
                                    }}</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Status</label>
                                    <select class="form-control" v-model="form.status">
                                        <option value="active">Active</option>
                                        <option value="terminated">Terminated</option>
                                    </select>
                                    <small class="text-danger" v-if="errors.status">{{ errors.status[0] }}</small>
                                </div>
                            </div>

                            <!-- Bank fields (only when EFT manual) -->
                            <div v-if="form.payment_method === 'eft_manual'">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Bank ID</label>
                                        <input type="number" class="form-control" v-model.number="form.bank_id">
                                        <small class="text-danger" v-if="errors.bank_id">{{ errors.bank_id[0] }}</small>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Account number</label>
                                        <input class="form-control" v-model="form.bank_account_number">
                                        <small class="text-danger" v-if="errors.bank_account_number">{{
                                            errors.bank_account_number[0] }}</small>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Branch code (6)</label>
                                        <input class="form-control" maxlength="6" v-model="form.bank_branch_code"
                                            @input="form.bank_branch_code = (form.bank_branch_code || '').replace(/[^0-9]/g, '').slice(0, 6)">
                                        <small class="text-danger" v-if="errors.bank_branch_code">{{
                                            errors.bank_branch_code[0] }}</small>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Account type</label>
                                        <select class="form-control" v-model="form.bank_account_type">
                                            <option value="">—</option>
                                            <option value="1">Current (Cheque)</option>
                                            <option value="2">Savings</option>
                                            <option value="3">Transmission</option>
                                            <option value="4">Bond</option>
                                            <option value="6">Subscription Share</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Owner</label>
                                        <select class="form-control" v-model="form.bank_holder_relationship">
                                            <option value="">—</option>
                                            <option value="1">Employee</option>
                                            <option value="2">Joint</option>
                                            <option value="3">Third party</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label>Third-party holder name (if 3)</label>
                                        <input class="form-control" v-model="form.bank_holder_name">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <button class="btn btn-primary" :disabled="saving">
                                    <span v-if="saving" class="spinner-border spinner-border-sm mr-1"></span>
                                    Save
                                </button>
                                <span class="text-success ml-3" v-if="saveOk">Saved</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Payslips -->
            <div v-show="activeTab === 'payslips'">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Payslips</h5>
                            <button class="btn btn-sm btn-outline-secondary" @click="loadPayslips"
                                :disabled="loadingPayslips">
                                <span v-if="loadingPayslips" class="spinner-border spinner-border-sm mr-1"></span>
                                Refresh
                            </button>
                        </div>

                        <div v-if="payslips.length">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Period</th>
                                            <th>Gross</th>
                                            <th>Net</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ps in payslips" :key="ps.payslip_id">
                                            <td>{{ ps.period }}</td>
                                            <td>R{{ formatMoney(ps.gross) }}</td>
                                            <td>R{{ formatMoney(ps.net) }}</td>
                                            <td class="text-right">
                                                <a class="btn btn-sm btn-outline-primary"
                                                    :href="`/api/payroll/payslip/${ps.payslip_id}/pdf`" target="_blank">
                                                    PDF
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div v-else class="text-muted">
                            No payslips found yet.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Applications -->
            <div v-show="activeTab === 'leave'">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Apply for Leave</h5>
                        <form @submit.prevent="submitLeave">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Leave Type</label>
                                    <select class="form-control" v-model="leaveForm.leave_type_id">
                                        <option value="" disabled>Choose type</option>
                                        <option v-for="lt in leaveTypes" :key="lt.id" :value="lt.id">{{ lt.name }}
                                        </option>
                                    </select>
                                    <small class="text-danger" v-if="leaveErrors.leave_type_id">{{
                                        leaveErrors.leave_type_id[0] }}</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" v-model="leaveForm.start_date">
                                    <small class="text-danger" v-if="leaveErrors.start_date">{{
                                        leaveErrors.start_date[0] }}</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" v-model="leaveForm.end_date">
                                    <small class="text-danger" v-if="leaveErrors.end_date">{{ leaveErrors.end_date[0]
                                    }}</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Units (days/hours)</label>
                                    <input type="number" min="0" step="0.5" class="form-control"
                                        v-model.number="leaveForm.units">
                                    <small class="text-muted">Enter total units (e.g. days)</small>
                                    <small class="text-danger" v-if="leaveErrors.units">{{ leaveErrors.units[0]
                                    }}</small>
                                </div>
                                <div class="form-group col-md-9">
                                    <label>Reason</label>
                                    <input class="form-control" v-model="leaveForm.reason" placeholder="(optional)">
                                    <small class="text-danger" v-if="leaveErrors.reason">{{ leaveErrors.reason[0]
                                    }}</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <button class="btn btn-primary" :disabled="submittingLeave">
                                    <span v-if="submittingLeave" class="spinner-border spinner-border-sm mr-1"></span>
                                    Submit
                                </button>
                                <span v-if="leaveOk" class="text-success ml-3">Submitted</span>
                            </div>
                        </form>

                        <hr>
                        <h6 class="mb-2">Recent Leave Applications</h6>
                        <div v-if="leaveRecent.length" class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Units</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="r in leaveRecent" :key="r.id">
                                        <td>{{ r.type_name || r.leave_type_name }}</td>
                                        <td>{{ r.start_date }}</td>
                                        <td>{{ r.end_date }}</td>
                                        <td>{{ r.units }}</td>
                                        <td><span class="badge"
                                                :class="r.status === 'approved' ? 'badge-success' : (r.status === 'rejected' ? 'badge-danger' : 'badge-warning')">{{
                                                    r.status }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-muted">No recent leave applications.</div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import Notification from '../helpers/Notification.js'

export default {
    data() {
        return {
            activeTab: 'edit',
            errors: {},
            saving: false,
            saveOk: false,
            loading: false,
            loadingPayslips: false,

            employeeId: null,

            form: {
                first_name: '', last_name: '', email: '', phone: '',
                id_number: '', birthdate: '', start_date: '',
                pay_frequency: 'monthly', payment_method: 'cash', status: 'active',
                role_id: '',
                bank_id: null, bank_account_number: '', bank_branch_code: '',
                bank_account_type: '', bank_holder_relationship: '', bank_holder_name: ''
            },

            payslips: [],

            // Leave
            leaveTypes: [],
            leaveForm: { leave_type_id: '', start_date: '', end_date: '', units: null, reason: '' },
            leaveErrors: {},
            submittingLeave: false,
            leaveOk: false,
            leaveRecent: []
        }
    },
    computed: {
        fullName() {
            return `${this.form.first_name} ${this.form.last_name}`.trim()
        }
    },
    async created() {
        if (!User.loggedIn()) {
            this.$router.push({ name: '/' })
            return
        }
        this.updateTitle('Profile')
        this.loading = true
        try {
            // 1) Get the current user's employee (authorised via JWT)
            const { data } = await axios.get('/api/employee/self')
            this.employeeId = data.id;
            const emp = data;

            // 2) Populate the edit form directly from /self
            this.form = {
                first_name: emp.first_name || '',
                last_name: emp.last_name || '',
                email: emp.email || '',
                phone: emp.phone || '',
                id_number: emp.id_number || '',
                birthdate: (emp.birthdate || '').slice(0, 10),
                start_date: (emp.start_date || '').slice(0, 10),
                pay_frequency: emp.pay_frequency || 'monthly',
                payment_method: emp.payment_method || 'cash',
                status: emp.status || 'active',
                role_id: emp.role_id || '',
                bank_id: emp.bank_id || null,
                bank_account_number: emp.bank_account_number || '',
                bank_branch_code: emp.bank_branch_code || '',
                bank_account_type: emp.bank_account_type || '',
                bank_holder_relationship: emp.bank_holder_relationship || '',
                bank_holder_name: emp.bank_holder_name || ''
            }

            // 3) Load the rest in parallel
            await Promise.all([
                this.loadPayslips(),
                this.loadLeaveMeta(),
            ])

        } catch (e) {
            // 404 -> no employee
            if (e?.response?.status === 404) {
                this.$router.push({ name: 'add-employee' })
                return
            }
            if (e?.response?.status === 401) {
                this.$router.push({ name: '/' })
                return
            }
            console.error(e)
            Notification?.error?.('Failed to load profile')
        } finally {
            this.loading = false
        }
    },
    methods: {
        // ---------- Save ----------
        async saveProfile() {
            this.errors = {}
            this.saving = true
            this.saveOk = false
            try {
                await axios.patch(`/api/employee/${this.employeeId}`, this.form)
                this.saveOk = true
                Notification.success('Profile updated')
            } catch (err) {
                if (err?.response?.data?.errors) this.errors = err.response.data.errors
                else Notification.error('Failed to save profile')
                console.error(err)
            } finally {
                this.saving = false
            }
        },

        // ---------- Payslips ----------
        async loadPayslips() {
            if (!this.employeeId) return
            this.loadingPayslips = true
            this.payslips = []
            try {
                // Latest payslip (your current endpoint)
                const { data } = await axios.post('/api/payroll/payslip', { employee_id: this.employeeId })
                if (data && data.ok) {
                    this.payslips = [{
                        payslip_id: data.payslip_id,
                        period: data.period,
                        gross: data.gross,
                        net: data.net
                    }]
                }
                // If you later add a list endpoint, switch to it here.
            } catch (e) {
                console.warn('Payslips load failed', e?.response?.data || e.message)
            } finally {
                this.loadingPayslips = false
            }
        },

        // ---------- Leave ----------
        async loadLeaveMeta() {
            try {
                const { data } = await axios.get('/api/payroll/leave-types')
                this.leaveTypes = Array.isArray(data) ? data : (data?.items || [])
            } catch {
                // fallback if API isn’t ready
                this.leaveTypes = [
                    { id: 1, name: 'Annual Leave' },
                    { id: 2, name: 'Sick Leave' },
                    { id: 3, name: 'Family Responsibility' }
                ]
            }

            try {
                const { data } = await axios.get('/api/payroll/leave-applications', {
                    params: { employee_id: this.employeeId }
                })
                this.leaveRecent = Array.isArray(data) ? data : (data?.items || [])
            } catch { /* ignore */ }
        },

        async submitLeave() {
            if (!this.employeeId) return
            this.submittingLeave = true
            this.leaveErrors = {}
            this.leaveOk = false
            try {
                const payload = {
                    employee_id: this.employeeId,
                    leave_type_id: this.leaveForm.leave_type_id,
                    start_date: this.leaveForm.start_date,
                    end_date: this.leaveForm.end_date,
                    units: this.leaveForm.units,
                    reason: this.leaveForm.reason || null
                }
                await axios.post('/api/payroll/leave-applications', payload)
                this.leaveOk = true
                Notification.success('Leave application submitted')
                await this.loadLeaveMeta()
                // reset (keep selected type)
                this.leaveForm.start_date = ''
                this.leaveForm.end_date = ''
                this.leaveForm.units = null
                this.leaveForm.reason = ''
            } catch (err) {
                if (err?.response?.data?.errors) this.leaveErrors = err.response.data.errors
                else Notification.error('Failed to submit leave')
                console.error(err)
            } finally {
                this.submittingLeave = false
            }
        },

        // ---------- Utils ----------
        formatMoney(n) {
            const v = Number(n || 0)
            return v.toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
        }
    }
}
</script>
