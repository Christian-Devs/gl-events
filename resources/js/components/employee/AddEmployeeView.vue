<template>
  <div>
    <div class="row mb-3">
      <router-link to="/employees" class="btn btn-primary">All Employees</router-link>
    </div>

    <div class="row justify-content-center">
      <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form p-4">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Add Employee</h1>
                  </div>

                  <form class="user" @submit.prevent="employeeInsert">

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <input type="text" class="form-control" placeholder="First name" v-model="form.first_name" />
                          <small class="text-danger" v-if="errors.first_name">{{ errors.first_name[0] }}</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control" placeholder="Last name" v-model="form.last_name" />
                          <small class="text-danger" v-if="errors.last_name">{{ errors.last_name[0] }}</small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <input type="email" class="form-control" placeholder="Email" v-model="form.email" />
                          <small class="text-danger" v-if="errors.email">{{ errors.email[0] }}</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control" placeholder="Phone" v-model="form.phone" />
                          <small class="text-danger" v-if="errors.phone">{{ errors.phone[0] }}</small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <input type="text" class="form-control" placeholder="ID / Passport number"
                            v-model="form.id_number" />
                          <small class="text-danger" v-if="errors.id_number">{{ errors.id_number[0] }}</small>
                        </div>
                        <div class="col-md-3">
                          <input type="date" class="form-control" placeholder="Birthdate" v-model="form.birthdate" />
                          <small class="text-danger" v-if="errors.birthdate">{{ errors.birthdate[0] }}</small>
                        </div>
                        <div class="col-md-3">
                          <input type="date" class="form-control" placeholder="Start date" v-model="form.start_date" />
                          <small class="text-danger" v-if="errors.start_date">{{ errors.start_date[0] }}</small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-4">
                          <select class="form-control" v-model="form.pay_frequency">
                            <option disabled value="">Pay frequency</option>
                            <option value="monthly">Monthly</option>
                            <option value="fortnightly">Fortnightly</option>
                            <option value="weekly">Weekly</option>
                          </select>
                          <small class="text-danger" v-if="errors.pay_frequency">{{ errors.pay_frequency[0] }}</small>
                        </div>
                        <div class="col-md-4">
                          <select class="form-control" v-model="form.payment_method">
                            <option disabled value="">Payment method</option>
                            <option value="bank">Bank</option>
                            <option value="cash">Cash</option>
                          </select>
                          <small class="text-danger" v-if="errors.payment_method">{{ errors.payment_method[0] }}</small>
                        </div>
                        <div class="col-md-4">
                          <select class="form-control" v-model="form.status">
                            <option disabled value="">Employment status</option>
                            <option value="active">Active</option>
                            <option value="terminated">Terminated</option>
                          </select>
                          <small class="text-danger" v-if="errors.status">{{ errors.status[0] }}</small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <select class="form-control" v-model="form.role_id">
                            <option value="" disabled>Select a role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                              {{ role.label || role.name }}
                            </option>
                          </select>
                          <small class="text-danger" v-if="errors.role_id">{{ errors.role_id[0] }}</small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">Add Employee</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div> <!-- card -->
      </div>
    </div>
  </div>
</template>

<script>
import Notification from '../../helpers/Notification'

export default {
  data() {
    return {
      roles: [],
      errors: {},
      fullName: '',
      form: {
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        id_number: '',
        birthdate: '',
        start_date: '',
        pay_frequency: 'monthly',
        payment_method: 'bank',
        status: 'active',
        role_id: '',
        user_id: ''
      }
    }
  },

  created() {
    if (!User.loggedIn()) {
      this.$router.push({ name: '/' })
      return
    }
    axios.get('/api/roles')
      .then(res => { this.roles = res.data })
      .catch(() => { /* ignore for now */ })
  },

  methods: {
    splitFullName() {
      const n = (this.fullName || '').trim().replace(/\s+/g, ' ')
      if (!n) return
      const parts = n.split(' ')
      this.form.first_name = this.form.first_name || parts[0]
      this.form.last_name = this.form.last_name || (parts.length > 1 ? parts.slice(1).join(' ') : parts[0])
    },

    async employeeInsert() {
      this.errors = {}
      try {
        // Change to '/api/employee' if that’s your existing endpoint
        await axios.post('/api/employees', this.form)
        Notification.success('Employee added')
        this.$router.push({ name: 'employees' })
      } catch (error) {
        if (error?.response?.data?.errors) {
          this.errors = error.response.data.errors
        } else {
          Notification.error('An unexpected error occurred')
          console.error(error)
        }
      }
    }
  }
}
</script>
