<template>
    <div class="container">
        <h3>Edit Quote</h3>

        <form @submit.prevent="updateQuote">
            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-6">
                        <label>Client Name</label>
                        <input v-model="form.client_name" class="form-control" type="text" required>
                    </div>
                    <div class="col-md-6">
                        <label>Client Email</label>
                        <input v-model="form.client_email" class="form-control" type="email">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea v-model="form.notes" class="form-control"></textarea>
            </div>

            <h5>Line Items</h5>
            <div v-for="(item, index) in form.items" :key="index" class="border p-2 mb-2">
                <div class="form-row">
                    <div class="col">
                        <label for="description">Description</label>
                        <input v-model="item.description" class="form-control" placeholder="Description" required />
                    </div>
                    <div class="col">
                        <label for="quantity">Quantity</label>
                        <input v-model.number="item.quantity" type="number" min="1" class="form-control"
                            @input="calculateTotals" placeholder="Qty" required />
                    </div>
                    <div class="col">
                        <label for="unit_price">Unit Price</label>
                        <input v-model.number="item.unit_price" type="number" min="0" class="form-control"
                            @input="calculateTotals" placeholder="Unit Price" required />
                    </div>
                    <div class="col">
                        <label for="total">Total</label>
                        <input v-model="item.total" class="form-control" readonly />
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-danger btn-sm" @click="removeItem(index)">X</button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary btn-sm mb-3" @click="addItem">+ Add Line Item</button>

            <div class="form-group">
                <label>Subtotal</label>
                <input v-model="form.subtotal" class="form-control" readonly />
            </div>

            <div class="form-group">
                <label>VAT</label>
                <input v-model.number="form.vat" type="number" min="0" class="form-control" @input="calculateTotals" />
            </div>

            <div class="form-group">
                <label>Total</label>
                <input v-model="form.total" class="form-control" readonly />
            </div>

            <button type="submit" class="btn btn-primary">Update Quote</button>
        </form>
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
                items: []
            },
            quoteId: this.$route.params.id
        };
    },
    created() {
        this.fetchQuote();
    },
    methods: {
        fetchQuote() {
            axios.get(`/api/quotes/${this.quoteId}`)
                .then(({ data }) => {
                    this.form = {
                        ...data,
                        vat: parseFloat(data.vat) || 0,
                        items: data.items.map(item => ({
                            ...item,
                            total: item.total || item.quantity * item.unit_price
                        }))
                    };
                    this.calculateTotals();
                })
                .catch(error => {
                    console.error('Failed to load quote:', error);
                });
        },
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
            this.form.items.push({
                description: '',
                quantity: 1,
                unit_price: 0,
                total: 0
            });
        },
        removeItem(index) {
            this.form.items.splice(index, 1);
            this.calculateTotals();
        },
        updateQuote() {
            axios.put(`/api/quotes/${this.quoteId}`, this.form)
                .then(() => {
                    this.$router.push({ name: 'quotes' });
                    alert('Quote updated successfully')
                })
                .catch(err => {
                    console.error('Update failed', err);
                    alert('Failed to update quote');
                });
        }
    }
};
</script>

<style scoped>
input,
textarea {
    margin-bottom: 10px;
}
</style>
