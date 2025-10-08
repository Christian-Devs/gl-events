<template>
    <div>
        <div class="row">
            <router-link to="/quotes" class="btn btn-primary">All Quotes</router-link>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Add Quote</h1>
                                    </div>
                                    <form class="user" @submit.prevent="submitQuote">
                                        <!-- Client Info -->
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <label for="client_name">Client Name</label>
                                                    <input type="text" class="form-control" placeholder="Client Name"
                                                        v-model="form.client_name">
                                                    <small class="text-danger" v-if="errors.client_name">{{
                                                        errors.client_name[0] }}</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="client_email">Client Email</label>
                                                    <input type="email" class="form-control" placeholder="Client Email"
                                                        v-model="form.client_email">
                                                    <small class="text-danger" v-if="errors.client_email">{{
                                                        errors.client_email[0] }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notes -->
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control" placeholder="Notes..."
                                                v-model="form.notes"></textarea>
                                            <small class="text-danger" v-if="errors.notes">{{ errors.notes[0] }}</small>
                                        </div>

                                        <!-- Quote Items -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Quote Line Items</label>
                                            <div v-for="(item, index) in form.items" :key="index" class="form-row mb-2">
                                                <div class="col-md-4">
                                                    <label for="description">Description</label>
                                                    <input class="form-control" v-model="item.description"
                                                        placeholder="Description">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="quantity">Quantity</label>
                                                    <input class="form-control" type="number"
                                                        v-model.number="item.quantity" placeholder="Qty"
                                                        @input="calculateTotals">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="unit_price">Unit Price</label>
                                                    <input class="form-control" type="number"
                                                        v-model.number="item.unit_price" placeholder="Price"
                                                        @input="calculateTotals">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="total">Total</label>
                                                    <input class="form-control" :value="item.total.toFixed(2)" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        @click="removeItem(index)">X</button>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-sm" @click="addItem">+
                                                Add Item</button>
                                        </div>

                                        <!-- Totals -->
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-4">
                                                    <label for="subtotal">Subtotal</label>
                                                    <input class="form-control" placeholder="Subtotal"
                                                        :value="form.subtotal.toFixed(2)" readonly />
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="vat">VAT</label>
                                                    <input class="form-control" placeholder="VAT"
                                                        v-model.number="form.vat" @input="calculateTotals" />
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="total">Total</label>
                                                    <input class="form-control" placeholder="Total"
                                                        :value="form.total.toFixed(2)" readonly />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block">Save Quote</button>
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
                client_name: '',
                client_email: '',
                quote_date: '',
                notes: '',
                subtotal: 0,
                vat: 0,
                total: 0,
                items: [
                    { description: '', quantity: 1, unit_price: 0, total: 0 }
                ]
            },
            errors: {}
        };
    },
    methods: {
        calculateTotals() {
            let subtotal = 0;
            this.form.items.forEach(item => {
                item.total = item.quantity * item.unit_price;
                subtotal += item.total;
            });
            this.form.subtotal = subtotal;
            this.form.total = subtotal + (parseFloat(this.form.vat) || 0);
        },
        addItem() {
            this.form.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 });
        },
        removeItem(index) {
            this.form.items.splice(index, 1);
            this.calculateTotals();
        },
        submitQuote() {
            axios.post('/api/quotes', this.form)
                .then(() => {
                    this.$router.push({ name: 'quotes' });
                })
                .catch(err => {
                    this.errors = err.response?.data?.errors || {};
                });
        }
    }
};
</script>
