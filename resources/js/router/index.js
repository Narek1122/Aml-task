import { createRouter, createWebHistory } from 'vue-router';
import { useStore } from 'vuex';
import MainLayout from '../components/MainLayout.vue';

const routes = [
    {
        path: '/',
        component: MainLayout, // Set the layout for all routes
        children: [
            { path: '', component: () => import('../pages/Home.vue'), name: 'home' },
            { path: '/login', component: () => import('../pages/Login.vue'), name: 'login' },
            { path: '/register', component: () => import('../pages/Register.vue'), name: 'register' },
            {
                path: '/profile',
                name: 'profile',
                component: () => import('../pages/Profile.vue'),
                meta: { requiresAuth: true },
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

const isAuthenticated = () => {
    return !!localStorage.getItem('token');
};

router.beforeEach((to, from, next) => {
    const token = isAuthenticated();

    if ((to.name === 'login' || to.name === 'register') && token) {
        return next({ name: 'profile' });
    }

    if (to.matched.some(record => record.meta.requiresAuth) && !token) {
        return next({ name: 'login' });
    }

    next();
});

export default router;
