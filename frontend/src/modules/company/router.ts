import { RouteRecordRaw } from 'vue-router'
import CompanyLayout from '@/modules/company/components/CompanyLayout.vue'
import CompanyDashboard from '@/modules/company/pages/Dashboard.vue'
import MyCompany from '@/modules/company/pages/MyCompany.vue'
import CompanyDepartments from '@/modules/company/pages/Departments.vue'
import CompanyEmployees from '@/modules/company/pages/Employees.vue'
import CompanyVacancies from '@/modules/company/pages/Vacancies.vue'
import CompanyApplications from '@/modules/company/pages/Applications.vue'

const companyRoutes: Array<RouteRecordRaw> = [
  {
    path: '/company',
    component: CompanyLayout,
    meta: { requiresAuth: true, role: ['company', 'company_owner'] },
    children: [
      {
        path: '',
        name: 'CompanyDashboard',
        component: CompanyDashboard,
        meta: { requiresAuth: true, role: ['company', 'company_owner'] }
      },
      {
        path: 'my-company',
        name: 'CompanyProfile',
        component: MyCompany,
        meta: { requiresAuth: true, role: ['company', 'company_owner'] }
      },
      {
        path: 'departments',
        name: 'CompanyDepartments',
        component: CompanyDepartments,
        meta: { requiresAuth: true, role: ['company', 'company_owner'] }
      },
      {
        path: 'employees',
        name: 'CompanyEmployees',
        component: CompanyEmployees,
        meta: { requiresAuth: true, role: ['company', 'company_owner'] }
      },
      {
        path: 'vacancies',
        name: 'CompanyVacancies',
        component: CompanyVacancies,
        meta: { requiresAuth: true, role: ['company', 'company_owner'] }
      },
      {
        path: 'applications',
        name: 'CompanyApplications',
        component: CompanyApplications,
        meta: { requiresAuth: true, role: ['company', 'company_owner'] }
      }
    ]
  }
]

export default companyRoutes
