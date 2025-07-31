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
                    <router-link :to="{ name: 'edit-invoice', params: { id: invoice.id } }"
                      class="btn btn-sm btn-primary">Edit</router-link>
                    <button class="btn btn-sm btn-success" v-if="!invoice.payment" @click="generatePayment(invoice.id)">
                      <i class="fas fa-money-bill-wave"></i> Generate Payment
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" @click="downloadPdf(invoice.id)"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Download PDF">
                      <i class="fas fa-file-pdf"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary" @click="sendEmail(invoice.id)"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Send via email">
                      <i class="fas fa-envelope"></i>
                    </button>
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
  mounted() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el))
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
    },
    downloadPdf(id) {
      window.open(`/api/invoices/${id}/download`, '_blank');
    },
    sendEmail(id) {
      axios.post(`/api/invoices/${id}/send`)
        .then(() => {
          Swal.fire('Success', 'Invoice emailed to client!', 'success');
        })
        .catch(() => {
          Swal.fire('Error', 'Failed to send email', 'error');
        });
    },
    async generatePayment(id) {
      try {
        const response = await axios.post(`/api/invoices/${id}/generate-payment`);
        console.log('Payment created:', response.data);

        // SAFELY push to local state
        if (this.invoice && Array.isArray(this.invoice.payments)) {
          this.invoice.payments.push(response.data.payment);
        }

        Swal.fire('Success', response.data.message, 'success');
      } catch (err) {
        console.error('Payment creation error:', err);
        const message = err.response?.data?.message || 'Something went wrong';
        Swal.fire('Error', message, 'error');
      }
    }

  }
}
</script>
