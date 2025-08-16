<template>
    <div class="container py-4">
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
                <h2 class="mb-4">Employee Salary Report</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm text-center p-3">
                    <h5>Total Salaries Paid</h5>
                    <p class="h5 text-success">R {{ Number(summary.total_paid || 0).toFixed(2) }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center p-3">
                    <h5>Pending Salaries</h5>
                    <p class="h5 text-warning">R {{ Number(summary.total_pending || 0).toFixed(2) }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center p-3">
                    <h5>Total Records</h5>
                    <p class="h5">{{  Number(summary.total_records || 0) }}</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Monthly Payroll Summary (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Net Salary Total</th>
                            <th>Records</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="month in monthly" :key="month.month">
                            <td>{{ formatMonth(month.month) }}</td>
                            <td>R {{ parseFloat(month.total).toFixed(2) }}</td>
                            <td>{{ month.count }}</td>
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
import axios from 'axios';

export default {
    data() {
        return {
            summary: {},
            monthly: []
        };
    },
    created() {
        axios.get('/api/reports/salaries')
            .then(res => {
                this.summary = res.data.summary;
                this.monthly = res.data.monthly;
            });
    },
    methods: {
        formatMonth(monthStr) {
            const [year, month] = monthStr.split("-");
            const date = new Date(year, month - 1);
            return date.toLocaleString('default', { month: 'long', year: 'numeric' });
        },
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
};
</script>
