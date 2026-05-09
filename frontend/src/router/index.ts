import { createRouter, createWebHistory } from 'vue-router'
import AdminHome from '@/pages/AdminHome.vue'
import Login from '@/pages/Login.vue'
import adminRoutes from '@/modules/admin/router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/', redirect: '/admin' },
  { path: '/login', name: 'Login', component: Login },
  ...adminRoutes
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth) {
    if (!auth.user) {
      await auth.fetchUser()
    }
    if (!auth.user) return next({ name: 'Login' })
    // role guard
    if (to.meta.role && auth.user.role !== to.meta.role) return next({ name: 'Login' })
    return next()
  }
  return next()
})

export default router
