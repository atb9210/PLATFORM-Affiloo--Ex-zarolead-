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
                                        <h5 class="text-muted font-weight-normal mb-4">Create a free account.</h5>
                                        <form class="forms-sample" method="post" action="#" @submit.prevent="submit">
                                            <div class="form-group">
                                                <label for="name">Full name</label>
                                                <input v-model="name" @keyup="onNameChange" id="name" type="text" class="form-control" :class="(this.errors.name != '') ? 'is-invalid' : ''" name="name" placeholder="Full name" required autofocus>

                                                <span v-if="errors.name != ''" class="invalid-feedback" role="alert">
                                                    <strong>{{ errors.name }}</strong>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email address</label>
                                                <input v-model="email" @keyup="onEmailChange" id="email" type="email" class="form-control" :class="(this.errors.email != '') ? 'is-invalid' : ''" name="email" placeholder="Email address" required>

                                                <span v-if="errors.email != ''" class="invalid-feedback" role="alert">
                                                    <strong>{{ errors.email }}</strong>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input v-model="password" @keyup="onPasswordChange" id="password" type="password" class="form-control" :class="(this.errors.password != '') ? 'is-invalid' : ''" name="password" placeholder="Password" required>

                                                <span v-if="errors.password != ''" class="invalid-feedback" role="alert">
                                                    <strong>{{ errors.password }}</strong>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <label for="password-confirm">Password</label>
                                                <input v-model="password_confirmation" @keyup="onPasswordConfirmationChange" id="password-confirm" type="password" class="form-control" :class="(this.errors.password_confirmation != '') ? 'is-invalid' : ''" name="password_confirmation" placeholder="Repeat your password" required>

                                                <span v-if="errors.password_confirmation != ''" class="invalid-feedback" role="alert">
                                                    <strong>{{ errors.password_confirmation }}</strong>
                                                </span>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-primary text-white mr-2 mb-2 mb-md-0">
                                                    Sing up
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0" disabled>
                                                    <i class="btn-icon-prepend" data-feather="twitter"></i>
                                                    Sign up with twitter
                                                </button>
                                            </div>
                                            <router-link to="/login">
                                                <a class="d-block mt-3 text-muted">Already a user? Sign in</a>
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
                name: '',
                email: '',
                password: '',
                password_confirmation: '',

                errors: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                },
            };
        },

        methods: {
            onNameChange() {
                this.errors.name = '';
            },

            onEmailChange() {
                this.errors.email = '';
            },

            onPasswordChange() {
                this.errors.password = '';
                this.errors.password_confirmation = '';

                if (
                    this.password != ''
                    && this.password_confirmation != ''
                    && this.password != this.password_confirmation
                ) {
                    this.errors.password_confirmation = 'Passwords musth match!';
                } else if (
                    this.password != ''
                    && this.password_confirmation != ''
                    && this.password == this.password_confirmation
                ) {
                    this.errors.password_confirmation = '';
                }
            },

            onPasswordConfirmationChange() {
                if (
                    this.password != ''
                    && this.password_confirmation != ''
                    && this.password != this.password_confirmation
                ) {
                    this.errors.password_confirmation = 'Passwords musth match!';
                } else if (
                    this.password != ''
                    && this.password_confirmation != ''
                    && this.password == this.password_confirmation
                ) {
                    this.errors.password_confirmation = '';
                }
            },

            submit() {
                alert("submitting");
            },
        },

        mounted() {
            console.log('Register page.')
        }
    }
</script>
