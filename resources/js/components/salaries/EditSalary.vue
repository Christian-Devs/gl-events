<template>
    <div class="card shadow-sm p-4">
        <h4 class="mb-3">Edit Salary</h4>
        <form @submit.prevent="updateSalary">
            <div class="form-group">
                <label>Employee</label>
                <select class="form-control" v-model="form.employee_id" disabled>
                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                        {{ employee.name }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Basic Salary</label>
                <input type="number" class="form-control" v-model.number="form.basic_salary" @input="calculateNet">
            </div>

            <div class="form-group">
                <label>Bonus</label>
                <input type="number" class="form-control" v-model.number="form.bonus" @input="calculateNet">
            </div>

            <div class="form-group">
                <label>Deductions</label>
                <input type="number" class="form-control" v-model.number="form.deductions" @input="calculateNet">
            </div>

            <div class="form-group">
                <label>Net Salary</label>
                <input type="number" class="form-control" v-model.number="form.net_salary" readonly>
            </div>

            <div class="form-group">
                <label>Payment Date</label>
                <input type="date" class="form-control" v-model="form.payment_date">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select class="form-control" v-model="form.status">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                </select>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea class="form-control" rows="2" v-model="form.notes"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            employees: [],
            form: {
                employee_id: '',
                basic_salary: 0,
                bonus: 0,
                deductions: 0,
                net_salary: 0,
                payment_date: '',
                status: 'pending',
                notes: '',
            },
        };
    },
    created() {
        const id = this.$route.params.id;

        axios.get('/api/employee').then(res => {
            this.employees = res.data;
        });

        axios.get(`/api/salaries/${id}`).then(res => {
            this.form = res.data;
            this.calculateNet();
        });
    },
    methods: {
        calculateNet() {
            const basic = parseFloat(this.form.basic_salary) || 0;
            const bonus = parseFloat(this.form.bonus) || 0;
            const deductions = parseFloat(this.form.deductions) || 0;
            this.form.net_salary = (basic + bonus - deductions).toFixed(2);
        },
        updateSalary() {
            const id = this.$route.params.id;
            axios.put(`/api/salaries/${id}`, this.form)
                .then(() => {
                    this.$router.push('/salaries');
                })
                .catch(err => {
                    console.error(err);
                });
        }
    }
};
</script>
