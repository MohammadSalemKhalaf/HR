import { RouteRecordRaw } from 'vue-router'
import AdminHome from '@/pages/AdminHome.vue'

const adminRoutes: Array<RouteRecordRaw> = [
  {
    path: '/admin',
    name: 'AdminHome',
    component: AdminHome,
    meta: { requiresAuth: true }
  }
]

export default adminRoutes
