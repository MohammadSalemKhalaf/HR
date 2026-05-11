import JobSeekerRegister from '@/pages/jobseeker/JobSeekerRegister.vue'
import JobListings from '@/pages/jobseeker/JobListings.vue'
import JobDetail from '@/pages/jobseeker/JobDetail.vue'
import JobApply from '@/pages/jobseeker/JobApply.vue'
import MyApplications from '@/pages/jobseeker/MyApplications.vue'

export const jobSeekerRoutes = [
  {
    path: '/register',
    name: 'JobSeekerRegister',
    component: JobSeekerRegister,
    meta: { layout: 'blank' }
  },
  {
    path: '/jobs',
    name: 'JobListings',
    component: JobListings,
    meta: { requiresAuth: true, roles: ['job_seeker'] }
  },
  {
    path: '/jobs/:id',
    name: 'JobDetail',
    component: JobDetail,
    meta: { requiresAuth: true, roles: ['job_seeker'] }
  },
  {
    path: '/jobs/:id/apply',
    name: 'JobApply',
    component: JobApply,
    meta: { requiresAuth: true, roles: ['job_seeker'] }
  },
  {
    path: '/my-applications',
    name: 'MyApplications',
    component: MyApplications,
    meta: { requiresAuth: true, roles: ['job_seeker'] }
  }
]
