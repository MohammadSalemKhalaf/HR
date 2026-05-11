import { RouteRecordRaw } from 'vue-router'
import AdminLayout from '@/modules/admin/components/AdminLayout.vue'
import Dashboard from '@/modules/admin/pages/Dashboard.vue'
import UsersList from '@/modules/admin/pages/users/UsersList.vue'
import UserEdit from '@/modules/admin/pages/users/UserEdit.vue'
import CompaniesList from '@/modules/admin/pages/companies/CompaniesList.vue'
import CompanyCreate from '@/modules/admin/pages/companies/CompanyCreate.vue'
import CompanyEdit from '@/modules/admin/pages/companies/CompanyEdit.vue'
import EmployeesList from '@/modules/admin/pages/employees/EmployeesList.vue'
import JobCategoriesList from '@/modules/admin/pages/job-categories/JobCategoriesList.vue'
import JobCategoryCreate from '@/modules/admin/pages/job-categories/JobCategoryCreate.vue'
import JobCategoryEdit from '@/modules/admin/pages/job-categories/JobCategoryEdit.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: Dashboard,
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'users',
        name: 'UsersList',
        component: UsersList,
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'users/:id/edit',
        name: 'UserEdit',
        component: UserEdit,
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'companies',
        name: 'CompaniesList',
        component: CompaniesList,
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'companies/create',
        name: 'CompanyCreate',
        component: CompanyCreate,
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'companies/:id/edit',
        name: 'CompanyEdit',
        component: CompanyEdit,
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'job-categories',
        name: 'JobCategoriesList',
        component: JobCategoriesList,
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'job-categories/create',
        name: 'JobCategoryCreate',
        component: JobCategoryCreate,
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'job-categories/:id/edit',
        name: 'JobCategoryEdit',
        component: JobCategoryEdit,
        meta: { requiresAuth: true, role: 'admin' }
      },
      {
        path: 'employees',
        name: 'AdminEmployees',
        component: EmployeesList,
        meta: { requiresAuth: true, role: 'admin' }
      }
    ]
  }
]

export default routes

