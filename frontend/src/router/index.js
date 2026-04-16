import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Dashboard',
      component: () => import('../views/Dashboard.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/login',
      name: 'Login',
      component: () => import('../views/Login.vue'),
    },
    {
      path: '/register',
      name: 'Register',
      component: () => import('../views/Register.vue'),
    },
    {
      path: '/requests/create',
      name: 'CreateRequest',
      component: () => import('../views/CreateRequest.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/requests/:id',
      name: 'RequestDetail',
      component: () => import('../views/RequestDetail.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/suppliers',
      name: 'Suppliers',
      component: () => import('../views/Suppliers.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/management',
      name: 'Management',
      component: () => import('../views/AdminPanel.vue'),
      meta: { requiresAuth: true, requiresDirecteur: true },
    },
  ],
});

router.beforeEach(async (to) => {
  const authStore = useAuthStore();
  if (authStore.token && !authStore.user) {
    authStore.setAuthHeader();
    await authStore.fetchUser();
  }
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return '/login';
  }
  if (to.meta.requiresDirecteur && authStore.user?.role !== 'directeur') {
    return '/';
  }
  if (authStore.isAuthenticated && (to.path === '/login' || to.path === '/register')) {
    return '/';
  }
});

export default router;
