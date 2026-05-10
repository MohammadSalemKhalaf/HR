import { RouteRecordRaw } from 'vue-router'
import EmployeeDashboard from '@/pages/employee/Dashboard.vue'
import TaskIndex from '@/pages/employee/tasks/TaskIndex.vue'
import TaskShow from '@/pages/employee/tasks/TaskShow.vue'
import AttendanceIndex from '@/pages/employee/attendance/AttendanceIndex.vue'
import LeaveIndex from '@/pages/employee/leaves/LeaveIndex.vue'
import ProfileShow from '@/pages/employee/profile/ProfileShow.vue'
import NotificationIndex from '@/pages/employee/notifications/NotificationIndex.vue'

const employeeRoutes: Array<RouteRecordRaw> = [
  {
    path: '/employee',
    name: 'EmployeeDashboard',
    component: EmployeeDashboard,
    meta: { requiresAuth: true, role: ['employee'] }
  },
  {
    path: '/employee/tasks',
    name: 'EmployeeTaskIndex',
    component: TaskIndex,
    meta: { requiresAuth: true, role: ['employee'] }
  },
  {
    path: '/employee/tasks/:id',
    name: 'EmployeeTaskShow',
    component: TaskShow,
    meta: { requiresAuth: true, role: ['employee'] }
  },
  {
    path: '/employee/attendance',
    name: 'EmployeeAttendanceIndex',
    component: AttendanceIndex,
    meta: { requiresAuth: true, role: ['employee'] }
  },
  {
    path: '/employee/leaves',
    name: 'EmployeeLeaveIndex',
    component: LeaveIndex,
    meta: { requiresAuth: true, role: ['employee'] }
  },
  {
    path: '/employee/leaves/apply',
    name: 'EmployeeLeaveCreate',
    component: LeaveIndex,
    meta: { requiresAuth: true, role: ['employee'] }
  },
  {
    path: '/employee/profile',
    name: 'EmployeeProfileShow',
    component: ProfileShow,
    meta: { requiresAuth: true, role: ['employee'] }
  },
  {
    path: '/employee/notifications',
    name: 'EmployeeNotificationIndex',
    component: NotificationIndex,
    meta: { requiresAuth: true, role: ['employee'] }
  }
]

export default employeeRoutes
