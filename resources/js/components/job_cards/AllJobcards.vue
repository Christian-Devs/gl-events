<template>
  <div>
    <div class="row">
      <router-link to="/add-jobcard" class="btn btn-primary">Add Job Card</router-link>
    </div>

    <br />
    <input class="form-control" type="text" v-model="search" placeholder="Search Job Card" style="width: 25%;" />
    <br />

    <div class="row">
      <div class="col-lg-12 mb-4">
        <div class="card">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Job Card List</h6>
          </div>
          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th>Quote</th>
                  <th>Assigned To</th>
                  <th>Status</th>
                  <th>Start</th>
                  <th>Due</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="jobcard in filteredJobcards" :key="jobcard.id">
                  <td>
                    <span class="font-weight-bold">#{{ jobcard.quote?.id }}</span>
                    <br />
                    {{ jobcard.quote?.client_name }}
                  </td>
                  <td>{{ jobcard.assigned_to }}</td>
                  <td>
                    <span :class="statusBadge(jobcard.status)">
                      {{ jobcard.status }}
                    </span>
                  </td>
                  <td>{{ jobcard.start_date }}</td>
                  <td>{{ jobcard.due_date }}</td>
                  <td>
                    <router-link :to="{ name: 'edit-jobcard', params: { id: jobcard.id } }" class="btn btn-sm btn-primary">Edit</router-link>
                    <button class="btn btn-sm btn-danger" @click="deleteJobcard(jobcard.id)">Delete</button>
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
      jobcards: [],
      search: ''
    };
  },
  computed: {
    filteredJobcards() {
      return this.jobcards.filter(j =>
        j.quote?.client_name.toLowerCase().includes(this.search.toLowerCase()) ||
        j.assigned_to?.toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },
  created() {
    this.fetchJobcards();
  },
  methods: {
    fetchJobcards() {
      axios.get('/api/jobcard').then(res => {
        this.jobcards = res.data;
      });
    },
    deleteJobcard(id) {
      if (confirm('Are you sure you want to delete this job card?')) {
        axios.delete(`/api/jobcard/${id}`).then(() => {
          this.jobcards = this.jobcards.filter(j => j.id !== id);
        });
      }
    },
    statusBadge(status) {
      switch (status) {
        case 'open': return 'badge badge-warning';
        case 'in-progress': return 'badge badge-primary';
        case 'completed': return 'badge badge-success';
        case 'cancelled': return 'badge badge-danger';
        default: return 'badge badge-secondary';
      }
    }
  }
};
</script>
