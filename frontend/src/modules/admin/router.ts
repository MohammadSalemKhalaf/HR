import { RouteRecordRaw } from 'vue-router'
import AdminHome from '@/pages/AdminHome.vue'
import managerRoutes from '@/modules/manager/router'
import CompaniesList from '@/modules/admin/pages/companies/CompaniesList.vue'
import DepartmentsList from '@/modules/admin/pages/departments/DepartmentsList.vue'
import EmployeesList from '@/modules/admin/pages/employees/EmployeesList.vue'

const adminRoutes: Array<RouteRecordRaw> = [
  {
    path: '/admin',
    name: 'AdminHome',
    component: AdminHome,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/companies',
    name: 'AdminCompanies',
    component: CompaniesList,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/departments',
    name: 'AdminDepartments',
    component: DepartmentsList,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/employees',
    name: 'AdminEmployees',
    component: EmployeesList,
    meta: { requiresAuth: true }
  },
  ...managerRoutes
]

export default adminRoutes
