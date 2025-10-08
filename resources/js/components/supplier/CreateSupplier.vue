<template>
  <div>
    <div class="row">
      <router-link to="/suppliers" class="btn btn-primary">All Suppliers</router-link>
    </div>
    <div class="row justify-content-center">
      <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Add Supplier</h1>
                  </div>
                  <form class="user" @submit.prevent="supplierInsert" enctype="multipart/form-data">
                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <label>Company Name</label>
                          <input type="text" class="form-control" id="exampleInputFirstName"
                            placeholder="Enter Supplier Name" v-model="form.company_name">
                          <small class="text-danger" v-if="errors.company_name">
                            {{ errors.company_name[0] }}
                          </small>
                        </div>
                        <div class="col-md-6">
                          <label>VAT Number</label>
                          <input type="text" class="form-control" id="exampleInputPassword"
                            placeholder="Enter VAT Number" v-model="form.vat_number">
                          <small class="text-danger" v-if="errors.vat_number">
                            {{ errors.vat_number[0] }}
                          </small>
                        </div>
                      </div>
                      <!--End Row-->
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <label>Phone</label>
                          <input type="text" class="form-control" id="exampleInputPassword"
                            placeholder="Enter Phone Number" v-model="form.phone">
                          <small class="text-danger" v-if="errors.phone">
                            {{ errors.phone[0] }}
                          </small>
                        </div>
                        <div class="col-md-6">
                          <label>Email</label>
                          <input type="email" class="form-control" id="exampleInputEmail" aria-describedby="emailHelp"
                            placeholder="Enter Email Address" v-model="form.email">
                          <small class="text-danger" v-if="errors.email">
                            {{ errors.email[0] }}
                          </small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <label>Contact Person</label>
                          <input type="text" class="form-control" id="exampleInputEmail" aria-describedby="emailHelp"
                            placeholder="Enter Contact Person Name" v-model="form.contact_person">
                          <small class="text-danger" v-if="errors.contact_person">
                            {{ errors.contact_person[0] }}
                          </small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-12">
                          <label>Address</label>
                          <input type="text" class="form-control" id="exampleInputPasswordRepeat"
                            placeholder="Enter Address" v-model="form.address">
                          <small class="text-danger" v-if="errors.address">
                            {{ errors.address[0] }}
                          </small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-12">
                          <label>Notes</label>
                          <textarea class="form-control" id="exampleInputPasswordRepeat" placeholder="Notes..."
                            v-model="form.notes"></textarea>
                          <small class="text-danger" v-if="errors.notes">
                            {{ errors.notes[0] }}
                          </small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">Add Supplier</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script type="text/javascript">
import Notification from '../../helpers/Notification'

export default {
  created() {
    if (!User.loggedIn()) {
      this.$router.push({ name: '/' })
    }
  },

  data() {
    return {
      form: {
        company_name: null,
        phone: null,
        email: null,
        contact_person: null,
        address: null,
        vat_number: null,
        notes: null
      },
      errors: {}
    }
  },

  methods: {
    supplierInsert() {
      axios.post('/api/supplier', this.form)
        .then(() => {
          this.$router.push({ name: 'suppliers' })
          Notification.success()
        })
        .catch(error =>
          this.errors = error.response.data.errors
        )
    },

  }

}
</script>


<style type="text/css"></style>