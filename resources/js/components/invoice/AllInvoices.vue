<template>
  <div>
    <div class="row">
      <router-link to="/add-invoice" class="btn btn-primary">Add Invoice</router-link>
    </div>

    <br>
    <input class="form-control" type="text" v-model="search" placeholder="Search Invoice" style="width: 25%;">
    <br>

    <div class="row">
      <div class="col-lg-12 mb-4">
        <div class="card">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Invoice List</h6>
          </div>
          <div class="table-responsive">
            <table class="table table-flush">
              <thead class="thead-light">
                <tr>
                  <th>Invoice #</th>
                  <th>Client</th>
                  <th>Issue Date</th>
                  <th>Status</th>
                  <th>Total</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="invoice in filteredInvoices" :key="invoice.id">
                  <td>{{ invoice.invoice_number }}</td>
                  <td>{{ invoice.quote?.client_name }}</td>
                  <td>{{ invoice.invoice_date }}</td>
                  <td>
                    <span :class="statusBadge(invoice.status)">
                      {{ invoice.status }}
                    </span>
                  </td>
                  <td>R{{ parseFloat(invoice.total).toFixed(2) }}</td>
                  <td>
                    <router-link :to="{ name: 'edit-invoice', params: { id: invoice.id } }" class="btn btn-sm btn-primary">Edit</router-link>
                    <button class="btn btn-sm btn-danger" @click="deleteInvoice(invoice.id)">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
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
      invoices: [],
      search: ''
    }
  },
  computed: {
    filteredInvoices() {
      return this.invoices.filter(inv => {
        return inv.invoice_number.toLowerCase().includes(this.search.toLowerCase()) ||
          inv.quote?.client_name?.toLowerCase().includes(this.search.toLowerCase());
      });
    }
  },
  created() {
    axios.get('/api/invoices').then(res => {
      this.invoices = res.data;
    });
  },
  methods: {
    deleteInvoice(id) {
      if (confirm("Delete this invoice?")) {
        axios.delete(`/api/invoices/${id}`).then(() => {
          this.invoices = this.invoices.filter(i => i.id !== id);
        });
      }
    },
    statusBadge(status) {
      switch (status) {
        case 'draft': return 'badge badge-secondary';
        case 'sent': return 'badge badge-primary';
        case 'paid': return 'badge badge-success';
        case 'overdue': return 'badge badge-danger';
        default: return 'badge badge-light';
      }
    }
  }
}
</script>
