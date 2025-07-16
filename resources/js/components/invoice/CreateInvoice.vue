<template>
    <div>
        <div class="row">
            <router-link to="/invoices" class="btn btn-primary">All Invoices</router-link>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="login-form p-4">
                            <div class="text-center mb-3">
                                <h1 class="h4 text-gray-900">Create Invoice</h1>
                            </div>

                            <form @submit.prevent="submitInvoice">
                                <div class="form-group">
                                    <label>Quote Info:</label>
                                    <div v-if="quote">
                                        <p><strong>Client:</strong> {{ quote.client_name }}</p>
                                        <p><strong>Email:</strong> {{ quote.client_email }}</p>
                                        <p><strong>Quote Date:</strong> {{ quote.quote_date }}</p>
                                    </div>
                                </div>
                                <!-- Invoice Details -->
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <label>Invoice Number</label>
                                            <input type="text" class="form-control" placeholder="Invoice Number"
                                                v-model="form.invoice_number">
                                            <small class="text-danger" v-if="errors.invoice_number">{{
                                                errors.invoice_number[0]
                                            }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Status</label>
                                            <select class="form-control" v-model="form.status">
                                                <option value="draft">Draft</option>
                                                <option value="sent">Sent</option>
                                                <option value="paid">Paid</option>
                                                <option value="overdue">Overdue</option>
                                            </select>
                                            <small class="text-danger" v-if="errors.status">{{ errors.status[0]
                                            }}</small>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Invoice Date</label>
                                        <input type="date" class="form-control" v-model="form.invoice_date"
                                            @input="updateDueDate">
                                        <small class="text-danger" v-if="errors.invoice_date">{{ errors.invoice_date[0]
                                        }}</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Due Date</label>
                                        <input type="date" class="form-control" v-model="form.due_date" readonly>
                                    </div>
                                </div>

                                <!-- Line Items -->
                                <div class="form-group mt-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Qty</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in form.items" :key="index">
                                                <td><input type="text" class="form-control" v-model="item.description">
                                                </td>
                                                <td><input type="number" class="form-control"
                                                        v-model.number="item.quantity" @input="updateTotal(index)"></td>
                                                <td><input type="number" class="form-control"
                                                        v-model.number="item.unit_price" @input="updateTotal(index)">
                                                </td>
                                                <td>{{ item.total.toFixed(2) }}</td>
                                                <td><button class="btn btn-sm btn-danger"
                                                        @click="removeItem(index)">x</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button class="btn btn-sm btn-secondary" @click.prevent="addItem">+ Add
                                        Item</button>
                                </div>

                                <!-- Totals -->
                                <div class="form-row mt-3">
                                    <div class="col-md-4 offset-md-8">
                                        <div class="form-group">
                                            <label>Subtotal</label>
                                            <input class="form-control" placeholder="Subtotal"
                                                :value="subtotal.toFixed(2)" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>VAT</label>
                                            <input type="number" class="form-control" placeholder="VAT" :value="vat">
                                        </div>
                                        <div class="form-group">
                                            <label>Total</label>
                                            <input class="form-control" placeholder="Total" :value="total" readonly />
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes + Submit -->
                                <div class="form-group">
                                    <textarea class="form-control" rows="3" v-model="form.notes"
                                        placeholder="Notes..."></textarea>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">Create Invoice</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['quoteId'],
    data() {
        return {
            form: {
                quote_id: this.$route.params.quoteId || '', // set via route
                invoice_number: '',
                status: 'draft',
                invoice_date: '',
                due_date: '',
                vat: 0,
                subtotal: 0,
                total: 0,
                notes: '',
                items: []
            },
            quote: null,  // Store quote info
            errors: {}
        }
    },
    created() {
        if (this.form.quote_id) {
            axios.get(`/api/quotes/${this.form.quote_id}`)
                .then(({ data }) => {
                    this.quote = data;

                    // Optional: pre-fill invoice_date from quote date
                    const now = new Date();
                    const formattedDate = now.toISOString().substr(0, 10);
                    this.form.invoice_date = formattedDate;
                    console.log(formattedDate)
                    this.form.vat = data.vat;

                    // Pre-fill items
                    this.form.items = data.items.map(item => ({
                        description: item.description,
                        quantity: item.quantity,
                        unit_price: item.unit_price,
                        total: item.total
                    }));

                    // Calculate totals
                    this.updateAllTotals();
                    this.updateDueDate();

                });
        }
    },
    computed: {
        subtotal() {
            return this.form.items.reduce((sum, item) => sum + item.total, 0);
        },
        vat() {
            return parseFloat(this.form.vat);
        },
        total() {
            return this.subtotal + this.vat;
        }
    },
    methods: {
        updateItemTotal(index) {
            const item = this.form.items[index];
            item.total = item.quantity * item.unit_price;
        },
        updateAllTotals() {
            this.form.items.forEach((item, i) => this.updateItemTotal(i));
        },
        subtotal() {
            return this.form.items.reduce((sum, item) => sum + item.total, 0);
        },
        total() {
            return this.subtotal() + parseFloat(this.form.vat || 0);
        },
        updateDueDate() {
            // Set due date = invoice date + 30 days
            if (this.form.invoice_date) {
                const date = new Date(this.form.invoice_date);
                date.setDate(date.getDate() + 30);
                this.form.due_date = date.toISOString().substr(0, 10); // format YYYY-MM-DD
            }
        },
        submitInvoice() {
            this.form.subtotal = this.subtotal;
            this.form.total = this.total;

            axios.post('/api/invoices', this.form)
                .then(() => this.$router.push({ name: 'invoices' }))
                .catch(err => {
                    this.errors = err.response?.data?.errors || {};
                });
        },
        addItem() {
            this.form.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 });
        },
        removeItem(index) {
            this.form.items.splice(index, 1);
        }
    }
}
</script>
