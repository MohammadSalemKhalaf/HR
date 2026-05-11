import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Login from '../pages/Login.vue'
import adminRoutes from '../modules/admin/router/index'
import managerRoutes from '../modules/manager/router'
import companyRoutes from '../modules/company/router'
import employeeRoutes from '../modules/employee/router'
import jobsRoutes from '../modules/jobs/router'
import { jobSeekerRoutes } from '../modules/jobseeker/router'
import { useAuthStore } from '../stores/auth'

const routes: Array<RouteRecordRaw> = [
  { path: '/', redirect: '/login' },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { layout: 'auth', requiresGuest: true }
  },
  ...adminRoutes,
  ...companyRoutes,
  ...managerRoutes,
  ...employeeRoutes,
  ...jobsRoutes,
  ...jobSeekerRoutes,
  {
    path: '/:pathMatch(.*)*',
    redirect: '/login'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

/**
 * Global route guard - handles auth, guest, and role-based access
 */
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  // Guest-only routes (login, register, etc.)
  if (to.meta.requiresGuest) {
    if (auth.isAuthenticated) {
      // Redirect authenticated users to their dashboard
      return next(auth.getRedirectPath())
    }
    return next()
  }

  // Auth-required routes
  if (to.meta.requiresAuth) {
    // Restore auth if not loaded yet
    if (!auth.user && auth.token) {
      await auth.fetchUser()
    }

    // Not authenticated
    if (!auth.isAuthenticated) {
      return next({ name: 'Login' })
    }

    // Role-based access control
    if (to.meta.role) {
      const requiredRoles = Array.isArray(to.meta.role) ? to.meta.role : [to.meta.role]
      const userRole = auth.user?.role

      if (!requiredRoles.includes(userRole)) {
        console.warn(`Access denied: User role '${userRole}' does not match required roles:`, requiredRoles)
        return next({ name: 'Login' })
      }
    }

    // Handle roles array (for job seeker and other new roles)
    if (to.meta.roles) {
      const requiredRoles = Array.isArray(to.meta.roles) ? to.meta.roles : [to.meta.roles]
      const userRole = auth.user?.role

      if (!requiredRoles.includes(userRole)) {
        console.warn(`Access denied: User role '${userRole}' does not match required roles:`, requiredRoles)
        return next({ name: 'Login' })
      }
    }
  }

  return next()
})

export default router

