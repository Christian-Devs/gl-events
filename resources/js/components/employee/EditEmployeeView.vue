<template>
    <div>
        <div class="row mb-3">
            <router-link to="/employees" class="btn btn-primary">All Employees</router-link>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form p-4">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Edit Employee</h1>
                                    </div>

                                    <form class="user" @submit.prevent="employeeUpdate">
                                        <!-- Names -->
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" placeholder="First name"
                                                        v-model="form.first_name" />
                                                    <small class="text-danger" v-if="errors.first_name">{{
                                                        errors.first_name[0] }}</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" placeholder="Last name"
                                                        v-model="form.last_name" />
                                                    <small class="text-danger" v-if="errors.last_name">{{
                                                        errors.last_name[0] }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Contact -->
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <input type="email" class="form-control" placeholder="Email"
                                                        v-model="form.email" />
                                                    <small class="text-danger" v-if="errors.email">{{ errors.email[0]
                                                    }}</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" placeholder="Phone"
                                                        v-model="form.phone" />
                                                    <small class="text-danger" v-if="errors.phone">{{ errors.phone[0]
                                                    }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Identity + dates -->
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control"
                                                        placeholder="ID / Passport number" v-model="form.id_number" />
                                                    <small class="text-danger" v-if="errors.id_number">{{
                                                        errors.id_number[0] }}</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="date" class="form-control" placeholder="Birthdate"
                                                        v-model="form.birthdate" />
                                                    <small class="text-danger" v-if="errors.birthdate">{{
                                                        errors.birthdate[0] }}</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="date" class="form-control" placeholder="Start date"
                                                        v-model="form.start_date" />
                                                    <small class="text-danger" v-if="errors.start_date">{{
                                                        errors.start_date[0] }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payroll + status -->
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-4">
                                                    <select class="form-control" v-model="form.pay_frequency">
                                                        <option disabled value="">Pay frequency</option>
                                                        <option value="monthly">Monthly</option>
                                                        <option value="fortnightly">Fortnightly</option>
                                                        <option value="weekly">Weekly</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.pay_frequency">{{
                                                        errors.pay_frequency[0] }}</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-control" v-model="form.payment_method">
                                                        <option disabled value="">Payment method</option>
                                                        <option value="cash">Cash</option>
                                                        <option value="cheque">Cheque</option>
                                                        <option value="eft_manual">EFT (manual)</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.payment_method">{{
                                                        errors.payment_method[0] }}</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-control" v-model="form.status">
                                                        <option disabled value="">Employment status</option>
                                                        <option value="active">Active</option>
                                                        <option value="terminated">Terminated</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.status">{{ errors.status[0]
                                                    }}</small>
                                                </div>
                                            </div>

                                            <!-- Bank (conditional) -->
                                            <div v-if="form.payment_method === 'eft_manual'" class="col-md-12 mt-3">
                                                <div class="form-row">
                                                    <div class="col-md-3">
                                                        <label>Bank ID <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control" placeholder="Bank ID"
                                                            v-model.number="form.bank_id" />
                                                        <small class="text-danger" v-if="errors.bank_id">{{
                                                            errors.bank_id[0] }}</small>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control"
                                                            placeholder="Account number"
                                                            v-model="form.bank_account_number" />
                                                        <small class="text-danger" v-if="errors.bank_account_number">{{
                                                            errors.bank_account_number[0] }}</small>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control"
                                                            placeholder="Branch code (6 digits)"
                                                            v-model="form.bank_branch_code" maxlength="6"
                                                            @input="form.bank_branch_code = (form.bank_branch_code || '').replace(/[^0-9]/g, '').slice(0, 6)" />
                                                        <small class="text-danger" v-if="errors.bank_branch_code">{{
                                                            errors.bank_branch_code[0] }}</small>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-control" v-model="form.bank_account_type">
                                                            <option value="">Account type (optional)</option>
                                                            <option value="1">Current (Cheque)</option>
                                                            <option value="2">Savings</option>
                                                            <option value="3">Transmission</option>
                                                            <option value="4">Bond</option>
                                                            <option value="6">Subscription Share</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-row mt-2">
                                                    <div class="col-md-3">
                                                        <select class="form-control"
                                                            v-model="form.bank_holder_relationship">
                                                            <option value="">Owner (optional)</option>
                                                            <option value="1">Employee</option>
                                                            <option value="2">Joint</option>
                                                            <option value="3">Third party</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control"
                                                            placeholder="Third-party holder name (if 3)"
                                                            v-model="form.bank_holder_name" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Role -->
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <select class="form-control" v-model="form.role_id">
                                                        <option value="" disabled>Select a role</option>
                                                        <option v-for="role in roles" :key="role.id" :value="role.id">
                                                            {{ role.label || role.name }}
                                                        </option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.role_id">{{
                                                        errors.role_id[0] }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block"
                                                :disabled="submitting">
                                                {{ submitting ? 'Saving…' : 'Update Employee' }}
                                            </button>
                                        </div>
                                    </form>

                                    <div v-if="errorText" class="alert alert-danger mt-2">{{ errorText }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- card -->
            </div>
        </div>
    </div>
</template>

<script>
import Notification from '../../helpers/Notification'

export default {
    data() {
        return {
            roles: [],
            errors: {},
            errorText: '',
            submitting: false,
            form: {
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                id_number: '',
                birthdate: '',
                start_date: '',
                pay_frequency: 'monthly',
                payment_method: 'cash',
                status: 'active',
                role_id: '',
                user_id: '',

                // bank fields
                bank_id: null,
                bank_account_number: '',
                bank_branch_code: '',
                bank_account_type: '',
                bank_holder_relationship: '',
                bank_holder_name: '',
            }
        }
    },

    async created() {
        if (!User.loggedIn()) {
            this.$router.push({ name: '/' })
            return
        }
        const id = this.$route.params.id

        try {
            const rolesRes = await axios.get('/api/roles')
            this.roles = rolesRes.data || []
        } catch (e) {
            /* non-fatal */
        }

        try {
            const { data } = await axios.get(`/api/employee/${id}`)
            // Normalize dates and hydrate bank fields safely
            const toDate = v => (v ? String(v).slice(0, 10) : '')
            this.form = {
                first_name: data.first_name || '',
                last_name: data.last_name || '',
                email: data.email || '',
                phone: data.phone || '',
                id_number: data.id_number || '',
                birthdate: toDate(data.birthdate),
                start_date: toDate(data.start_date || data.joining_date),
                pay_frequency: data.pay_frequency || 'monthly',
                payment_method: data.payment_method || 'cash',
                status: data.status || 'active',
                role_id: data.role_id || '',
                user_id: data.user_id || '',

                bank_id: data.bank_id ?? null,
                bank_account_number: data.bank_account_number || '',
                bank_branch_code: data.bank_branch_code || '',
                bank_account_type: data.bank_account_type || '',
                bank_holder_relationship: data.bank_holder_relationship || '',
                bank_holder_name: data.bank_holder_name || '',
            }
        } catch (e) {
            this.errorText = 'Failed to load employee'
            console.error(e)
        }
    },

    methods: {
        async employeeUpdate() {
            this.errors = {}
            this.errorText = ''
            this.submitting = true
            const id = this.$route.params.id
            try {
                await axios.patch(`/api/employee/${id}`, this.form)
                Notification.success('Employee updated')
                this.$router.push({ name: 'employees' })
            } catch (error) {
                if (error?.response?.data?.errors) {
                    this.errors = error.response.data.errors
                } else {
                    this.errorText = error?.response?.data?.message || 'An unexpected error occurred'
                    console.error(error)
                }
            } finally {
                this.submitting = false
            }
        }
    }
}
</script>
