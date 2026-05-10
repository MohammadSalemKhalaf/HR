import { RouteRecordRaw } from 'vue-router'
import JobsIndex from '@/pages/jobs/JobsIndex.vue'
import JobShow from '@/pages/jobs/JobShow.vue'
import JobApply from '@/pages/jobs/JobApply.vue'
import ApplicationsIndex from '@/pages/jobs/ApplicationsIndex.vue'

const jobsRoutes: Array<RouteRecordRaw> = [
  {
    path: '/jobs',
    name: 'JobsIndex',
    component: JobsIndex,
    meta: { requiresAuth: true, role: ['job_seeker'] }
  },
  {
    path: '/jobs/:id',
    name: 'JobShow',
    component: JobShow,
    meta: { requiresAuth: true, role: ['job_seeker'] }
  },
  {
    path: '/jobs/:id/apply',
    name: 'JobApply',
    component: JobApply,
    meta: { requiresAuth: true, role: ['job_seeker'] }
  },
  {
    path: '/my-applications',
    name: 'MyApplications',
    component: ApplicationsIndex,
    meta: { requiresAuth: true, role: ['job_seeker'] }
  }
]

export default jobsRoutes
