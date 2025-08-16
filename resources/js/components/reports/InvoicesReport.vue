<template>
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <!-- Report Actions -->
                <div class="d-flex justify-content-end mb-3">
                    <button @click="downloadReport" class="btn btn-outline-primary btn-sm mr-2">
                        Download PDF
                    </button>
                    <button @click="openEmailModal" class="btn btn-outline-secondary btn-sm">
                        Email Report
                    </button>
                </div>
                <h2 class="h4">Invoices Report</h2>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row mb-4">
            <div class="col-md-3" v-for="(value, label) in summary" :key="label">
                <div class="card shadow-sm text-center mt-2">
                    <div class="card-body">
                        <h5 class="card-title text-capitalize">{{ label.replace('_', ' ') }}</h5>
                        <p class="card-text h4">
                            {{ label === 'total_amount' ? 'R ' + parseFloat(value).toFixed(2) : value }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5>Monthly Breakdown (Last 12 Months)</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Month</th>
                            <th>Invoices</th>
                            <th>Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="month in monthly" :key="month.month">
                            <td>{{ month.month }}</td>
                            <td>{{ month.count }}</td>
                            <td>R {{ parseFloat(month.total).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Email Modal -->
        <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="emailModalLabel">Email Report</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="email" v-model="emailAddress" class="form-control" placeholder="Enter email address" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" @click="sendEmail">Send</button>
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
            emailAddress: '',
            summary: {},
            monthly: []
        };
    },
    created() {
        axios.get('/api/reports/invoices')
            .then(res => {
                this.summary = res.data.summary;
                this.monthly = res.data.monthly;
            });
    },
    methods: {
        downloadReport() {
            window.open('/api/reports/invoices/download', '_blank');
        },
        openEmailModal() {
            this.emailAddress = '';
            $('#emailModal').modal('show');
        },
        sendEmail() {
            if (!this.emailAddress) return;

            axios.post('/api/reports/invoices/email', { email: this.emailAddress })
                .then(() => {
                    $('#emailModal').modal('hide');
                    Swal.fire('Success', 'Report emailed successfully', 'success');
                })
                .catch(() => {
                    Swal.fire('Error', 'Failed to send report email', 'error');
                });
        }
    }
}
</script>
