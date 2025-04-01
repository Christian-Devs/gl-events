<template>
    <div>
      <div class="row">
        <router-link to="/add-jobcard" class="btn btn-primary">Create Job Card</router-link>
      </div>
  
      <br>
      <input class="form-control" type="text" v-model="search" placeholder="Search Job Card" style="width: 25%;">
      <br>
  
      <div class="row">
        <div class="col-lg-12 mb-4">
          <!-- Simple Tables -->
          <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Job Card List</h6>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th>Job Number</th>
                    <th>Stand Name</th>
                    <th>Show Name</th>
                    <th>AM / PM</th>
                    <th>Total Amount</th>
                    <th>Date Created</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="jobcard in filtersearch" :key="jobcard.id">
                    <td>{{ jobcard.job_number }}</td>
                    <td>{{ jobcard.stand_name }}</td>
                    <td>{{ jobcard.show_name }}</td>
                    <td>{{ jobcard.salesperson }}</td>
                    <td>R{{ jobcard.total_amount }}</td>
                    <td>{{ jobcard.created_at | moment('L hh:MM')}}</td>
                    <td>
                      <router-link :to="{name: 'edit-jobcard', params:{id:jobcard.id}}" class="btn btn-sm btn-primary">Edit</router-link>
                      <a @click="deleteJobcard(jobcard.id)" class="btn btn-sm btn-danger text-white">Delete</a>
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
    created() {
      if (!User.loggedIn()) {
        this.$router.push({ name: '/' })
      }
    },
    data() {
      return {
        jobcards: [],
        search: ''
      }
    },
  
    computed: {
      filtersearch() {
        return this.jobcards.filter(jobcard => {
          return jobcard.salesperson.toLowerCase().match(this.search.toLowerCase()) || jobcard.job_number.toLowerCase().match(this.search.toLowerCase()) || jobcard.stand_name.toLowerCase().match(this.search.toLowerCase())|| jobcard.show_name.toLowerCase().match(this.search.toLowerCase())
        })
      }
    },
  
    methods: {
      allJobcards() {
        axios.get('/api/jobcard/')
          .then(({ data }) => (this.jobcards = data))
          .catch()
      },
  
      deleteJobcard(id) {
        Swal.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.value) {
            axios.delete('/api/jobcard/'+ id)
          .then(() => {
            this.jobcards = this.jobcards.filter( jobcard => {
              return jobcard.id!== id;
            })
          })
          .catch(() => {this.$router.push({name: 'jobcards'})})
            Swal.fire({
              title: "Deleted!",
              text: "Your file has been deleted.",
              icon: "success"
            });
          }
        });
      }
    },
  
    created() {
      this.allJobcards()
    }
  }
  </script>
  
  
  <style type="text/css">
  
  </style>