<template>
    <div class="flex-center position-ref full-height">
        <div class="top-right links">
            <router-link to="/dashboard">
                <a>Dashboard</a>
            </router-link>

            <router-link to="/login">
                <a>Login</a>
            </router-link>

            <router-link to="/login">
                <a>Register</a>
            </router-link>
        </div>

        <div class="content">
            <div class="title m-b-md">
                Zaro<span class="title-right-span">Lead</span>
            </div>

            <div class="links">
                <a href="https://laravel.com/docs"><i class="link-icon" data-feather="info"></i> Docs</a>
                <router-link to="/dashboard">
                    <a><i class="link-icon" data-feather="box"></i> Dashboard</a>
                </router-link>
                <a href="#"><i class="link-icon" data-feather="box"></i> News</a>
                <a href="#"><i class="link-icon" data-feather="box"></i> Forge</a>
                <a href="https://github.com/driverfury/zarolead"><i class="link-icon" data-feather="github"></i> GitHub</a>
            </div>
        </div>
    </div>
</template>

<script>
    import router from './../routes.js';

    export default {
        data() {
            return {
                shared: window.shared,
            };
        },

        mounted() {
            console.log('Home page.')
        }
    }
</script>

<style lang="scss" scoped>
@import 'https://fonts.googleapis.com/css?family=Nunito:200,600';
@import '/assets/vendors/core/core.css';
@import '/assets/fonts/feather-font/css/iconfont.css';
@import '/assets/vendors/flag-icon-css/css/flag-icon.min.css';
@import '/assets/css/demo_2/style.css';

html, body {
    background-color: #0c1427;
    color: #bfc3ce;
    font-family: 'Overpass', sans-serif;
    font-weight: 200;
    height: 100vh;
    margin: 0;
}

.full-height {
    height: 100vh;
}

.flex-center {
    align-items: center;
    display: flex;
    justify-content: center;
}

.position-ref {
    position: relative;
}

.top-right {
    position: absolute;
    right: 10px;
    top: 18px;
}

.content {
    text-align: center;
}

.title {
    font-size: 84px;
    font-weight: 900;
    color: #bfc3ce;
}

.title-right-span {
    font-weight: 300;
    color: #727cf5;
}

.links > a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
}

.m-b-md {
    margin-bottom: 30px;
}
</style>
