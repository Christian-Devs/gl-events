<template>
    <div>
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
                    <h2 class="h4">Quotes Report</h2>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-md-3 mb-3" v-for="(value, key) in summaryDisplay" :key="key">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted text-uppercase">{{ key }}</h6>
                            <h4 class="font-weight-bold">
                                {{ key === 'Total Value' ? 'R ' + Number(value).toFixed(2) : value }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Overview Table -->
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Overview (Last 12 Months)</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Month</th>
                                <th>Number of Quotes</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="month in report.monthly" :key="month.month">
                                <td>{{ month.month }}</td>
                                <td>{{ month.count }}</td>
                                <td>R {{ Number(month.total).toFixed(2) }}</td>
                            </tr>
                            <tr v-if="!report.monthly.length">
                                <td colspan="3" class="text-center text-muted">No data available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                        <input type="email" v-model="emailAddress" class="form-control"
                            placeholder="Enter email address" />
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
            report: {
                summary: {
                    total_quotes: 0,
                    total_value: 0,
                    pending: 0,
                    approved: 0,
                    rejected: 0
                },
                monthly: []
            }
        };
    },
    computed: {
        summaryDisplay() {
            return {
                'Total Quotes': this.report.summary.total_quotes,
                'Total Value': this.report.summary.total_value,
                'Pending': this.report.summary.pending,
                'Approved': this.report.summary.approved,
                'Rejected': this.report.summary.rejected
            };
        }
    },
    created() {
        axios.get('/api/reports/quotes').then(res => {
            this.report = res.data;
        });
    },
    methods: {
        downloadReport() {
            window.open('/api/reports/quotes/download', '_blank');
        },
        openEmailModal() {
            this.emailAddress = '';
            $('#emailModal').modal('show');
        },
        sendEmail() {
            if (!this.emailAddress) return;

            axios.post('/api/reports/quotes/email', { email: this.emailAddress })
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
