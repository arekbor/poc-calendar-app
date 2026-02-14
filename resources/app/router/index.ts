import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";
import LoginView from "@/views/LoginView.vue";
import RegisterView from "@/views/RegisterView.vue";
import { useAuthStore } from "@/stores/auth";
import NotFoundView from "@/views/NotFoundView.vue";
import InternalServerErrorView from "@/views/InternalServerErrorView.vue";
import DashboardView from "@/views/DashboardView.vue";
import ProfileView from "@/views/ProfileView.vue";
import AuthLayout from "@/components/AuthLayout.vue";

const routes: RouteRecordRaw[] = [
    {
        path: '/404',
        name: 'not-found',
        component: NotFoundView,
    },
    {
        path: '/500',
        name: 'internal-server-error',
        component: InternalServerErrorView
    },
    {
        path: '/login',
        name: 'login',
        component: LoginView,
        meta: { guestOnly: true }
    },
    {
        path: '/register',
        name: 'register',
        component: RegisterView,
        meta: { guestOnly: true }
    },
    {
        path: '/',
        component: AuthLayout,
        meta: { requireAuth: true },
        children: [
            {
                path: '',
                name: 'home',
                redirect: { name: 'dashboard' }
            },
            {
                path: 'dashboard',
                name: 'dashboard',
                component: DashboardView,
            },
            {
                path: 'profile',
                name: 'profile',
                component: ProfileView
            }
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        component: NotFoundView
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    if (to.matched.some((record) => record.meta.requireAuth) && !authStore.isLoggedIn) {
        next({ name: 'login', query: { redirect: to.fullPath } });
    } else if (to.matched.some((record) => record.meta.guestOnly) && authStore.isLoggedIn) {
        next({ name: 'dashboard' })
    } else {
        next();
    }
});

export default router;