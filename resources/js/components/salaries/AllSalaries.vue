<template>
    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">All Salaries</h4>
            <router-link to="/salaries/create" class="btn btn-primary btn-sm">+ Add Salary</router-link>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Basic</th>
                        <th>Bonus</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                        <th style="min-width:260px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="salary in salaries" :key="salary.id">
                        <td>{{ salary.employee.name }}</td>
                        <td>{{ money(salary.basic_salary) }}</td>
                        <td>{{ money(salary.bonus || 0) }}</td>
                        <td>{{ money(salary.deductions || 0) }}</td>
                        <td><strong>{{ money(salary.net_salary) }}</strong></td>
                        <td>{{ salary.payment_date }}</td>
                        <td>
                            <span class="badge" :class="{
                                'badge-success': salary.status === 'paid',
                                'badge-warning': salary.status === 'pending'
                            }">
                                {{ salary.status }}
                            </span>
                        </td>
                        <td class="d-flex flex-wrap gap-1">
                            <!-- Sync to SimplePay -->
                            <button class="btn btn-sm btn-outline-secondary" :disabled="loading[salary.employee.id]"
                                @click="syncToSimplePay(salary.employee.id)">
                                <span v-if="loading[salary.employee.id]">Syncing…</span>
                                <span v-else>Sync</span>
                            </button>

                            <!-- Preview payslip -->
                            <button class="btn btn-sm btn-outline-primary ml-1"
                                :disabled="loadingPreview[salary.employee.id]"
                                @click="previewPayslip(salary.employee.id)">
                                <span v-if="loadingPreview[salary.employee.id]">Loading…</span>
                                <span v-else>Preview Payslip</span>
                            </button>

                            <!-- Open PDF (enabled after preview) -->
                            <a v-if="payslips[salary.employee.id]?.payslip_id"
                                class="btn btn-sm btn-outline-success ml-1"
                                :href="`/api/payroll/payslip/${payslips[salary.employee.id].payslip_id}/pdf`"
                                target="_blank" rel="noopener">
                                PDF
                            </a>

                            <!-- Edit / Delete you already had -->
                            <router-link :to="`/edit-salary/${salary.id}`" class="btn btn-sm btn-info ml-1"
                                title="Edit">
                                ✏️
                            </router-link>
                            <button @click="deleteSalary(salary.id)" class="btn btn-sm btn-danger ml-1" title="Delete">
                                🗑️
                            </button>
                        </td>
                    </tr>
                    <tr v-if="salaries.length === 0">
                        <td colspan="9" class="text-center text-muted">No salaries found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Simple toast/error area -->
        <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
    </div>
</template>

<script>
// If axios is global in your app, you can omit this import.
// import axios from 'axios'
export default {
    data() {
        return {
            salaries: [],
            payslips: {},            // { [employeeId]: { payslip_id, period, net, deductions, ... } }
            loading: {},             // { [employeeId]: boolean } for sync
            loadingPreview: {},      // { [employeeId]: boolean } for preview
            error: null
        }
    },
    created() {
        this.fetchSalaries()
    },
    methods: {
        fetchSalaries() {
            this.error = null
            axios.get('/api/salaries')
                .then(res => { this.salaries = res.data || [] })
                .catch(err => { this.error = this.errMsg(err) })

                console.log(this.salaries);
        },
        deleteSalary(id) {
            if (confirm('Are you sure you want to delete this salary record?')) {
                axios.delete(`/api/salaries/${id}`)
                    .then(() => this.fetchSalaries())
                    .catch(err => { this.error = this.errMsg(err) })
            }
        },

        // ----- SimplePay actions -----
        async syncToSimplePay(employeeId) {
            this.$set(this.loading, employeeId, true)
            this.error = null
            try {
                await axios.post('/api/payroll/simplepay/employee/sync', { employee_id: employeeId })
                // optional toast
                // this.$toast?.success?.('Synced with SimplePay')
            } catch (e) {
                this.error = this.errMsg(e)
            } finally {
                this.$set(this.loading, employeeId, false)
            }
        },

        async previewPayslip(employeeId) {
            this.$set(this.loadingPreview, employeeId, true)
            this.error = null
            try {
                const { data } = await axios.get('/api/payroll/payslip', { params: { employee_id: employeeId } })
                if (data && data.ok !== false) {
                    this.$set(this.payslips, employeeId, data)
                } else {
                    this.error = data?.error || 'Payslip not available'
                }
            } catch (e) {
                this.error = this.errMsg(e)
            } finally {
                this.$set(this.loadingPreview, employeeId, false)
            }
        },

        // Helpers
        money(v) {
            const n = Number(v || 0)
            return new Intl.NumberFormat('en-ZA', { style: 'currency', currency: 'ZAR' }).format(n)
        },
        errMsg(e) {
            return e?.response?.data?.error || e?.message || 'Something went wrong'
        }
    }
}
</script>
