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
                                    <form class="jobcard" @submit.prevent="jobcardInsert" enctype="multipart/form-data">
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
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Description"
                                                        v-model="material.description">
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Width"
                                                        v-model="material.width">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Height"
                                                        v-model="material.height">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total m²"
                                                        v-model="material.total_sqm">
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Fabric Banner Type"
                                                        v-model="material.banner_type">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="outsourced">Out-sourced: </label>
                                                    <select name="outsourced" id="outsourced"
                                                        v-model="material.outsourced">
                                                        <option value="true">Yes</option>
                                                        <option selected="selected" value="false">No</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Machine"
                                                        v-model="material.machine">
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Print Medium / m²"
                                                        v-model="material.print_medium_sqm">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Print Medium"
                                                        v-model="material.total_print_medium">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Ink / m²"
                                                        v-model="material.ink_sqm">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Ink"
                                                        v-model="material.total_ink">
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-6">
                                                    <label for="outsourced">Pinnacle Digital Fabric Printing:</label>
                                                    <select name="outsourced" id="outsourced" v-model="material.pdfp">
                                                        <option value="true">Yes</option>
                                                        <option selected>No</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Pinnacle"
                                                        v-model="material.total_pinnacle">
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Silicon Welt Length"
                                                        v-model="material.silicon_welt_length">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Unit Price / m"
                                                        v-model="material.sw_unit_price_m">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Silicon Welt"
                                                        v-model="material.total_silicon_welt">
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Flat Chonk Length"
                                                        v-model="material.flat_chonk_length">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Unit Price / m"
                                                        v-model="material.fc_unit_price_m">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Flat Chonk"
                                                        v-model="material.total_flat_chonk">
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Eyelets Length"
                                                        v-model="material.eyelets_length">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Unit Price / m"
                                                        v-model="material.eyelets_unit_price_m">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Eyelets"
                                                        v-model="material.total_eyelets">
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp"
                                                        placeholder="Webbing / Velcro Length"
                                                        v-model="material.web_vel_length">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Unit Price / m"
                                                        v-model="material.webvel_unit_price_m">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp"
                                                        placeholder="Total Webbing / Velcro"
                                                        v-model="material.total_webvel">
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp"
                                                        placeholder="Pull-up Banner Unit Price"
                                                        v-model="material.pullup_unit_price">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp"
                                                        placeholder="Total Pull-up Banner Hardware"
                                                        v-model="material.pullup_total_hardware">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Unit Price / m²"
                                                        v-model="material.pullup_unit_price_sqm">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Magnetic"
                                                        v-model="material.total_magnetic">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Consumables"
                                                        v-model="material.consumables">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Unit Price / Item"
                                                        v-model="material.unit_price_item">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-1">
                                                    <button class="btn btn-success form-control"
                                                        @click="addMaterials">Add
                                                        Material</button>
                                                </div>
                                            </div>
                                            <div class="pt-4 form-row align-items-center">
                                                <div class="col-md-6">
                                                    <h4>Total Amount: </h4>
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="R0.00"
                                                        v-model="form.total_amount">
                                                    <small class="text-danger" v-if="errors.total_amount">
                                                        {{ errors.total_amount[0] }}
                                                    </small>
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
                job_number: null,
                salesperson: null,
                stand_name: null,
                show_name: null,
                materials: [{
                    quantity: null,
                    description: null,
                    width: null,
                    height: null,
                    total_sqm: null,
                    banner_type: null,
                    outsourced: null,
                    machine: null,
                    print_medium_sqm: null,
                    total_print_medium: null,
                    ink_sqm: null,
                    total_ink: null,
                    pdfp: null,
                    total_pinnacle: null,
                    silicon_welt_length: null,
                    sw_unit_price_m: null,
                    total_silicon_welt: null,
                    flat_chonk_length: null,
                    fc_unit_price_m: null,
                    total_flat_chonk: null,
                    eyelets_length: null,
                    eyelets_unit_price_m: null,
                    total_eyelets: null,
                    web_vel_length: null,
                    webvel_unit_price_m: null,
                    total_webvel: null,
                    pullup_unit_price: null,
                    pullup_total_hardware: null,
                    pullup_unit_price_sqm: null,
                    total_magnetic: null,
                    consumables: null,
                    unit_price_item: null
                }],
                total_amount: null
            },
            errors: {}
        }
    },

    methods: {
        jobcardInsert() {
            axios.post('/api/jobcard', this.form)
                .then(() => {
                    this.$router.push({ name: 'jobcards' })
                    Notification.success()
                })
                .catch(error => {
                    if (error.response && error.response.data && error.response.data.errors) {
                        this.errors = error.response.data.errors;

                        // ✅ Ensure errors.materials is an array
                        if (!Array.isArray(this.errors.materials)) {
                            this.errors.materials = [];
                        }
                    } else {
                        this.errors = {};
                    }
                });
        },

        addMaterials() {
            this.form.materials.push({
                quantity: null,
                description: null,
                width: null,
                height: null,
                total_sqm: null,
                banner_type: null,
                outsourced: null,
                machine: null,
                print_medium_sqm: null,
                total_print_medium: null,
                ink_sqm: null,
                total_ink: null,
                pdfp: null,
                total_pinnacle: null,
                silicon_welt_length: null,
                sw_unit_price_m: null,
                total_silicon_welt: null,
                flat_chonk_length: null,
                fc_unit_price_m: null,
                total_flat_chonk: null,
                eyelets_length: null,
                eyelets_unit_price_m: null,
                total_eyelets: null,
                web_vel_length: null,
                webvel_unit_price_m: null,
                total_webvel: null,
                pullup_unit_price: null,
                pullup_total_hardware: null,
                pullup_unit_price_sqm: null,
                total_magnetic: null,
                consumables: null,
                unit_price_item: null
            });
            this.errors.materials = this.errors.materials || [];
            this.errors.materials.push({});
        },

        removeMaterials(index) {
            this.form.materials.splice(index, 1);
        },


    }

}
</script>


<style type="text/css"></style>