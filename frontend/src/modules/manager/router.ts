import { RouteRecordRaw } from 'vue-router'
import ManagerDashboard from '@/pages/manager/Dashboard.vue'
import TaskIndex from '@/pages/manager/tasks/TaskIndex.vue'
import TaskShow from '@/pages/manager/tasks/TaskShow.vue'
import TaskCreate from '@/pages/manager/tasks/TaskCreate.vue'
import TaskEdit from '@/pages/manager/tasks/TaskEdit.vue'
import EmployeeIndex from '@/pages/manager/employees/EmployeeIndex.vue'
import EmployeeShow from '@/pages/manager/employees/EmployeeShow.vue'
import DepartmentIndex from '@/pages/manager/departments/DepartmentIndex.vue'
import DepartmentShow from '@/pages/manager/departments/DepartmentShow.vue'
import LeaveIndex from '@/pages/manager/leaves/LeaveIndex.vue'
import AttendanceIndex from '@/pages/manager/attendance/AttendanceIndex.vue'
import NotificationIndex from '@/pages/manager/notifications/NotificationIndex.vue'

const managerRoutes: Array<RouteRecordRaw> = [
  {
    path: '/manager',
    name: 'ManagerDashboard',
    component: ManagerDashboard,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/tasks',
    name: 'ManagerTaskIndex',
    component: TaskIndex,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/tasks/create',
    name: 'ManagerTaskCreate',
    component: TaskCreate,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/tasks/:id',
    name: 'ManagerTaskShow',
    component: TaskShow,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/tasks/:id/edit',
    name: 'ManagerTaskEdit',
    component: TaskEdit,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/employees',
    name: 'ManagerEmployeeIndex',
    component: EmployeeIndex,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/employees/:id',
    name: 'ManagerEmployeeShow',
    component: EmployeeShow,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/departments',
    name: 'ManagerDepartmentIndex',
    component: DepartmentIndex,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/departments/:id',
    name: 'ManagerDepartmentShow',
    component: DepartmentShow,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/leaves',
    name: 'ManagerLeaveIndex',
    component: LeaveIndex,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/attendance',
    name: 'ManagerAttendanceIndex',
    component: AttendanceIndex,
    meta: { requiresAuth: true, role: ['manager'] }
  },
  {
    path: '/manager/department-notifications',
    name: 'ManagerNotificationIndex',
    component: NotificationIndex,
    meta: { requiresAuth: true, role: ['manager'] }
  }
]

export default managerRoutes
