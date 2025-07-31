<template>
    <div>
        <div class="row mb-3">
            <router-link to="/payments/create" class="btn btn-primary">Add Payment</router-link>
        </div>

        <div class="form-group">
            <input class="form-control" type="text" v-model="search" placeholder="Search by method, status or amount..."
                style="width: 30%;">
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Payment Records</h6>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in filteredPayments" :key="payment.id">
                                    <td>
                                        <router-link v-if="payment.invoice"
                                            :to="{ name: 'edit-invoice', params: { id: payment.invoice.id } }">
                                            {{ payment.invoice.invoice_number }}
                                        </router-link>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                    <td>R {{ parseFloat(payment.amount).toFixed(2) }}</td>
                                    <td>{{ payment.payment_date }}</td>
                                    <td>{{ payment.payment_method || 'N/A' }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'badge-success': payment.status === 'paid',
                                            'badge-warning': payment.status === 'pending'
                                        }">
                                            {{ payment.status }}
                                        </span>
                                    </td>
                                    <td>{{ payment.notes || '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer text-muted">
                        Total Payments: {{ payments.length }}
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
            payments: [],
            search: ''
        };
    },
    computed: {
        filteredPayments() {
            return this.payments.filter(p => {
                const search = this.search.toLowerCase();
                return (
                    (p.payment_method && p.payment_method.toLowerCase().includes(search)) ||
                    (p.status && p.status.toLowerCase().includes(search)) ||
                    String(p.amount).includes(search)
                );
            });
        }
    },
    created() {
        this.fetchPayments();
    },
    methods: {
        fetchPayments() {
            axios.get('/api/payments')
                .then(({ data }) => (this.payments = data))
                .catch(() => {
                    this.payments = [];
                });
        }
    }
}
</script>
