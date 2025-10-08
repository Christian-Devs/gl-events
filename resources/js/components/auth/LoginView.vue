<template>
    <div>
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-header justify-content-center text-center" style="background-color: black">
                        <img src="backend/img/logo/logo.png">
                        <h2 class="text-white mt-2" style="font-weight: bolder;">GL Events SA - Business Management
                            System</h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">
                                            Login
                                        </h1>
                                    </div>
                                    <form class="user" @submit.prevent="login">
                                        <div class="form-group">
                                            <label for="exampleInputEmail">Email Address</label>
                                            <input type="email" class="form-control" id="exampleInputEmail"
                                                aria-describedby="emailHelp" placeholder="Enter Email Address"
                                                v-model="form.email" />
                                            <small class="text-danger" v-if="errors.email">
                                                {{ errors.email[0] }}
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <label for="exampleInputPassword">Password</label>
                                                <input :type="show1 ? 'text' : 'password'" class="form-control"
                                                    v-model.trim="form.password" autocomplete="new-password"
                                                    :disabled="submitting" minlength="6" required />
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        @click="show1 = !show1">
                                                        <i :class="['fas', show1 ? 'fa-eye-slash' : 'fa-eye']"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-danger" v-if="errors.password">
                                                {{ errors.password[0] }}
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                                        </div>
                                    </form>
                                    <hr />
                                    <div class="text-center">
                                        <router-link class="font-weight-bold small" to="/forgotPassword">Forgot
                                            Password?</router-link>
                                    </div>
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

export default {
    created() {
        if (User.loggedIn()) {
            this.$router.push({ name: 'home' })
        }
        this.updateTitle('Login')
    },
    data() {
        return {
            form: {
                email: null,
                password: null
            },
            show1: false,
            errors: {

            }
        }
    },
    methods: {
        login() {
            axios.post('api/auth/login', this.form)
                .then(res => {
                    const { access_token, force_password_change } = res.data;

                    if (access_token) {
                        localStorage.setItem('token', access_token);
                    }
                    if (force_password_change) {
                        this.$router.push({ name: 'change-password' });
                        return;
                    }
                    User.responseAfterLogin(res)
                    Toast.fire({
                        icon: "success",
                        title: "Signed in successfully"
                    })
                    this.$router.push({ name: 'home' })
                })
                .catch(err => this.errors = err.response.data)
                .catch(
                    Toast.fire({
                        icon: "error",
                        title: "Invalid login credentials"
                    })
                )
        }
    }
}
</script>

<style type="text/css"></style>
