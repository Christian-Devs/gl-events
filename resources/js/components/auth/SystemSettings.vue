<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">System Settings</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link to="/home">Home</router-link></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </div>

        <ul class="nav nav-tabs">
            <li class="nav-item"><a href="#" class="nav-link" :class="{ active: tab === 'company' }"
                    @click.prevent="tab = 'company'">Company</a></li>
            <li class="nav-item"><a href="#" class="nav-link" :class="{ active: tab === 'simplepay' }"
                    @click.prevent="tab = 'simplepay'">SimplePay</a></li>
            <li class="nav-item"><a href="#" class="nav-link" :class="{ active: tab === 'email' }"
                    @click.prevent="tab = 'email'">Email</a></li>
            <li class="nav-item"><a href="#" class="nav-link" :class="{ active: tab === 'numbering' }"
                    @click.prevent="tab = 'numbering'">Numbering</a></li>
        </ul>

        <div class="card mt-3">
            <div class="card-body">
                <form @submit.prevent="save" autocomplete="off">
                    <!-- Company -->
                    <div v-show="tab === 'company'">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Company Name</label>
                                <input class="form-control" v-model="form.company.company_name">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Logo URL</label>
                                <input class="form-control" v-model="form.company.company_logo_url">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Default Tax Rate (%)</label>
                                <input type="number" min="0" step="0.01" class="form-control"
                                    v-model.number="form.company.tax_rate">
                            </div>
                        </div>
                    </div>

                    <!-- SimplePay -->
                    <div v-show="tab === 'simplepay'">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>API Base URL</label>
                                <input class="form-control" v-model="form.simplepay.base"
                                    placeholder="https://api.payroll.simplepay.cloud/v1">
                            </div>
                            <div class="form-group col-md-6">
                                <label>API Key (secret)</label>
                                <input class="form-control" v-model="form.simplepay.key" placeholder="••••••••">
                                <small class="text-muted">Leave blank to keep existing.</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Client ID</label>
                                <input type="number" class="form-control" v-model.number="form.simplepay.client_id">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Wave ID (Monthly)</label>
                                <input type="number" class="form-control" v-model.number="form.simplepay.wave_monthly">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Wave ID (Weekly)</label>
                                <input type="number" class="form-control" v-model.number="form.simplepay.wave_weekly">
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div v-show="tab === 'email'">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>SMTP Host</label>
                                <input class="form-control" v-model="form.email.host">
                            </div>
                            <div class="form-group col-md-2">
                                <label>Port</label>
                                <input type="number" class="form-control" v-model.number="form.email.port">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Username</label>
                                <input class="form-control" v-model="form.email.username">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Password (secret)</label>
                                <input type="password" class="form-control" v-model="form.email.password"
                                    placeholder="••••••••">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>From Address</label>
                                <input type="email" class="form-control" v-model="form.email.from"
                                    placeholder="no-reply@company.com">
                            </div>
                        </div>
                    </div>

                    <!-- Numbering -->
                    <div v-show="tab === 'numbering'">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Invoice Prefix</label>
                                <input class="form-control" v-model="form.numbering.invoice_prefix">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Quote Prefix</label>
                                <input class="form-control" v-model="form.numbering.quote_prefix">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <button class="btn btn-primary" :disabled="saving">
                            <span v-if="saving" class="spinner-border spinner-border-sm mr-2"></span>
                            Save Settings
                        </button>
                        <span v-if="saved" class="text-success ml-3">Saved</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Notification from '../../helpers/Notification'

export default {
    data() {
        return {
            tab: 'company',
            saving: false,
            saved: false,
            form: {
                company: { company_name: '', company_logo_url: '', tax_rate: null },
                simplepay: { base: '', key: '', client_id: null, wave_monthly: null, wave_weekly: null },
                email: { host: '', port: null, username: '', password: '', from: '' },
                numbering: { invoice_prefix: 'INV', quote_prefix: 'QT' }
            }
        }
    },
    async created() {
        if (!User.loggedIn()) return this.$router.push({ name: 'login' })
        try {
            const { data } = await axios.get('/api/settings')
            // merge defaults with server values
            this.form = {
                company: { ...this.form.company, ...(data.company || {}) },
                simplepay: { ...this.form.simplepay, ...(data.simplepay || {}) },
                email: { ...this.form.email, ...(data.email || {}) },
                numbering: { ...this.form.numbering, ...(data.numbering || {}) },
            }
        } catch (e) {
            Notification.error('Failed to load settings')
            console.error(e)
        }
    },
    methods: {
        async save() {
            this.saving = true; this.saved = false
            try {
                await axios.put('/api/settings', this.form)
                this.saved = true
                Notification.success('Settings saved')
                // Refresh public settings for UI elements
                try {
                    const { data } = await axios.get('/api/settings/public')
                    localStorage.setItem('cfg', JSON.stringify(data))
                } catch { }
            } catch (e) {
                Notification.error(e?.response?.data?.message || 'Failed to save')
                console.error(e)
            } finally {
                this.saving = false
            }
        }
    }
}
</script>
