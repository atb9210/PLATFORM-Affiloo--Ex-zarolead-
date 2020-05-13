<template>
    <div class="main-wrapper">
		<div class="page-wrapper full-page">
			<div class="page-content d-flex align-items-center justify-content-center">

				<div class="row w-100 mx-0 auth-page">
					<div class="col-md-8 col-xl-6 mx-auto">
						<div class="card">
							<div class="row">
                                <div class="col-md-4 pr-md-0">
                                    <div class="auth-left-wrapper">

                                    </div>
                                </div>
                                <div class="col-md-8 pl-md-0">
                                    <div class="auth-form-wrapper px-4 py-5">
                                        <a href="#" class="noble-ui-logo logo-light d-block mb-2">Zaro<span>Lead</span></a>
                                        <h5 class="text-muted font-weight-normal mb-4">Welcome back! Log in to your account.</h5>
                                        <form class="forms-sample" method="post" action="#" @submit.prevent="login">
                                            <div class="form-group">
                                                <label for="email">Email address</label>
                                                <input v-model="email" @keyup="onEmailChange" id="email" type="email" class="form-control" :class="(this.errors.email != '') ? 'is-invalid' : ''" name="email" placeholder="Email address" required autofocus>

                                                <span v-if="errors.email != ''" class="invalid-feedback" role="alert">
                                                    <strong>{{ errors.email }}</strong>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input v-model="password" @keyup="onPasswordChange" id="password" type="password" class="form-control" :class="(errors.password != '') ? 'is-invalid' : ''" name="password" autocomplete="current-password" placeholder="Password" required>

                                                <span v-if="errors.password != ''" class="invalid-feedback" role="alert">
                                                    <strong>{{ errors.password }}</strong>
                                                </span>
                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input name="remember" type="checkbox" class="form-check-input" id="remember">
                                                    Remember me
                                                </label>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-primary mr-2 mb-2 mb-md-0 text-white">
                                                    Login
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0" disabled>
                                                    <i class="btn-icon-prepend" data-feather="twitter"></i>
                                                    Login with twitter
                                                </button>
                                            </div>
                                            <router-link to="register">
                                                <a class="d-block mt-3 text-muted">Not a user? Sign up</a>
                                            </router-link>
                                        </form>
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

<script>
    export default {
        data() {
            return {
                email: '',
                password: '',

                errors: {
                    email: '',
                    password: '',
                },
            }
        },

        methods: {
            onEmailChange() {
                this.errors.email = '';
            },

            onPasswordChange() {
                this.errors.password = '';
            },

            login() {
                axios.post('/api/v1/login', {
                    email: this.email,
                    password: this.password
                }).then(function(response) {
                    this.$store.state.authenticated = true;
                    this.$store.state.accessToken = response.access_token;
                    this.$cookies.set('accessToken', response.access_token);
                }.bind(this)).catch(function(errors) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1113000
                    });

                    Toast.fire({
                        icon: 'error',
                        title: 'Invalid credentials'
                    });
                }.bind(this));
            }
        },

        mounted() {
            console.log('Login page.');
        }
    }
</script>
