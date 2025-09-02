<template>
  <div>
    <div class="row">
      <router-link to="/add-employee" class="btn btn-primary">Add Employee</router-link>
    </div>

    <br>
    <input class="form-control" type="text" v-model="search" placeholder="Search Employee" style="width: 25%;">
    <br>

    <div class="row">
      <div class="col-lg-12 mb-4">
        <!-- Simple Tables -->
        <div class="card">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Employee List</h6>
          </div>
          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th>Name</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Joining Date</th>
                  <th>Photo</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="employee in filtersearch" :key="employee.id">
                  <td>{{ employee.first_name }} {{ employee.last_name }}</td>
                  <td>{{ employee.phone }}</td>
                  <td>{{ employee.email }}</td>
                  <td>{{ employee.joining_date }}</td>
                  <td>
                    <router-link :to="{ name: 'edit-employee', params: { id: employee.id } }"
                      class="btn btn-sm btn-primary">Edit</router-link>
                    <a @click="deleteEmployee(employee.id)" class="btn btn-sm btn-danger text-white">Delete</a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer"></div>
        </div>
      </div>
    </div>
    <!--Row-->
  </div>
</template>


<script type="text/javascript">
export default {
  data() {
    return {
      employees: [],   // always start as array
      loading: false,
      search: '',
    }
  },
  created() {
    this.fetchEmployees()
  },
  computed: {
    // In case something odd slips through, keep this defensive
    safeEmployees() {
      return Array.isArray(this.employees) ? this.employees : []
    },
    filteredEmployees() {
      const list = this.safeEmployees
      const s = (this.search || '').toLowerCase()
      if (!s) return list
      return list.filter(e => {
        const name = `${e.first_name || ''} ${e.last_name || ''}`.toLowerCase()
        return (
          name.includes(s) ||
          (e.email || '').toLowerCase().includes(s) ||
          (e.phone || '').toLowerCase().includes(s)
        )
      })
    }
  },
  methods: {
    async fetchEmployees() {
      this.loading = true
      try {
        const { data } = await axios.get('/api/employee', {
          params: { per_page: 50, search: this.search }
        })
        // normalize: if paginator -> use data.data, else if array -> use it, else []
        this.employees = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : [])
      } catch (e) {
        this.employees = [] // stay safe
        console.error(e)
      } finally {
        this.loading = false
      }
    },
  }
}

</script>


<style type="text/css">
#em_photo {
  height: 40px;
  width: 40px;
}
</style>