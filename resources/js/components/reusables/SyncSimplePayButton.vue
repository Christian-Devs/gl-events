<template>
    <button class="btn btn-sm" :class="isLinked ? 'btn-outline-secondary' : 'btn-outline-primary'" :disabled="loading"
        @click="onClick" title="Sync this employee to SimplePay">
        <span v-if="loading" class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
        {{ isLinked ? 'Sync SimplePay' : 'Link to SimplePay' }}
    </button>
</template>

<script>
import Swal from 'sweetalert2'
import Notification from '../../helpers/Notification'

export default {
    name: 'SyncSimplePayButton',
    props: {
        employeeId: { type: [Number, String], required: true },
        simplepayEmployeeId: { type: [Number, String, null], default: null },
    },
    data() {
        return { loading: false }
    },
    computed: {
        isLinked() { return !!this.simplepayEmployeeId }
    },

    methods: {
        tryParseEmbeddedJson(str) {
            if (!str || typeof str !== 'string') return null
            const first = str.indexOf('{')
            const last = str.lastIndexOf('}')
            if (first === -1 || last === -1 || last <= first) return null
            const candidate = str.slice(first, last + 1)
            try { return JSON.parse(candidate) } catch (e) {
                try { return JSON.parse(candidate.replace(/\\"/g, '"').replace(/\\n/g, '')) } catch { return null }
            }
        },

        formatErrorsHtml(errorsObj) {
            if (!errorsObj) return ''
            const items = []
            if (Array.isArray(errorsObj)) {
                errorsObj.forEach(i => items.push(String(i)))
            } else {
                Object.entries(errorsObj).forEach(([k, v]) => {
                    if (Array.isArray(v)) v.forEach(m => items.push(`${k}: ${m}`))
                    else items.push(`${k}: ${v}`)
                })
            }
            if (!items.length) return ''
            return `<ul style="text-align:left;margin:0;padding-left:1rem">${items.map(i => `<li>${i}</li>`).join('')}</ul>`
        },

        async onClick() {
            if (this.loading) return
            this.loading = true
            try {
                const { data } = await axios.post('/api/payroll/simplepay/employee/sync', { employee_id: this.employeeId })
                const title = data?.message || (this.isLinked ? 'Synced with SimplePay' : 'Linked to SimplePay')
                if (window.Toast && window.Toast.fire) window.Toast.fire({ icon: 'success', title })
                else if (Notification?.success) Notification.success(title)
                this.$emit('synced', data)
            } catch (err) {
                // Debug: always print the raw error to console first
                console.error('Sync error raw:', err, err?.response?.data)

                const resp = err?.response
                let payload = resp?.data || {}
                // If SimplePay details buried in `details` string, attempt parse
                if (payload?.details && typeof payload.details === 'string') {
                    const parsed = this.tryParseEmbeddedJson(payload.details)
                    if (parsed) payload._embedded = parsed
                }

                // Find structured errors in likely places
                const candidateErrors = payload.errors || payload._embedded?.errors || payload.fields || null
                const html = this.formatErrorsHtml(candidateErrors)

                const messageText = payload?.message || payload?.error || payload._embedded?.message || (resp ? `HTTP ${resp.status}` : err?.message)

                try {
                    if (html) {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Validation error',
                            html,
                            width: 640,
                            confirmButtonText: 'OK'
                        })
                    } else {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Sync failed',
                            text: messageText || 'Unknown error',
                            confirmButtonText: 'OK'
                        })
                    }
                } catch (swalErr) {
                    // If Swal itself throws (very rare), fall back to Notification
                    console.error('Swal failed:', swalErr)
                    if (Notification?.error) Notification.error(messageText || 'Sync failed')
                }

                // Also show toast if configured
                if (window.Toast && window.Toast.fire) {
                    window.Toast.fire({ icon: 'error', title: (Array.isArray(candidateErrors) ? candidateErrors[0] : messageText) || 'Sync failed' })
                }

                this.$emit('sync-failed', { err, payload })
            } finally {
                this.loading = false
            }
        },
    }
}
</script>
