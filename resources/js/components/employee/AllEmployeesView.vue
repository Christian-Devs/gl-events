<template>
  <div>
    <div class="row mb-3">
      <router-link to="/add-employee" class="btn btn-primary">Add Employee</router-link>
    </div>

    <input class="form-control mb-3" type="text" v-model="search" placeholder="Search Employees" style="width: 25%;" />

    <div class="row">
      <div class="col-lg-12 mb-4">
        <div class="card">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Employees List</h6>
          </div>

          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
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
                <tr v-for="emp in filteredEmployees" :key="emp.id">
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
                  <td>
                    <SyncSimplePayButton v-if="!emp.simplepay_employee_id" :employee-id="emp.id"
                      :simplepay-employee-id="emp.simplepay_employee_id" @synced="refreshRow(emp.id)"
                      :disabled="!emp.email || !emp.phone || !emp.id_number || !emp.birthdate || !emp.start_date"
                      class="btn btn-sm btn-outline-secondary ml-1" />
                    <router-link :to="{ name: 'edit-employee', params: { id: emp.id } }"
                      class="btn btn-sm btn-primary ml-1">
                      Edit
                    </router-link>
                    <button @click.prevent="deleteEmployee(emp.id)" class="btn btn-sm btn-danger ml-1">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="card-footer"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      search: '',
    };
  },
  computed: {
    filteredEmployees() {
      if (!this.search) return this.employees;
      const term = this.search.toLowerCase();
      return this.employees.filter(
        (e) =>
          `${e.first_name} ${e.last_name}`.toLowerCase().includes(term) ||
          e.email.toLowerCase().includes(term) ||
          e.phone.toLowerCase().includes(term)
      );
    },
  },
  props: ['employees'],
  methods: {
    refreshRow(id) {
      // refresh employee data logic
    },
    deleteEmployee(id) {
      // delete employee logic
    },
  },
};
</script>


<script>
import SyncSimplePayButton from '../reusables/SyncSimplePayButton.vue' // adjust path

export default {
  components: { SyncSimplePayButton },
  data() {
    return {
      search: '',
      employees: [],
      loading: false,
    }
  },
  created() {
    this.fetchEmployees()
  },
computed: {
    filteredEmployees() {
      if (!this.search) return this.employees;
      const term = this.search.toLowerCase();
      return this.employees.filter(
        (e) =>
          `${e.first_name} ${e.last_name}`.toLowerCase().includes(term) ||
          e.email.toLowerCase().includes(term) ||
          e.phone.toLowerCase().includes(term)
      );
    },
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
