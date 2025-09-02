<template>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email / Phone</th>
        <th>Role</th>
        <th>Status</th>
        <th>SimplePay</th>
        <th style="width: 220px;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="emp in employees" :key="emp.id">
        <td>{{ emp.first_name }} {{ emp.last_name }}</td>
        <td>
          <div>{{ emp.email }}</div>
          <small class="text-muted">{{ emp.phone }}</small>
        </td>
        <td>{{ emp.role ? emp.role.name : '' }}</td>
        <td>
          <span :class="['badge', emp.status === 'active' ? 'badge-success' : 'badge-secondary']">
            {{ emp.status }}
          </span>
        </td>
        <td>
          <span v-if="emp.simplepay_employee_id" class="badge badge-light">Linked</span>
          <span v-else class="badge badge-warning">Not linked</span>
        </td>
        <td class="text-right">
          <SyncSimplePayButton v-if="!emp.simplepay_employee_id" :employee-id="emp.id"
            :simplepay-employee-id="emp.simplepay_employee_id" @synced="refreshRow(emp.id)"
            :disabled="!emp.email || !emp.phone || !emp.id_number || !emp.birthdate || !emp.start_date" />
          <router-link :to="{ name: 'edit-employee', params: { id: emp.id } }" class="btn btn-sm btn-primary ml-2">
            Edit
          </router-link>
          <a @click.prevent="deleteEmployee(emp.id)" class="btn btn-sm btn-danger text-white ml-2">
            Delete
          </a>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import SyncSimplePayButton from '../reusables/SyncSimplePayButton.vue' // adjust path

export default {
  components: { SyncSimplePayButton },
  data() {
    return {
      employees: [],
      loading: false,
    }
  },
  created() {
    this.fetchEmployees()
  },

  methods: {
    async fetchEmployees() {
      this.loading = true
      try {
        const { data } = await axios.get('/api/employee', { params: {/* search, per_page, page */ } })
        this.employees = Array.isArray(data?.data) ? data.data : data
      } finally {
        this.loading = false
      }
    },
    async refreshRow(id) {
      try {
        const { data } = await axios.get(`/api/employee/${id}`)
        const idx = this.employees.findIndex(e => e.id === id)
        if (idx !== -1) this.$set(this.employees, idx, data)
      } catch {
        this.fetchEmployees()
      }
    },
    async deleteEmployee(id) {
      if (!confirm('Delete this employee?')) return
      try {
        await axios.delete(`/api/employee/${id}`)
        this.employees = this.employees.filter(e => e.id !== id)
      } catch (e) {
        alert(e?.response?.data?.message || 'Failed to delete employee')
      }
    },
  },
}
</script>
