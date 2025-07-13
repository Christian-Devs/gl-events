<template>
    <div class="container">
        <h3>Create New Quote</h3>
        <form @submit.prevent="submitQuote">

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

            <hr>

            <h5>Quote Line Items</h5>
            <div v-for="(item, index) in form.items" :key="index" class="row mb-2">
                <div class="col-md-4">
                    <input v-model="item.description" class="form-control" placeholder="Description" required>
                </div>
                <div class="col-md-2">
                    <input v-model.number="item.quantity" @input="recalculate" type="number" min="1"
                        class="form-control" placeholder="Qty" required>
                </div>
                <div class="col-md-2">
                    <input v-model.number="item.unit_price" @input="recalculate" type="number" min="0" step="0.01"
                        class="form-control" placeholder="Unit Price" required>
                </div>
                <div class="col-md-2">
                    <input :value="(item.quantity * item.unit_price).toFixed(2)" class="form-control" disabled>
                </div>
                <div class="col-md-2">
                    <button @click.prevent="removeItem(index)" class="btn btn-danger btn-sm">Remove</button>
                </div>
            </div>

            <button @click.prevent="addItem" class="btn btn-sm btn-info mb-3">+ Add Item</button>

            <hr>

            <div class="form-group row">
                <div class="col-md-4 offset-md-8">
                    <label>Subtotal</label>
                    <input :value="subtotal.toFixed(2)" class="form-control" disabled>
                </div>
                <div class="col-md-4 offset-md-8 mt-2">
                    <label>VAT (%)</label>
                    <input v-model.number="form.vat" @input="recalculate" type="number" min="0" class="form-control">
                </div>
                <div class="col-md-4 offset-md-8 mt-2">
                    <label>Total</label>
                    <input :value="total.toFixed(2)" class="form-control" disabled>
                </div>
            </div>

            <div class="form-group mt-3">
                <label>Notes</label>
                <textarea v-model="form.notes" class="form-control"></textarea>
            </div>

            <button class="btn btn-success">Create Quote</button>

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
                vat: 0,
                notes: '',
                items: [
                    { description: '', quantity: 1, unit_price: 0 }
                ],
                subtotal: 0,
                total: 0
            }
        };
    },
    computed: {
        subtotal() {
            return this.form.items.reduce((sum, item) => {
                return sum + item.quantity * item.unit_price;
            }, 0);
        },
        total() {
            const vatAmount = (this.form.vat / 100) * this.subtotal;
            return this.subtotal + vatAmount;
        }
    },
    methods: {
        addItem() {
            this.form.items.push({ description: '', quantity: 1, unit_price: 0 });
        },
        removeItem(index) {
            this.form.items.splice(index, 1);
        },
        recalculate() {
            // Computed handles this, but you could also sync a hidden field here
        },
        submitQuote() {
            const payload = {
                ...this.form,
                subtotal: this.subtotal,
                total: this.total,
                items: this.form.items.map(item => ({
                    ...item,
                    total: item.quantity * item.unit_price
                }))
            };

            axios.post('/api/quotes', payload)
                .then(() => {
                    this.$router.push('/quotes');
                })
                .catch(err => {
                    alert('Error creating quote');
                    console.error(err);
                });
        }
    }
}
</script>
