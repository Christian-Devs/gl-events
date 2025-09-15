<!-- resources/js/components/auth/ChangePassword.vue -->
<template>
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8">
            <div class="card shadow-sm my-5">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h1 class="h4 text-gray-900">Set a New Password</h1>
                        <p class="text-muted mb-0">Please choose a secure password to continue.</p>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="form-group">
                            <label>New password</label>
                            <div class="input-group">
                                <input :type="show1 ? 'text' : 'password'" class="form-control"
                                    v-model.trim="form.password" autocomplete="new-password" :disabled="submitting"
                                    minlength="6" required />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" @click="show1 = !show1">
                                        <i :class="['fas', show1 ? 'fa-eye-slash' : 'fa-eye']"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-danger" v-if="errors.password">{{ errors.password[0] }}</small>
                        </div>

                        <div class="form-group">
                            <label>Confirm password</label>
                            <div class="input-group">
                                <input :type="show2 ? 'text' : 'password'" class="form-control"
                                    v-model.trim="form.password_confirmation" autocomplete="new-password"
                                    :disabled="submitting" minlength="6" required />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" @click="show2 = !show2">
                                        <i :class="['fas', show2 ? 'fa-eye-slash' : 'fa-eye']"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-danger" v-if="errors.password_confirmation">{{
                                errors.password_confirmation[0] }}</small>
                        </div>

                        <button class="btn btn-primary btn-block" :disabled="submitting || !form.user_id">
                            <span v-if="submitting" class="spinner-border spinner-border-sm mr-1"></span>
                            Update password
                        </button>

                        <div class="text-center mt-3" v-if="ok">
                            <span class="text-success">Password updated. Redirecting to login…</span>
                        </div>

                        <div class="text-center mt-3" v-if="generalError">
                            <span class="text-danger">{{ generalError }}</span>
                        </div>
                    </form>

                    <div class="mt-3 small text-muted">
                        Password must be at least 6 characters. Consider using a mix of letters, numbers and symbols.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// If you prefer decoding JWT instead of calling /api/auth/me, you can also import your Token helper and use Token.payload(...).sub
export default {
    name: 'ChangePassword',
    data() {
        return {
            form: {
                user_id: null,
                password: '',
                password_confirmation: ''
            },
            errors: {},
            generalError: '',
            submitting: false,
            ok: false,
            show1: false,
            show2: false,
            loadingUser: true
        }
    },
    async created() {
        // Try to resolve user_id from /api/auth/me (token was set after login)
        try {
            const { data } = await axios.post('/api/auth/me')
            const id = data?.user?.id || data?.id || data?.employee?.user_id
            this.form.user_id = id || null
        } catch (e) {
            // If token expired or missing, send them back to login
            this.$router.replace({ name: '/' })
            return
        } finally {
            this.loadingUser = false
        }
    },
    methods: {
        async submit() {
            this.errors = {}
            this.generalError = ''
            this.submitting = true
            this.ok = false
            try {
                await axios.post('/api/auth/update-password', this.form)
                this.ok = true
                window.Notification?.success?.('Password updated')
                // wipe any stale auth and go to login
                localStorage.removeItem('token')
                localStorage.removeItem('name')
                localStorage.removeItem('role')
                setTimeout(() => this.$router.replace({ name: '/' }), 900)
            } catch (e) {
                const data = e?.response?.data
                if (data?.errors) this.errors = data.errors
                else if (data?.message) this.generalError = data.message
                else this.generalError = 'Failed to update password'
                console.error(e)
            } finally {
                this.submitting = false
            }
        }
    }
}
</script>
