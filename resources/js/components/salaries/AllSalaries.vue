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
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="salary in salaries" :key="salary.id">
                        <td>{{ salary.employee.name }}</td>
                        <td>R {{ parseFloat(salary.basic_salary).toFixed(2) }}</td>
                        <td>R {{ parseFloat(salary.bonus || 0).toFixed(2) }}</td>
                        <td>R {{ parseFloat(salary.deductions || 0).toFixed(2) }}</td>
                        <td><strong>R {{ parseFloat(salary.net_salary).toFixed(2) }}</strong></td>
                        <td>{{ salary.payment_date }}</td>
                        <td>
                            <span class="badge" :class="{
                                'badge-success': salary.status === 'paid',
                                'badge-warning': salary.status === 'pending'
                            }">
                                {{ salary.status }}
                            </span>
                        </td>
                        <td>{{ salary.notes || '-' }}</td>
                        <td>
                            <router-link :to="`/edit-salary/${salary.id}`" class="btn btn-sm btn-info" title="Edit">
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
    </div>
</template>

<script>
export default {
    data() {
        return {
            salaries: []
        };
    },
    created() {
        this.fetchSalaries();
    },
    methods: {
        fetchSalaries() {
            axios.get('/api/salaries')
                .then(res => {
                    this.salaries = res.data;
                });
        },
        deleteSalary(id) {
            if (confirm('Are you sure you want to delete this salary record?')) {
                axios.delete(`/api/salaries/${id}`)
                    .then(() => this.fetchSalaries());
            }
        }
    }
}
</script>
