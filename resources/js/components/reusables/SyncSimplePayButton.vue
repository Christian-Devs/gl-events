<template>
    <button class="btn btn-sm" :class="isLinked ? 'btn-outline-secondary' : 'btn-outline-primary'" :disabled="loading"
        @click="onClick" title="Sync this employee to SimplePay">
        <span v-if="loading" class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
        {{ isLinked ? 'Sync SimplePay' : 'Link to SimplePay' }}
    </button>
</template>

<script>
import Notification from '../../helpers/Notification' // adjust path if needed

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
        isLinked() {
            return !!this.simplepayEmployeeId
        },
    },
    methods: {
        async onClick() {
            if (this.loading) return
            this.loading = true
            try {
                await axios.post('/api/payroll/simplepay/employee/sync', { employee_id: this.employeeId })
                Notification.success(this.isLinked ? 'Synced with SimplePay' : 'Linked to SimplePay')
                this.$emit('synced') // tell parent to refresh row/list
            } catch (e) {
                const msg = e?.response?.data?.error || 'Sync failed'
                const fields = e?.response?.data?.fields
                Notification.error(msg)
                if (fields && Array.isArray(fields) && fields.length) {
                    console.error('Missing required fields for SimplePay:', fields)
                }
            } finally {
                this.loading = false
            }
        },
    },
}
</script>
