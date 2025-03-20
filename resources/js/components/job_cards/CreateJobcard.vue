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
                                        <h1 class="h4 text-gray-900 mb-4">Create Job Card</h1>
                                    </div>
                                    <form class="user" @submit.prevent="jobcardInsert" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="exampleInputFirstName"
                                                        placeholder="Enter Job Number" v-model="form.job_number">
                                                    <small class="text-danger" v-if="errors.job_number">
                                                        {{ errors.job_number[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control"
                                                        id="exampleInputPasswordRepeat" placeholder="Salesperson"
                                                        v-model="form.salesperson">
                                                    <small class="text-danger" v-if="errors.salesperson">
                                                        {{ errors.salesperson[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <!--End Row-->
                                        </div>

                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="exampleInputPassword"
                                                        placeholder="Enter Stand Name" v-model="form.stand_name">
                                                    <small class="text-danger" v-if="errors.stand_name">
                                                        {{ errors.stand_name[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Enter Show Name"
                                                        v-model="form.show_name">
                                                    <small class="text-danger" v-if="errors.show_name">
                                                        {{ errors.show_name[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" v-for="(material, index) in form.materials"
                                        :key="index">
                                            <div class="form-row align-items-center">
                                                <div class="col-md-11">
                                                    <h6><b>Material {{ index + 1 }}</b></h6>
                                                </div>
                                                <div class="col-1 mb-2 ">
                                                    <button class="btn btn-danger form-control"
                                                        @click="removeMaterials(index)">Remove</button>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="exampleInputPassword"
                                                        placeholder="Quantity" v-model="material.quantity">
                                                    <small class="text-danger" v-if="errors.quantity">
                                                        {{ errors.quantity[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Description"
                                                        v-model="material.description">
                                                    <small class="text-danger" v-if="errors.description">
                                                        {{ errors.description[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-1">
                                                    <button class="btn btn-success form-control" @click="addMaterials">Add
                                                        Material</button>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block">Create
                                                Jobcard</button>
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
                name: null,
                nid: null,
                phone: null,
                email: null,
                joining_date: null,
                photo: null,
                materials: [{ quantity: '', description: '' }],
            },
            errors: {}
        }
    },

    methods: {
        addMaterials() {
            this.form.materials.push({ quantity: '', description: '' });
        },
        removeMaterials(index) {
            this.form.materials.splice(index, 1);
        },
        jobcardInsert() {
            axios.post('/api/employee', this.form)
                .then(() => {
                    this.$router.push({ name: 'employees' })
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