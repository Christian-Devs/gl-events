<template>
    <div>
      <div class="row">
        <router-link to="/add-supplier" class="btn btn-primary">Add Supplier</router-link>
      </div>
  
      <br>
      <input class="form-control" type="text" v-model="search" placeholder="Search Supplier" style="width: 25%;">
      <br>
  
      <div class="row">
        <div class="col-lg-12 mb-4">
          <!-- Simple Tables -->
          <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Supplier List</h6>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th>Company Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Contact Person</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="supplier in filtersearch" :key="supplier.id">
                    <td>{{ supplier.company_name }}</td>
                    <td>{{ supplier.phone }}</td>
                    <td>{{ supplier.email }}</td>
                    <td>{{ supplier.address }}</td>
                    <td>{{ supplier.contact_person }}</td>
                    <td>
                      <router-link :to="{name: 'edit-supplier', params:{id:supplier.id}}" class="btn btn-sm btn-primary">Edit</router-link>
                      <a @click="deleteSupplier(supplier.id)" class="btn btn-sm btn-danger text-white">Delete</a>
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
        suppliers: [],
        search: ''
      }
    },
  
    computed: {
      filtersearch() {
        return this.suppliers.filter(supplier => {
          return supplier.company_name.toLowerCase().match(this.search.toLowerCase()) || supplier.phone.match(this.search) || supplier.email.toLowerCase().match(this.search.toLowerCase()) ||  supplier.contact_person.toLowerCase().match(this.search.toLowerCase())
        })
      }
    },
  
    methods: {
      allSuppliers() {
        axios.get('/api/supplier/')
          .then(({ data }) => (this.suppliers = data))
          .catch()
      },
  
      deleteSupplier(id) {
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
            axios.delete('/api/supplier/'+ id)
          .then(() => {
            this.suppliers = this.suppliers.filter( supplier => {
              return supplier.id!== id;
            })
          })
          .catch(() => {this.$router.push({name: 'suppliers'})})
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
      this.allSuppliers()
    }
  }
  </script>
  
  
  <style type="text/css">
  #em_photo {
    height: 40px;
    width: 40px;
  }
  </style>