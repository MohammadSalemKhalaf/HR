import { RouteRecordRaw } from 'vue-router'
import AdminLayout from '@/modules/admin/components/AdminLayout.vue'
import Dashboard from '@/modules/admin/pages/Dashboard.vue'
import CompaniesList from '@/modules/admin/pages/companies/CompaniesList.vue'
import DepartmentsList from '@/modules/admin/pages/departments/DepartmentsList.vue'
import EmployeesList from '@/modules/admin/pages/employees/EmployeesList.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      { path: '', name: 'AdminDashboard', component: Dashboard },
      { path: 'companies', name: 'CompaniesList', component: CompaniesList },
      { path: 'departments', name: 'DepartmentsList', component: DepartmentsList },
      { path: 'employees', name: 'EmployeesList', component: EmployeesList }
    ]
  }
]

export default routes
