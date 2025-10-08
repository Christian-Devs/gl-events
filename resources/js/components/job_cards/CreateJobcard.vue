<template>
    <div>
        <div class="row">
            <router-link to="/jobcards" class="btn btn-primary">All Job Cards</router-link>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Create Job Card</h1>
                                    </div>
                                    <form class="user" @submit.prevent="createJobCard">
                                        <!-- Quote Reference (disabled input for display only) -->
                                        <div class="form-group">
                                            <input type="text" class="form-control" :value="quoteRef" disabled />
                                        </div>

                                        <!-- Assigned To -->
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <label for="assigned_to">Assigned To</label>
                                                    <input type="text" class="form-control" placeholder="Assigned To"
                                                        v-model="form.assigned_to">
                                                    <small class="text-danger" v-if="errors.assigned_to">{{
                                                        errors.assigned_to[0] }}</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="status">Status</label>
                                                    <select class="form-control" v-model="form.status">
                                                        <option value="open">Open</option>
                                                        <option value="in-progress">In Progress</option>
                                                        <option value="completed">Completed</option>
                                                        <option value="cancelled">Cancelled</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.status">{{ errors.status[0]
                                                    }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Start and Due Dates -->
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <label for="start_date">Start Date</label>
                                                    <input type="date" class="form-control" placeholder="Start Date"
                                                        v-model="form.start_date">
                                                    <small class="text-danger" v-if="errors.start_date">{{
                                                        errors.start_date[0] }}</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="due_date">Due Date</label>
                                                    <input type="date" class="form-control" placeholder="Due Date"
                                                        v-model="form.due_date">
                                                    <small class="text-danger" v-if="errors.due_date">{{
                                                        errors.due_date[0] }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notes -->
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control" rows="4" placeholder="Notes..."
                                                v-model="form.notes"></textarea>
                                            <small class="text-danger" v-if="errors.notes">{{ errors.notes[0] }}</small>
                                        </div>

                                        <!-- Submit -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block">Save Job
                                                Card</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                quote_id: this.$route.params.quoteId,
                assigned_to: '',
                status: 'open',
                start_date: '',
                due_date: '',
                notes: ''
            },
            quoteRef: '',
            errors: {}
        };
    },
    created() {
        // Optional: fetch quote info for better reference label
        axios.get(`/api/quotes/${this.form.quote_id}`)
            .then(res => {
                this.quoteRef = `Quote #${res.data.id} - ${res.data.client_name}`;
            })
            .catch(() => {
                this.quoteRef = `Quote ID ${this.form.quote_id}`;
            });
    },
    methods: {
        createJobCard() {
            axios.post('/api/jobcard', this.form)
                .then(() => {
                    this.$router.push({ name: 'jobcards' });
                })
                .catch(err => {
                    this.errors = err.response?.data?.errors || {};
                });
        }
    }
};
</script>
