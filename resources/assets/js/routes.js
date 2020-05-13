import VueRouter from 'vue-router';

import {store} from './store';

import Home from './views/Home';
import Login from './views/Login';
import Register from './views/Register';
import Dashboard from './views/Dashboard';

let routes = [
    {
        path: '/',
        component: Home,
        meta: {
            requiresAuth: false,
        },
    },
    {
        path: '/login',
        component: Login,
        meta: {
            requiresAuth: false,
        },
    },
    {
        path: '/register',
        component: Register,
        meta: {
            requiresAuth: false,
        },
    },
    {
        path: '/dashboard',
        component: Dashboard,
        meta: {
            requiresAuth: false,
        },
    },
];

const router = new VueRouter({
    routes: routes,
});

router.beforeEach(async function(to, from, next) {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        var hasPermission = await store.dispatch("isAuthenticated");
        console.log("hasPermission:");
        console.log(hasPermission);
        if (hasPermission) {
            next();
        } else {
            next({
                path: "/login",
            });
        }
    } else {
        next();
    }
});

export default router;
