<template>
  <div id="drop-overlay" v-show="dragActive" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center">
    <div class="border-4 border-dashed border-indigo-500 rounded-3xl p-12 flex flex-col items-center gap-4">
      <svg class="w-16 h-16 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-8m0 0-3 3m3-3 3 3M4.5 19.5h15A2.25 2.25 0 0021.75 17.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" />
      </svg>
      <p class="text-indigo-200 text-xl font-bold">Drop your resume here</p>
      <p class="text-indigo-300 text-sm">Release to attach the file</p>
    </div>
  </div>

  <div class="min-h-screen bg-gradient-to-b from-gray-950 via-black to-gray-950 py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <router-link to="" @click="goBack" class="inline-flex items-center text-blue-400 hover:text-blue-300 mb-8 transition">← Back to Job</router-link>

      <div v-if="loading" class="text-gray-400 text-center py-12">Loading…</div>

      <div v-else class="bg-gray-900/80 backdrop-blur border border-gray-800 rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-10">
          <h1 class="text-3xl font-bold text-white mb-2">Apply for Position</h1>
          <p class="text-white/90">{{ job.title }} at {{ job.company?.name || 'Unknown' }}</p>
        </div>

        <div class="p-8 space-y-8">
          <div v-if="error" class="border border-red-500/50 bg-red-900/30 text-red-200 rounded-lg px-4 py-3">{{ error }}</div>

          <div class="border border-gray-700 rounded-xl p-6 bg-gray-800/50">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="text-xl font-bold text-white mb-2">{{ job.title }}</h2>
                <div class="flex flex-wrap gap-3 text-white/70 text-sm">
                  <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    {{ job.company?.name || 'Unknown' }}
                  </span>
                  <span>•</span>
                  <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ job.location }}
                  </span>
                </div>
              </div>
              <div class="text-right">
                <p class="text-green-400 font-bold text-lg">${{ job.salary }}/year</p>
                <p class="text-white/60 text-sm mt-1">{{ job.type }}</p>
              </div>
            </div>
          </div>

          <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
            <div class="space-y-3">
              <label class="block text-sm font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                </svg>
                Resume
              </label>
              <select v-model="resumeId" @change="onResumeSelect" class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg border border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <option value="">-- Choose an existing resume --</option>
                <option v-for="r in resumes" :key="r.id" :value="r.id">{{ r.filename }}</option>
              </select>
              <div class="relative" @dragenter="onDragEnter" @dragleave="onDragLeave" @dragover.prevent="onDragOver" @drop.prevent="onDrop">
                <input ref="fileInput" type="file" @change="onFileChange" accept=".pdf,.doc,.docx" class="hidden" />
                <label :for="'file-' + Math.random()" class="w-full flex flex-col items-center justify-center px-4 py-6 border-2 border-dashed border-gray-700 rounded-lg cursor-pointer bg-gray-800 text-white transition hover:border-indigo-500" @click="triggerFileInput">
                  <span id="upload-text" class="text-center">Drag & drop your resume here or click to select</span>
                  <span id="file-name" v-if="fileName" class="mt-2 text-sm text-white/60">{{ fileName }}</span>
                </label>
              </div>
            </div>

            <div class="space-y-3">
              <label for="cover_letter" class="block text-sm font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"></path>
                </svg>
                Cover Letter (Optional)
              </label>
              <textarea v-model="cover_letter" rows="5" placeholder="Tell us why you're interested in this position and what makes you a great fit..." class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg border border-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 transition resize-none"></textarea>
              <div class="flex justify-between items-center text-xs text-white/50">
                <span>Share your motivation and relevant experience</span>
                <span>Max 2000 characters</span>
              </div>
            </div>

            <div v-if="job.description" class="border border-gray-700 rounded-lg p-4 bg-gray-800/50">
              <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                </svg>
                Job Description
              </h3>
              <p class="text-white/60 text-sm line-clamp-3" v-html="job.description"></p>
            </div>

            <div class="bg-indigo-900/20 border border-indigo-700/50 rounded-lg p-4">
              <p class="text-indigo-200 text-sm flex gap-3">
                <svg class="w-5 h-5 flex-shrink-0 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>💡 Pro Tip:</strong> A tailored cover letter can significantly improve your chances. Highlight specific skills that match the job requirements.</span>
              </p>
            </div>

            <div v-if="hasApplied" class="mt-4 p-4 rounded-lg bg-red-900/20 border border-red-700/50 text-red-200">
              You have already applied to this vacancy.
            </div>

            <div class="flex gap-4 pt-4">
              <button type="button" @click="goBack" class="flex-1 px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-lg font-semibold text-center transition border border-gray-700">Cancel</button>
              <button type="submit" :disabled="!canSubmit || hasApplied" class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-lg font-semibold transition transform hover:scale-105 shadow-lg hover:shadow-indigo-500/50 disabled:from-gray-700 disabled:to-gray-700 disabled:text-gray-400 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">Submit Application</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, computed } from 'vue'
import api from '@/api/axios'
import { useRoute, useRouter } from 'vue-router'

export default defineComponent({
  name: 'JobApply',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const job = ref<any>({})
    const resumes = ref<any[]>([])
    const resumeId = ref<string>('')
    const cvFile = ref<File | null>(null)
    const fileName = ref('')
    const cover_letter = ref('')
    const loading = ref(true)
    const error = ref('')
    const hasApplied = ref(false)
    const dragActive = ref(false)
    const fileInput = ref<HTMLInputElement>()

    const canSubmit = computed(() => {
      return resumeId.value || cvFile.value
    })

    async function fetchJob() {
      loading.value = true
      try {
        const res = await api.get(`/job-vacancies/${route.params.id}`)
        job.value = res.data.data || res.data
      } catch (e) {
        console.error(e)
        error.value = 'Failed to load job details'
      } finally {
        loading.value = false
      }
    }

    async function fetchResumes() {
      try {
        const res = await api.get('/helpers/resumes')
        resumes.value = res.data.data || res.data || []
      } catch (e) {
        console.error('Failed to fetch resumes', e)
      }
    }

    async function fetchApplications() {
      try {
        const res = await api.get('/applications')
        // normalize potential pagination / envelope shapes
        let apps: any[] = []
        if (Array.isArray(res.data)) apps = res.data
        else if (Array.isArray(res.data.data)) apps = res.data.data
        else if (Array.isArray(res.data.data?.data)) apps = res.data.data.data
        else if (typeof res.data === 'object') apps = Object.values(res.data).flat().filter(Boolean)

        // mark if current user already applied to this vacancy
        hasApplied.value = apps.some((a: any) => String(a.jobVacancyId) === String(route.params.id) || String(a.job_vacancy_id) === String(route.params.id))
      } catch (e) {
        console.error('Failed to fetch applications', e)
      }
    }

    function onResumeSelect() {
      if (resumeId.value) {
        cvFile.value = null
        fileName.value = ''
      }
    }

    function onFileChange(e: Event) {
      const target = e.target as HTMLInputElement
      if (target.files && target.files.length) {
        cvFile.value = target.files[0]
        fileName.value = cvFile.value.name
        resumeId.value = ''
      }
    }

    function triggerFileInput() {
      fileInput.value?.click()
    }

    function onDragEnter() {
      dragActive.value = true
    }

    function onDragLeave() {
      dragActive.value = false
    }

    function onDragOver(e: DragEvent) {
      e.preventDefault()
    }

    function onDrop(e: DragEvent) {
      dragActive.value = false
      e.preventDefault()
      if (e.dataTransfer?.files) {
        const file = e.dataTransfer.files[0]
        if (file && ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'].includes(file.type)) {
          cvFile.value = file
          fileName.value = file.name
          resumeId.value = ''
        }
      }
    }

    async function submit() {
      error.value = ''
      if (hasApplied.value) {
        error.value = 'You have already applied to this vacancy.'
        return
      }
      const form = new FormData()
      
      try {
        const me = await api.get('/me')
        const userData = me.data.data || me.data
        const userId = userData?.id || userData?.user?.id || userData?.user_id
        if (!userId) throw new Error('Could not resolve user ID')
        form.append('user_id', String(userId))
      } catch (e) {
        error.value = 'Failed to resolve current user'
        console.error(e)
        return
      }

      form.append('job_vacancy_id', String(route.params.id))
      if (resumeId.value) form.append('resume_id', String(resumeId.value))
      if (cvFile.value) form.append('cv_file', cvFile.value)

      try {
        const res = await api.post('/applications', form, { headers: { 'Content-Type': 'multipart/form-data' } })
        alert('Application submitted successfully!')
        router.push('/my-applications')
      } catch (err: any) {
        console.error('Apply failed', err)
        if (err?.response && err.response.status === 409) {
          error.value = 'You have already applied to this vacancy.'
          hasApplied.value = true
        } else if (err.validation) {
          error.value = Object.values(err.validation).flat().join('; ')
        } else {
          error.value = err?.message || 'Failed to submit application'
        }
      }
    }

    function goBack() {
      router.push({ name: 'JobShow', params: { id: route.params.id } })
    }

    onMounted(() => {
      fetchJob()
      fetchResumes()
      fetchApplications()
    })

    return { job, resumes, resumeId, cvFile, fileName, cover_letter, onFileChange, onResumeSelect, submit, loading, error, canSubmit, dragActive, onDragEnter, onDragLeave, onDragOver, onDrop, triggerFileInput, fileInput, goBack, hasApplied }
  }
})
</script>
