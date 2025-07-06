<template>
    <div class="card">
        <div class="card-header">
            <h4 class="text-primary">System Settings</h4>
        </div>
        <div class="card-body">
            <form @submit.prevent="saveSettings">
                <!-- Company Name & VAT Number-->
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label>Company Name</label>
                            <input type="text" v-model="form.company_name" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label>VAT Number</label>
                            <input type="text" v-model="form.vat_number" class="form-control" />
                        </div>
                    </div>
                </div>

                <!-- VAT Rate & Currency-->
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label>VAT Rate (%)</label>
                            <input type="number" v-model="form.vat_rate" class="form-control" step="0.01" min="0" />
                        </div>
                        <div class="col-md-6">
                            <label>Currency</label>
                            <input type="text" v-model="form.currency" class="form-control" />
                        </div>
                    </div>
                </div>

                <!-- Email & Phone-->
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email" v-model="form.email" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label>Phone</label>
                            <input type="text" v-model="form.phone" class="form-control" />
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label>Address</label>
                    <textarea v-model="form.address" class="form-control"></textarea>
                </div>

                <!-- Footer Note -->
                <div class="form-group">
                    <label>Footer Note</label>
                    <textarea v-model="form.footer_note" class="form-control"></textarea>
                </div>

                <!-- Logo Upload -->
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-7">
                            <label>Company Logo</label>
                            <input type="file" class="form-control" @change="handleLogoUpload" />
                            <img v-if="preview" :src="preview" class="mt-2" style="height: 60px;" />
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" :disabled="loading">
                    {{ loading ? 'Saving...' : 'Save Settings' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                company_name: '',
                vat_number: '',
                vat_rate: 0,
                currency: '',
                email: '',
                phone: '',
                address: '',
                footer_note: '',
                logo: null,
            },
            preview: null,
            settingsId: 1,
            loading: false
        };
    },
    created() {
        this.loadSettings();
    },
    methods: {
        loadSettings() {
            axios.get('/api/settings')
                .then(response => {
                    this.form = response.data;
                    this.settingsId = response.data.id;
                    if (response.data.logo) {
                        this.preview = '/' + response.data.logo;
                    }
                })
                .catch(error => {
                    console.error('Failed to load settings:', error);
                    alert('Failed to load settings');
                });
        },
        handleLogoUpload(event) {
            let file = event.target.files[0];
            let reader = new FileReader();
            reader.onload = (e) => {
                this.form.logo = e.target.result;
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        saveSettings() {
            this.loading = true;
            axios.put(`/api/settings/${this.settingsId}`, this.form)
                .then(() => {
                    alert('Settings saved successfully');
                })
                .catch((error) => {
                    console.error('Save failed:', error);
                    alert('Failed to save settings');
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
};
</script>
