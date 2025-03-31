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
                                                    <small class="text-danger" v-if="errors.quantity">
                                                        {{ errors.quantity[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="outsourced">Description: </label>
                                                    <select name="description" id="description" v-model="material.description">
                                                        <option value="fabric_banner">Fabric Banner</option>
                                                        <option value="full_vinyl_print">Full Vinyl Print</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.description">
                                                        {{ errors.description[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Width"
                                                        v-model="material.width">
                                                    <small class="text-danger" v-if="errors.width">
                                                        {{ errors.width[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Height"
                                                        v-model="material.height">
                                                    <small class="text-danger" v-if="errors.height">
                                                        {{ errors.height[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" disabled class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total m²"
                                                        v-model="material.total_sqm">
                                                    <small class="text-danger" v-if="errors.total_sqm">
                                                        {{ errors.total_sqm[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Fabric Banner Type"
                                                        v-model="material.banner_type">
                                                    <small class="text-danger" v-if="errors.banner_type">
                                                        {{ errors.banner_type[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="outsourced">Out-sourced: </label>
                                                    <select name="outsourced" id="outsourced" v-model="material.outsourced">
                                                        <option value="true">Yes</option>
                                                        <option selected="selected" value="false">No</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.outsourced">
                                                        {{ errors.outsourced[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Machine"
                                                        v-model="material.machine">
                                                    <small class="text-danger" v-if="errors.machine">
                                                        {{ errors.machine[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Print Medium"
                                                        v-model="material.total_print_medium">
                                                    <small class="text-danger" v-if="errors.total_print_medium">
                                                        {{ errors.total_print_medium[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Ink"
                                                        v-model="material.total_ink">
                                                    <small class="text-danger" v-if="errors.total_ink">
                                                        {{ errors.total_ink[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-6">
                                                    <label for="outsourced">Pinnacle Digital Fabric Printing:</label>
                                                    <select name="outsourced" id="outsourced" v-model="material.pdfp">
                                                        <option value="yes">Yes</option>
                                                        <option selected value="no">No</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.pdfp">
                                                        {{ errors.pdfp[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6" v-if="material.pdfp == 'yes'">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Pinnacle"
                                                        v-model="material.total_pinnacle">
                                                    <small class="text-danger" v-if="errors.total_pinnacle">
                                                        {{ errors.total_pinnacle[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <label for="outsourced">Silicon Welt:</label>
                                                    <select name="outsourced" id="outsourced" v-model="material.silicon_welt">
                                                        <option value="yes">Yes</option>
                                                        <option selected value="no">No</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.pdfp">
                                                        {{ errors.pdfp[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4" v-if="material.silicon_welt == 'yes'">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Silicon Welt Length"
                                                        v-model="material.silicon_welt_length">
                                                    <small class="text-danger" v-if="errors.silicon_welt_length">
                                                        {{ errors.silicon_welt_length[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4" v-if="material.silicon_welt == 'yes'">
                                                    <input type="text" disabled class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Silicon Welt"
                                                        v-model="material.total_silicon_welt">
                                                    <small class="text-danger" v-if="errors.total_silicon_welt">
                                                        {{ errors.total_silicon_welt[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <label for="outsourced">Flat Chonk:</label>
                                                    <select name="outsourced" id="outsourced" v-model="material.flat_chonk">
                                                        <option value="yes">Yes</option>
                                                        <option selected value="no">No</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.pdfp">
                                                        {{ errors.pdfp[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4" v-if="material.flat_chonk == 'yes'">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Flat Chonk Length"
                                                        v-model="material.flat_chonk_length">
                                                    <small class="text-danger" v-if="errors.flat_chonk_length">
                                                        {{ errors.flat_chonk_length[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4" v-if="material.flat_chonk == 'yes'">
                                                    <input type="text" disabled class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Flat Chonk"
                                                        v-model="material.total_flat_chonk">
                                                    <small class="text-danger" v-if="errors.total_flat_chonk">
                                                        {{ errors.total_flat_chonk[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <label for="outsourced">Eyelets:</label>
                                                    <select name="outsourced" id="outsourced" v-model="material.eyelets">
                                                        <option value="yes">Yes</option>
                                                        <option selected value="no">No</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.eyelets">
                                                        {{ errors.eyelets[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4" v-if="material.eyelets == 'yes'">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Eyelets Length"
                                                        v-model="material.eyelets_length">
                                                    <small class="text-danger" v-if="errors.eyelets_length">
                                                        {{ errors.eyelets_length[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4" v-if="material.eyelets == 'yes'">
                                                    <input type="text" disabled class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Eyelets"
                                                        v-model="material.total_eyelets">
                                                    <small class="text-danger" v-if="errors.total_eyelets">
                                                        {{ errors.total_eyelets[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-4">
                                                    <label for="outsourced">Webbing/Velcro:</label>
                                                    <select name="outsourced" id="outsourced" v-model="material.webvel">
                                                        <option value="yes">Yes</option>
                                                        <option selected value="no">No</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.eyelets">
                                                        {{ errors.eyelets[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4" v-if="material.webvel == 'yes'">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Webbing / Velcro Length"
                                                        v-model="material.web_vel_length">
                                                    <small class="text-danger" v-if="errors.web_vel_length">
                                                        {{ errors.web_vel_length[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4" v-if="material.webvel == 'yes'">
                                                    <input type="text" disabled class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Webbing / Velcro"
                                                        v-model="material.total_webvel">
                                                    <small class="text-danger" v-if="errors.total_webvel">
                                                        {{ errors.total_webvel[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-2">
                                                    <label for="outsourced">Pull Up Banner: </label>
                                                    <select name="outsourced" id="outsourced" v-model="material.pullup_banner">
                                                        <option value="yes">Yes</option>
                                                        <option selected value="no">No</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.pdfp">
                                                        {{ errors.pdfp[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-2" v-if="material.pullup_banner == 'yes'">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Pull-up Banner Hardware"
                                                        v-model="material.pullup_total_hardware">
                                                    <small class="text-danger" v-if="errors.pullup_total_hardware">
                                                        {{ errors.pullup_total_hardware[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="outsourced">Magnetic: </label>
                                                    <select name="outsourced" id="outsourced" v-model="material.magnetic">
                                                        <option value="yes">Yes</option>
                                                        <option selected value="no">No</option>
                                                    </select>
                                                    <small class="text-danger" v-if="errors.pdfp">
                                                        {{ errors.pdfp[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-2" v-if="material.magnetic == 'yes'">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Total Magnetic"
                                                        v-model="material.total_magnetic">
                                                    <small class="text-danger" v-if="errors.total_magnetic">
                                                        {{ errors.total_magnetic[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Consumables"
                                                        v-model="material.consumables">
                                                    <small class="text-danger" v-if="errors.consumables">
                                                        {{ errors.consumables[0] }}
                                                    </small>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="Unit Price / Item"
                                                        v-model="material.unit_price_item">
                                                    <small class="text-danger" v-if="errors.unit_price_item">
                                                        {{ errors.unit_price_item[0] }}
                                                    </small>
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
                                        </div>
                                        <div class="form-group">
                                            <div class="pt-2 form-row align-items-center">
                                                <div class="col-md-6">
                                                    <h6>Total Amount: </h6>
                                                    <input type="text" class="form-control" id="exampleInputEmail"
                                                        aria-describedby="emailHelp" placeholder="R0.00"
                                                        v-model="form.total_amount">
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
                    quantity: '', 
                    description: '',
                    width: '',
                    height: '',
                    total_sqm: '',
                    banner_type: '',
                    outsourced: '',
                    machine: '',
                    total_print_medium: '',
                    total_ink: '',
                    pdfp: '',
                    total_pinnacle: '',
                    silicon_welt: '',
                    silicon_welt_length: '',
                    total_silicon_welt: '',
                    flat_chonk: '',
                    flat_chonk_length: '',
                    total_flat_chonk: '',
                    eyelets: '',
                    eyelets_length: '',
                    total_eyelets: '',
                    webvel: '',
                    web_vel_length: '',
                    total_webvel: '',
                    pullup_banner: '',
                    pullup_total_hardware: '',
                    magnetic: '',
                    total_magnetic: '',
                    consumables: '',
                    unit_price_item: ''
                }],
                total_amount: ''
            },
            errors: {}
        }
    },

    methods: {
        addMaterials() {
            this.form.materials.push({ quantity: '', 
                    description: '',
                    width: '',
                    height: '',
                    total_sqm: '',
                    banner_type: '',
                    outsourced: '',
                    machine: '',
                    print_medium_sqm: '',
                    total_print_medium: '',
                    ink_sqm: '',
                    total_ink: '',
                    pdfp: '',
                    total_pinnacle: '',
                    silicon_welt_length: '',
                    sw_unit_price_m: '',
                    total_silicon_welt: '',
                    flat_chonk_length: '',
                    fc_unit_price_m: '',
                    total_flat_chonk: '',
                    eyelets_length: '',
                    eyelets_unit_price_m: '',
                    total_eyelets: '',
                    web_vel_length: '',
                    webvel_unit_price_m: '',
                    total_webvel: '',
                    pullup_unit_price: '',
                    pullup_total_hardware: '',
                    pullup_unit_price_sqm: '',
                    total_magnetic: '',
                    consumables: '',
                    unit_price_item: ''
                });
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