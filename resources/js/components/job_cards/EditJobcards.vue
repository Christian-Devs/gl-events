<template>
  <div>
    <div class="row">
      <router-link to="/jobcards" class="btn btn-primary">All Job Cards</router-link>
    </div>
    <div class="row justify-content-center">
      <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Edit Job Card</h1>
                  </div>
                  <form class="user" @submit.prevent="updateJobCard">
                    <div class="form-group">
                      <input type="text" class="form-control" :value="quoteRef" disabled />
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control" placeholder="Assigned To" v-model="form.assigned_to" />
                    </div>
                    <div class="form-group">
                      <select class="form-control" v-model="form.status">
                        <option value="open">Open</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                      </select>
                    </div>
                    <div class="form-row">
                      <div class="col-md-6">
                        <input type="date" class="form-control" placeholder="Start Date" v-model="form.start_date" />
                      </div>
                      <div class="col-md-6">
                        <input type="date" class="form-control" placeholder="Due Date" v-model="form.due_date" />
                      </div>
                    </div>
                    <div class="form-group mt-3">
                      <textarea class="form-control" rows="4" placeholder="Notes..." v-model="form.notes"></textarea>
                    </div>
                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">Update Job Card</button>
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

<script>
export default {
  data() {
    return {
      form: {
        assigned_to: '',
        status: '',
        start_date: '',
        due_date: '',
        notes: ''
      },
      quoteRef: ''
    };
  },
  created() {
    this.loadJobcard();
  },
  methods: {
    loadJobcard() {
      axios.get(`/api/jobcard/${this.$route.params.id}`).then(res => {
        const jc = res.data;
        this.form.assigned_to = jc.assigned_to;
        this.form.status = jc.status;
        this.form.start_date = jc.start_date;
        this.form.due_date = jc.due_date;
        this.form.notes = jc.notes;
        this.quoteRef = `Quote #${jc.quote?.id || ''} - ${jc.quote?.client_name || ''}`;
      });
    },
    updateJobCard() {
      axios.put(`/api/jobcard/${this.$route.params.id}`, this.form).then(() => {
        this.$router.push({ name: 'jobcards' });
      });
    }
  }
};
</script>
