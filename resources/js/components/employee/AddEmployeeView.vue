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
                            <option value="weekly">Weekly</option>
                          </select>
                          <small class="text-danger" v-if="errors.pay_frequency">{{ errors.pay_frequency[0] }}</small>
                        </div>
                        <div class="col-md-4">
                          <select class="form-control" v-model="form.payment_method">
                            <option disabled value="">Payment method</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="eft_manual">EFT (manual)</option>
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
                      <div v-if="form.payment_method === 'eft_manual'" class="col-md-12 mt-3">
                        <div class="form-row">
                          <div class="col-md-3">
                            <label>Bank ID <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" placeholder="Bank ID"
                              v-model.number="form.bank_id" />
                            <small class="text-danger" v-if="errors.bank_id">{{ errors.bank_id[0] }}</small>
                          </div>
                          <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Account number"
                              v-model="form.bank_account_number" />
                            <small class="text-danger" v-if="errors.bank_account_number">{{
                              errors.bank_account_number[0]
                            }}</small>
                          </div>
                          <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Branch code (6 digits)"
                              v-model="form.bank_branch_code" maxlength="6"
                              @input="form.bank_branch_code = form.bank_branch_code.replace(/[^0-9]/g, '').slice(0, 6)" />
                            <small class="text-danger" v-if="errors.bank_branch_code">{{ errors.bank_branch_code[0]
                            }}</small>
                          </div>
                          <div class="col-md-3">
                            <select class="form-control" v-model="form.bank_account_type">
                              <option value="">Account type (optional)</option>
                              <option value="1">Current (Cheque)</option>
                              <option value="2">Savings</option>
                              <option value="3">Transmission</option>
                              <option value="4">Bond</option>
                              <option value="6">Subscription Share</option>
                            </select>
                          </div>
                        </div>
                        <div class="form-row mt-2">
                          <div class="col-md-3">
                            <select class="form-control" v-model="form.bank_holder_relationship">
                              <option value="">Owner (optional)</option>
                              <option value="1">Employee</option>
                              <option value="2">Joint</option>
                              <option value="3">Third party</option>
                            </select>
                          </div>
                          <div class="col-md-9">
                            <input type="text" class="form-control" placeholder="Third-party holder name (if 3)"
                              v-model="form.bank_holder_name" />
                          </div>
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
        payment_method: 'cash',              // change to 'eft_manual' if you want to force bank capture
        status: 'active',
        role_id: '',
        user_id: '',

        // bank fields (required if payment_method === 'eft_manual')
        bank_id: null,
        bank_account_number: '',
        bank_branch_code: '',
        bank_account_type: '',
        bank_holder_relationship: '',
        bank_holder_name: '',
      },
      submitting: false,
    }
  },

  created() {
    if (!User.loggedIn()) {
      this.$router.push({ name: '/' })
      return
    }
    axios.get('/api/roles')
      .then(res => { this.roles = res.data })
      .catch(() => { })
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
      this.submitting = true
      try {
        await axios.post('/api/employee', this.form)
        Notification.success('Employee added')
        this.$router.push({ name: 'employees' })
      } catch (error) {
        if (error?.response?.data?.errors) {
          this.errors = error.response.data.errors
        } else {
          Notification.error('An unexpected error occurred')
          console.error(error)
        }
      } finally {
        this.submitting = false
      }
    }
  }
}
</script>
