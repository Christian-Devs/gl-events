<template>
    <div>
        <div class="row">
            <router-link to="/add-quote" class="btn btn-primary">Add Quote</router-link>
        </div>

        <br />
        <input class="form-control" type="text" v-model="search" placeholder="Search Quotes" style="width: 25%;" />
        <br />

        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Quotes List</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Client Name</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="quote in filteredQuotes" :key="quote.id">
                                    <td>{{ quote.client_name }}</td>
                                    <td>{{ quote.quote_date }}</td>
                                    <td>R{{ parseFloat(quote.total).toFixed(2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <select v-model="quote.status" @change="updateStatus(quote)"
                                                class="form-control form-control-sm mr-2">
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                            <span :class="['badge', statusClass(quote.status)]">{{ quote.status
                                                }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <router-link :to="{name: 'edit-quote', params:{id:quote.id}}"
                                            class="btn btn-sm btn-primary ml-1">Edit</router-link>
                                        <button @click="deleteQuote(quote.id)"
                                            class="btn btn-sm btn-danger ml-1">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            quotes: [],
            search: ''
        };
    },
    computed: {
        filteredQuotes() {
            return this.quotes.filter(q =>
                q.client_name.toLowerCase().includes(this.search.toLowerCase()) ||
                q.client_email?.toLowerCase().includes(this.search.toLowerCase()) ||
                q.quote_date.includes(this.search)
            );
        }
    },
    methods: {
        loadQuotes() {
            axios.get('/api/quotes')
                .then(({ data }) => (this.quotes = data))
                .catch(err => console.error('Failed to load quotes:', err));
        },
        updateStatus(quote) {
            axios.put(`/api/quotes/${quote.id}`, { status: quote.status })
                .then(() => alert('Status updated'))
                .catch(() => alert('Failed to update status'));
        },
        statusClass(status) {
            switch (status) {
                case 'approved':
                    return 'badge-success';
                case 'rejected':
                    return 'badge-danger';
                case 'pending':
                default:
                    return 'badge-secondary';
            }
        },
        deleteQuote(id) {
            if (confirm('Are you sure you want to delete this quote?')) {
                axios.delete(`/api/quotes/${id}`)
                    .then(() => {
                        this.quotes = this.quotes.filter(q => q.id !== id);
                    })
                    .catch(() => alert('Failed to delete quote.'));
            }
        }

    },
    created() {
        this.loadQuotes();
    }
};
</script>

<style scoped>
select {
    padding: 3px;
    border-radius: 5px;
    min-width: 100px;
}
</style>
