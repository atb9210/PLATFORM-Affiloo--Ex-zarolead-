<template>
    <div class="main-wrapper">

        <Sidebar />
	
		<div class="page-wrapper">

            <Navbar />

            <div class="page-content">
                <Overview v-if="page == 'overview'" :orders="orders" />
			</div>

            <Footer />

		</div>
	</div>
</template>

<script>
    import router from './../routes.js';

    import Sidebar from './../components/Sidebar';
    import Navbar from './../components/Navbar';
    import Footer from './../components/Footer';

    import Overview from './../components/Overview';

    export default {
        name: 'Dashboard',

        components: {
            Sidebar,
            Navbar,
            Footer,

            Overview,
        },

        data() {
            return {
                page: '',

                orders: [],
            };
        },

        mounted() {
            console.log('Dashboard page.')

            console.log('Loading orders...');
            axios.get('/api/v1/orders', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + this.$store.state.accessToken,
                },
            }).then(function(response) {
                this.orders = response.data.data;
                this.page = 'overview';
            }.bind(this)).catch(function(errors) {
                console.log(errors);
            }.bind(this));
        }
    }
</script>
