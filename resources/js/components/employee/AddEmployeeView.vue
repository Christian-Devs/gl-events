<template>
  <div>
    <div class="row">
      <router-link to="/employees" class="btn btn-primary">All Employees</router-link>
    </div>
    <div class="row justify-content-center">
      <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Add Employee</h1>
                  </div>
                  <form class="user" @submit.prevent="employeeInsert" enctype="multipart/form-data">
                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <input type="text" class="form-control" id="exampleInputFirstName"
                            placeholder="Enter Full Name" v-model="form.name">
                          <small class="text-danger" v-if="errors.name">
                            {{ errors.name[0] }}
                          </small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control" id="employeeCodeInput"
                            placeholder="Enter Employee Code" v-model="form.nid">
                          <small class="text-danger" v-if="errors.nid">
                            {{ errors.nid[0] }}
                          </small>
                        </div>
                      </div>
                      <!--End Row-->
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <input type="text" class="form-control" id="exampleInputPassword"
                            placeholder="Enter Phone number" v-model="form.phone">
                          <small class="text-danger" v-if="errors.phone">
                            {{ errors.phone[0] }}
                          </small>
                        </div>
                        <div class="col-md-6">
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
                          <input type="date" class="form-control" id="JoiningDateInput"
                            placeholder="Date joined" v-model="form.joining_date">
                          <small class="text-danger" v-if="errors.joining_date">
                            {{ errors.joining_date[0] }}
                          </small>
                        </div>
                        <div class="col-md-6">
                          <select class="form-control" aria-placeholder="Select a Role" v-model="form.role_id" required>
                            <option value="" disabled>Select a role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                              {{ role.label || role.name }}
                            </option>
                          </select>
                          <small class="text-danger" v-if="errors.role_id">
                            {{ errors.role_id[0] }}
                          </small>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="form-row">
                        <div class="col-md-6">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" @change="onFileSelected">
                            <small class="text-danger" v-if="errors.photo">
                              {{ errors.photo[0] }}
                            </small>
                            <label class="custom-file-label" for="customFile">Choose file</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <img :src="form.photo" style="height: 40px; width: 40px;">
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
    else {
      axios.get('/api/roles')
        .then(response => {
          this.roles = response.data;
        })
        .catch(error => {
          console.error('Failed to load roles', error);
        });
    }
  },

  data() {
    return {
      form: {
        name: null,
        nid: null,
        phone: null,
        email: null,
        joining_date: null,
        photo: null,
        role_id: ''
      },
      roles: [],
      errors: {}
    }
  },

  methods: {
    employeeInsert() {
      axios.post('/api/employee', this.form)
        .then(() => {
          this.$router.push({ name: 'employees' })
          Notification.success()
        })
        .catch((error) => {
          if (error.response && error.response.data && error.response.data.errors) {
            this.errors = error.response.data.errors;
          } else {
            this.errors = {}; // Reset in case something else went wrong
            Notification.error('An unexpected error occurred');
            console.error('Error:', error);
          }
        })
    },

    onFileSelected(event) {
      let file = event.target.files[0];
      if (file.size > 1048770) {
        Notification.image_validation()
      } else {
        let reader = new FileReader();
        reader.onload = event => {
          this.form.photo = event.target.result
          console.log(event.target.result);
        };
        reader.readAsDataURL(file);
      }
    }

  }

}
</script>


<style type="text/css"></style>
