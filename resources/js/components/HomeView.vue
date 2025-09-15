<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>

        <div class="row mb-3">
            <!-- Employees -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Employees</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ dash.totals.employees }}</div>
                                <div class="mt-2 mb-0 text-muted text-xs">
                                    <span class="text-success mr-2"><i class="fas fa-user-check"></i> {{
                                        dash.totals.active_pct }}%</span>
                                    <span>Active: {{ dash.totals.active }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SimplePay Linked -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">SimplePay Linked</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ dash.totals.linked }}</div>
                                <div class="mt-2 mb-0 text-muted text-xs">
                                    <span class="text-info mr-2"><i class="fas fa-link"></i> Linked</span>
                                    <span>Not linked: {{ dash.totals.not_linked }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-link fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotes: Pending Approval -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Quotes Pending Approval</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ dash.sales.quotes.pending }}
                                </div>
                                <div class="mt-2 mb-0 text-muted text-xs">
                                    <span>Total quotes: {{ dash.sales.quotes.total }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-signature fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices: Unpaid / Created -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Unpaid Invoices</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ dash.sales.invoices.unpaid_count }}
                                </div>
                                <div class="mt-2 mb-0 text-muted text-xs">
                                    <span>Outstanding: <strong>R{{ formatMoney(dash.sales.invoices.outstanding_total)
                                            }}</strong></span><br>
                                    <span>Created this month: {{ dash.sales.invoices.created_this_month }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice-dollar fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employees Added (Chart) -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Employees Added (last 6 months)</h6>
                    </div>
                    <div class="card-body">
                        <div ref="empChart" style="width:100%; height:320px;"></div>
                    </div>
                </div>
            </div>

            <!-- Latest Payroll Run -->
            <div class="col-xl-4 col-lg-5">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Latest Payroll Run</h6>
                    </div>
                    <div class="card-body">
                        <template v-if="dash.payroll && dash.payroll.ok">
                            <div>Client ID: <strong>{{ dash.payroll.client_id }}</strong></div>
                            <div>Run ID: <strong>{{ dash.payroll.latest_run_id }}</strong></div>
                            <div>Payslips: <strong>{{ dash.payroll.payslip_count }}</strong></div>
                        </template>
                        <template v-else>
                            <div class="text-muted">Not available.</div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Quotes Requiring Approval -->
            <div class="col-xl-6 col-lg-6">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Quotes Requiring Approval</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="q in dash.sales.quotes.pending_list" :key="q.id">
                                    <td>{{ q.number || q.id }}</td>
                                    <td>{{ q.client_name || q.customer_name }}</td>
                                    <td>R{{ formatMoney(q.total) }}</td>
                                    <td>{{ formatDate(q.created_at) }}</td>
                                    <td><span class="badge badge-warning">{{ q.status }}</span></td>
                                </tr>
                                <tr
                                    v-if="!dash.sales.quotes.pending_list || dash.sales.quotes.pending_list.length === 0">
                                    <td colspan="5" class="text-center text-muted">No quotes pending approval</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Unpaid Invoices -->
            <div class="col-xl-6 col-lg-6">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Unpaid Invoices</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Due</th>
                                    <th>Amount Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="inv in dash.sales.invoices.unpaid_list" :key="inv.id">
                                    <td>{{ inv.number || inv.id }}</td>
                                    <td>{{ inv.client_name || inv.customer_name }}</td>
                                    <td>{{ formatDate(inv.due_date) }}</td>
                                    <td>R{{ formatMoney(inv.amount_due) }}</td>
                                    <td>
                                        <span :class="['badge', badgeForInvoice(inv.status)]">{{ inv.status }}</span>
                                    </td>
                                </tr>
                                <tr
                                    v-if="!dash.sales.invoices.unpaid_list || dash.sales.invoices.unpaid_list.length === 0">
                                    <td colspan="5" class="text-center text-muted">No unpaid invoices</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div><!-- row -->
    </div>
</template>

<script>

export default {
    data() {
        return {
            loading: false,
            chartStyles: { width: '100%', height: '320px' },
            dash: {
                totals: { employees: 0, active: 0, active_pct: 0, linked: 0, not_linked: 0 },
                recent: [],
                per_month: [],
                payroll: { ok: false },
                sales: {
                    quotes: { total: 0, pending: 0, pending_list: [] },
                    invoices: { created_this_month: 0, unpaid_count: 0, outstanding_total: 0, unpaid_list: [] }
                }
            }
        }
    },
    async created() {
        if (!User.loggedIn()) {
            this.$router.push({ name: '/' })
            return
        }
        await this.fetchDashboard()
        this.updateTitle('Dashboard')
    },
    watch: {
        'dash.per_month': {
            deep: true,
            immediate: true,
            handler() {
                this.renderEmpChart();
            }
        }
    },
    mounted() {
        this.$nextTick(() => this.renderEmpChart());
    },
    methods: {
        async fetchDashboard() {
            this.loading = true
            try {
                const { data } = await axios.get('/api/dashboard/summary')
                if (data && data.totals) {
                    this.dash = { ...this.dash, ...data }
                }
                this.$nextTick(() => this.renderEmpChart());
            } catch (e) {
                console.error(e)
            } finally {
                this.loading = false
            }
        },
        renderEmpChart() {
            const Chart = window.CanvasJS && window.CanvasJS.Chart
            if (!Chart) {
                console.warn('CanvasJS.Chart not available yet.')
                return
            }

            const rows = Array.isArray(this.dash?.per_month) ? this.dash.per_month : []

            const points = rows.map(r => ({
                x: new Date(r.month_start),
                y: Number(r.cnt || 0)
            }))

            const el = this.$refs.empChart
            if (!el) {
                console.warn('empChart ref not found')
                return
            }

            if (!points.length) {
                el.innerHTML = '<div class="text-muted">No employee additions in the last 6 months.</div>'
                return
            }

            const chart = new Chart(el, {
                animationEnabled: true,
                axisX: { valueFormatString: 'MMM YYYY' },
                axisY: { includeZero: true },
                data: [{ type: 'column', dataPoints: points }]
            })
            chart.render()
        },
        formatMoney(n) {
            const v = Number(n || 0)
            return v.toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
        },
        formatDate(d) {
            if (!d) return '—'
            const dt = new Date(d)
            if (Number.isNaN(dt.getTime())) return d
            return dt.toISOString().slice(0, 10)
        },
        badgeForInvoice(status) {
            const s = String(status || '').toLowerCase()
            if (s.includes('overdue')) return 'badge-danger'
            if (s.includes('partial')) return 'badge-warning'
            if (s.includes('sent') || s.includes('unpaid')) return 'badge-info'
            if (s.includes('paid')) return 'badge-success'
            return 'badge-secondary'
        }
    }
}
</script>
