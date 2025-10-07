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
                    <p class="h5">{{ Number(summary.total_records || 0) }}</p>
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
import axios from 'axios';

export default {
    data() {
        return {
            emailAddress: '',
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
        async downloadReport(type = 'salaries') {
            const url = `/api/reports/salaries/download`;

            try {
                const res = await axios.get(url, { responseType: 'blob' });

                console.log('[downloadReport] axios response status:', res.status);
                console.log('[downloadReport] headers:', res.headers);

                // If server returned JSON error wrapped as 200 (rare), try to detect it:
                const contentType = (res.headers['content-type'] || '').toLowerCase();
                if (!res || res.status !== 200 || !contentType.includes('pdf')) {
                    // Try to decode body as text to show server message if it's JSON/text
                    try {
                        const text = await res.data.text ? await res.data.text() : null;
                        console.warn('[downloadReport] Unexpected response (not PDF):', text);
                        Notification.error('Server returned unexpected response when downloading report');
                    } catch (inner) {
                        Notification.error('Failed to download report (unexpected response)');
                    }
                    return;
                }

                // Get filename if provided
                const disp = res.headers['content-disposition'] || '';
                let filename = `${type}_report.pdf`;
                const m = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disp);
                if (m && m[1]) filename = m[1].replace(/['"]/g, '');

                // Create blob and open in new tab (or download)
                const blob = new Blob([res.data], { type: 'application/pdf' });
                const blobUrl = window.URL.createObjectURL(blob);

                // Option A: open in new tab / viewer
                window.open(blobUrl, '_blank');

                // Option B: force download instead
                // const a = document.createElement('a');
                // a.href = blobUrl;
                // a.download = filename;
                // document.body.appendChild(a);
                // a.click();
                // a.remove();

                // revoke after a minute
                setTimeout(() => window.URL.revokeObjectURL(blobUrl), 60_000);

                Notification?.success?.('Report ready');

            } catch (err) {
                console.error('[downloadReport] error:', err);
                // If server responded with JSON error (e.g. {message: '...'}), show it:
                if (err?.response?.data) {
                    try {
                        // If response is blob but contains JSON, read it:
                        if (err.response.data instanceof Blob) {
                            const text = await err.response.data.text();
                            const parsed = JSON.parse(text || '{}');
                            if (parsed.message) {
                                Notification.error(parsed.message);
                                return;
                            }
                        }
                    } catch (parseErr) {
                        // ignore parse failure
                    }
                    const payload = err.response.data;
                    const msg = payload.message || payload.error || 'Failed to download report';
                    Notification.error(msg);
                    // If it was an auth problem:
                    if (err.response.status === 401) {
                        this.$router.push({ name: '/' });
                    }
                    return;
                }

                // Fallback
                Notification.error(err.message || 'Failed to download report');
            }
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
