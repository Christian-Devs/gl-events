<template>
  <div>
    <div class="row">
      <router-link to="/invoices" class="btn btn-secondary">Back to Invoices</router-link>
    </div>

    <div class="row justify-content-center">
      <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="login-form p-4">
              <div class="text-center mb-3">
                <h1 class="h4 text-gray-900">Edit Invoice</h1>
              </div>

              <form @submit.prevent="updateInvoice">
                <div class="form-group">
                  <input type="text" class="form-control" placeholder="Invoice Number" v-model="form.invoice_number" disabled>
                </div>

                <div class="form-row">
                  <div class="col-md-6">
                    <input type="date" class="form-control" v-model="form.invoice_date">
                  </div>
                  <div class="col-md-6">
                    <input type="date" class="form-control" v-model="form.due_date">
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
                        <td><input type="text" class="form-control" v-model="item.description"></td>
                        <td><input type="number" class="form-control" v-model.number="item.quantity" @input="updateItemTotal(index)"></td>
                        <td><input type="number" class="form-control" v-model.number="item.unit_price" @input="updateItemTotal(index)"></td>
                        <td>{{ item.total.toFixed(2) }}</td>
                        <td><button class="btn btn-sm btn-danger" @click="removeItem(index)">x</button></td>
                      </tr>
                    </tbody>
                  </table>
                  <button class="btn btn-sm btn-secondary" @click.prevent="addItem">+ Add Item</button>
                </div>

                <div class="form-row mt-3">
                  <div class="col-md-4 offset-md-8">
                    <div class="form-group">
                      <label>Subtotal</label>
                      <input type="text" class="form-control" :value="subtotal.toFixed(2)" disabled>
                    </div>
                    <div class="form-group">
                      <label>VAT</label>
                      <input type="number" class="form-control" v-model.number="form.vat">
                    </div>
                    <div class="form-group">
                      <label>Total</label>
                      <input type="text" class="form-control" :value="total.toFixed(2)" disabled>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <textarea class="form-control" rows="3" v-model="form.notes" placeholder="Notes..."></textarea>
                </div>

                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control" v-model="form.status">
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                  </select>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary btn-block">Update Invoice</button>
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
  data() {
    return {
      form: {
        invoice_number: '',
        invoice_date: '',
        due_date: '',
        vat: 0,
        notes: '',
        status: 'draft',
        items: []
      },
      errors: {}
    }
  },
  computed: {
    subtotal() {
      return this.form.items.reduce((sum, item) => sum + item.total, 0);
    },
    total() {
      return this.subtotal + (this.form.vat || 0);
    }
  },
  created() {
    axios.get(`/api/invoices/${this.$route.params.id}`)
      .then(res => {
        const invoice = res.data;
        this.form = {
          invoice_number: invoice.invoice_number,
          invoice_date: invoice.invoice_date,
          due_date: invoice.due_date,
          vat: invoice.vat || 0,
          notes: invoice.notes,
          status: invoice.status,
          items: invoice.items.map(item => ({
            description: item.description,
            quantity: item.quantity,
            unit_price: item.unit_price,
            total: item.total
          }))
        };
      });
  },
  methods: {
    updateItemTotal(index) {
      const item = this.form.items[index];
      item.total = item.quantity * item.unit_price;
    },
    addItem() {
      this.form.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 });
    },
    removeItem(index) {
      this.form.items.splice(index, 1);
    },
    updateInvoice() {
      const payload = {
        invoice_date: this.form.invoice_date,
        due_date: this.form.due_date,
        subtotal: this.subtotal,
        vat: this.form.vat,
        total: this.total,
        status: this.form.status,
        notes: this.form.notes,
        items: this.form.items
      };

      axios.put(`/api/invoices/${this.$route.params.id}`, payload)
        .then(() => {
          this.$router.push({ name: 'invoices' });
        })
        .catch(err => {
          this.errors = err.response?.data?.errors || {};
        });
    }
  }
}
</script>
