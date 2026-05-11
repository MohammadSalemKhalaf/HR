<template>
  <AppLayout>
    <div class="p-6">
      <!-- Back Button -->
      <router-link
        to="/jobs"
        class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 mb-6 transition"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Job
      </router-link>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <svg class="w-8 h-8 animate-spin text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0c4.418 0 8 3.582 8 8h-8z" />
        </svg>
      </div>

      <div v-else-if="job" class="max-w-3xl">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl p-8 text-white mb-6">
          <h1 class="text-3xl font-bold mb-2">Apply for {{ job.title }}</h1>
          <p class="text-lg opacity-90">at {{ job.company?.name }}</p>
          <div class="flex gap-4 mt-4 flex-wrap">
            <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-sm">{{ job.location }}</span>
            <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-sm">${{ formatSalary(job.salary) }}/year</span>
          </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitApplication" class="space-y-6">
          <!-- Alerts -->
          <div v-if="errorMessage" class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
            <p class="text-sm text-red-300">{{ errorMessage }}</p>
          </div>
          <div v-if="successMessage" class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
            <p class="text-sm text-green-300">{{ successMessage }}</p>
          </div>

          <!-- Resume Section -->
          <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
              <svg class="w-6 h-6 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
              </svg>
              Upload Your Resume
            </h3>

            <!-- Drag Drop Zone -->
            <div
              @drop.prevent="handleDrop"
              @dragover.prevent="isDragging = true"
              @dragleave="isDragging = false"
              :class="[
                'relative border-2 border-dashed rounded-lg p-8 text-center transition-colors',
                isDragging ? 'border-blue-500 bg-blue-500/10' : 'border-slate-600 bg-slate-700/30 hover:border-blue-400'
              ]"
            >
              <input
                ref="fileInput"
                type="file"
                accept=".pdf,.doc,.docx"
                @change="handleFileSelect"
                class="hidden"
              />

              <div class="space-y-3">
                <svg class="w-12 h-12 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16v-8m0 0-3 3m3-3 3 3M4.5 19.5h15A2.25 2.25 0 0021.75 17.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" />
                </svg>
                <div>
                  <p class="text-white font-medium">Drag your resume here or <button type="button" @click="openFilePicker" class="text-blue-400 hover:underline">click to browse</button></p>
                  <p class="text-sm text-slate-400 mt-1">PDF, DOC, or DOCX up to 5MB</p>
                </div>
              </div>

              <!-- File preview -->
              <div v-if="form.resumeFile" class="mt-4 p-3 bg-green-500/10 border border-green-500/30 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div class="text-left flex-1">
                  <p class="text-sm text-green-300 font-medium">{{ form.resumeFile.name }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Cover Letter Section -->
          <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z" />
              </svg>
              Cover Letter (Optional)
            </h3>
            <textarea
              v-model="form.coverLetter"
              rows="6"
              placeholder="Tell us why you're interested in this position and what makes you a great fit..."
              class="w-full px-4 py-3 bg-slate-700/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 resize-none"
            />
            <p class="text-xs text-slate-400 mt-2">Max 2000 characters</p>
          </div>

          <!-- AI Analysis Preview -->
          <div v-if="analysisLoading" class="bg-indigo-500/10 border border-indigo-500/30 rounded-2xl p-8">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-6 h-6 text-indigo-400 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 7H7v6h6V7z" />
              </svg>
              Analyzing Resume
            </h3>
            <p class="text-slate-200">Reading your CV and comparing its skills with the job requirements...</p>
          </div>

          <div v-else-if="aiAnalysis" class="bg-indigo-500/10 border border-indigo-500/30 rounded-2xl p-8">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-6 h-6 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 7H7v6h6V7z" />
              </svg>
              AI Analysis Preview
            </h3>
            <div class="space-y-3">
              <div>
                <p class="text-sm text-indigo-300">Match Score</p>
                <div class="flex items-center gap-3 mt-1">
                  <div class="h-2 flex-1 bg-slate-700 rounded-full overflow-hidden">
                    <div
                      class="h-full bg-gradient-to-r from-green-500 to-emerald-500"
                      :style="{ width: `${(aiAnalysis.score / 10) * 100}%` }"
                    />
                  </div>
                  <span class="text-white font-bold">{{ aiAnalysis.score }}/10</span>
                </div>
              </div>
              <div>
                <p class="text-sm text-indigo-300">Feedback</p>
                <p class="text-white mt-1">{{ aiAnalysis.feedback }}</p>
              </div>
              <div v-if="aiAnalysis.matchedSkills.length || aiAnalysis.missingSkills.length" class="grid gap-4 md:grid-cols-2 pt-2">
                <div v-if="aiAnalysis.matchedSkills.length" class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-4">
                  <p class="text-xs uppercase tracking-[0.18em] text-emerald-300 mb-2">Matched Skills</p>
                  <div class="flex flex-wrap gap-2">
                    <span v-for="skill in aiAnalysis.matchedSkills" :key="`matched-${skill}`" class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-200 text-xs font-medium">
                      {{ skill }}
                    </span>
                  </div>
                </div>
                <div v-if="aiAnalysis.missingSkills.length" class="rounded-xl border border-amber-500/20 bg-amber-500/10 p-4">
                  <p class="text-xs uppercase tracking-[0.18em] text-amber-300 mb-2">Missing Skills</p>
                  <div class="flex flex-wrap gap-2">
                    <span v-for="skill in aiAnalysis.missingSkills" :key="`missing-${skill}`" class="px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-100 text-xs font-medium">
                      {{ skill }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-if="submissionResult" class="rounded-2xl border border-cyan-500/30 bg-cyan-500/10 p-8">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-6 h-6 text-cyan-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
              </svg>
              Saved Application Result
            </h3>
            <div class="space-y-3">
              <div>
                <p class="text-sm text-cyan-200">Stored Score</p>
                <p class="text-3xl font-black text-white">{{ submissionResult.score }}/10</p>
              </div>
              <p class="text-slate-100">{{ submissionResult.feedback }}</p>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex gap-3">
            <router-link
              to="/jobs"
              class="px-6 py-3 bg-slate-700/50 hover:bg-slate-700 border border-slate-600 rounded-lg text-white font-semibold transition"
            >
              Cancel
            </router-link>
            <button
              type="submit"
              :disabled="submitting || analysisLoading || !form.resumeFile || !canSubmitByScore"
              class="flex-1 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold rounded-lg transition duration-200"
            >
              <span v-if="!submitting">Submit Application</span>
              <span v-else class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0c4.418 0 8 3.582 8 8h-8z" />
                </svg>
                Submitting...
              </span>
            </button>
          </div>

          <div v-if="aiAnalysis && !canSubmitByScore" class="rounded-lg border border-rose-500/30 bg-rose-500/10 p-4">
            <p class="text-sm text-rose-200 font-semibold">Application blocked</p>
            <p class="text-sm text-rose-100 mt-1">You need at least 5/10 to apply for this role. Your current score is {{ aiAnalysis.score }}/10.</p>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/api/axios'

type AnalysisResult = {
  score: number
  feedback: string
  matchedSkills: string[]
  missingSkills: string[]
}

const router = useRouter()
const route = useRoute()

const job = ref<any>(null)
const loading = ref(false)
const submitting = ref(false)
const isDragging = ref(false)
const fileInput = ref<HTMLInputElement | null>(null)
const errorMessage = ref('')
const successMessage = ref('')
const analysisLoading = ref(false)
const extractedResumeText = ref('')
const aiAnalysis = ref<AnalysisResult | null>(null)
const submissionResult = ref<AnalysisResult | null>(null)
const minExtractedTextLength = 20

const form = ref({
  resumeFile: null as File | null,
  coverLetter: '',
})

const skillKeywords = [
  'php', 'laravel', 'vue', 'react', 'angular', 'javascript', 'typescript', 'node', 'node.js',
  'html', 'css', 'tailwind', 'bootstrap', 'sql', 'mysql', 'postgres', 'postgresql', 'mongodb',
  'api', 'rest', 'graphql', 'docker', 'kubernetes', 'aws', 'git', 'testing', 'unit testing',
  'leadership', 'communication', 'problem solving', 'project management', 'figma', 'ui ux',
  'devops', 'ci cd', 'agile', 'scrum', 'python', 'java', 'c#', 'dotnet', 'mobile', 'android', 'ios'
]

const formatSalary = (salary: string): string => {
  const num = parseInt(salary.replace(/[^\d]/g, '')) || 0
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(0) + 'K'
  return num.toString()
}

const handleDrop = (e: DragEvent) => {
  isDragging.value = false
  const files = e.dataTransfer?.files
  if (files && files.length > 0) {
    void handleFile(files[0])
  }
}

const handleFileSelect = (e: Event) => {
  const input = e.target as HTMLInputElement
  if (input.files && input.files.length > 0) {
    void handleFile(input.files[0])
  }
}

const openFilePicker = () => {
  fileInput.value?.click()
}

const handleFile = async (file: File) => {
  errorMessage.value = ''
  successMessage.value = ''
  submissionResult.value = null

  const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
  if (!validTypes.includes(file.type)) {
    errorMessage.value = 'Please upload a PDF or Word document'
    return
  }

  if (file.size > 5 * 1024 * 1024) {
    errorMessage.value = 'File size must be less than 5MB'
    return
  }

  form.value.resumeFile = file

  await analyzeResume(file)
}

const analyzeResume = async (file: File) => {
  analysisLoading.value = true

  try {
    extractedResumeText.value = await extractResumeText(file)
    if (extractedResumeText.value.trim().length < minExtractedTextLength) {
      aiAnalysis.value = null
      errorMessage.value = 'Could not read enough text from this CV. Please upload a clearer text-based PDF/DOCX.'
      return
    }

    const response = await api.post('/applications/analyze', {
      job_vacancy_id: route.params.id,
      cv_text: extractedResumeText.value,
    })

    const analysis = response.data?.data || {}
    aiAnalysis.value = {
      score: Number(analysis.score ?? 0),
      feedback: String(analysis.feedback ?? ''),
      matchedSkills: Array.isArray(analysis.matched_skills) ? analysis.matched_skills : [],
      missingSkills: Array.isArray(analysis.missing_skills) ? analysis.missing_skills : [],
    }
  } catch (error) {
    console.error('Error analyzing resume:', error)
    aiAnalysis.value = null
    errorMessage.value = 'Failed to analyze this CV. Please try again with a different file.'
  } finally {
    analysisLoading.value = false
  }
}

const extractResumeText = async (file: File): Promise<string> => {
  if (file.type === 'application/pdf') {
    return await extractPdfText(file)
  }

  if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
    return await extractDocxText(file)
  }

  return await file.text()
}

const extractPdfText = async (file: File): Promise<string> => {
  const pdfjs = await import('pdfjs-dist')
  ;(pdfjs as any).GlobalWorkerOptions.workerSrc = new URL('pdfjs-dist/build/pdf.worker.min.mjs', import.meta.url).toString()

  const loadingTask = (pdfjs as any).getDocument({ data: new Uint8Array(await file.arrayBuffer()) })
  const pdf = await loadingTask.promise
  const parts: string[] = []

  for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
    const page = await pdf.getPage(pageNumber)
    const content = await page.getTextContent()
    const text = content.items.map((item: any) => item.str).join(' ')
    parts.push(text)
  }

  return parts.join('\n')
}

const extractDocxText = async (file: File): Promise<string> => {
  const mammoth = await import('mammoth')
  const result = await mammoth.extractRawText({ arrayBuffer: await file.arrayBuffer() })
  return result.value || ''
}

const normalizeText = (text: string): string => {
  return text
    .toLowerCase()
    .replace(/[^a-z0-9\+\#\.\-\s]/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
}

const extractSkills = (text: string): string[] => {
  const haystack = normalizeText(text)
  return Array.from(new Set(skillKeywords.filter(skill => haystack.includes(skill))))
}

const buildJobText = (): string => {
  if (!job.value) return ''

  return [
    job.value.title,
    job.value.description,
    job.value.location,
    job.value.type,
    job.value.jobcategory?.name,
    job.value.company?.name,
  ].filter(Boolean).join(' ')
}

const canSubmitByScore = computed(() => {
  if (!aiAnalysis.value) return false
  return Number(aiAnalysis.value.score) >= 5
})

const submitApplication = async () => {
  if (!form.value.resumeFile) {
    errorMessage.value = 'Please upload a resume'
    return
  }

  if (extractedResumeText.value.trim().length < minExtractedTextLength) {
    errorMessage.value = 'CV text is too short or unreadable. Please upload a clearer CV file.'
    return
  }

  if (!canSubmitByScore.value) {
    errorMessage.value = `You cannot apply because your score is ${aiAnalysis.value?.score ?? 0}/10. Minimum required score is 5/10.`
    return
  }

  submitting.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const formData = new FormData()
    formData.append('cv_file', form.value.resumeFile)
    if (extractedResumeText.value) {
      formData.append('cv_text', extractedResumeText.value)
    }
    if (form.value.coverLetter) {
      formData.append('cover_letter', form.value.coverLetter)
    }

    const user = JSON.parse(localStorage.getItem('user') || '{}')
    formData.append('user_id', user.id)
    formData.append('job_vacancy_id', route.params.id)

    const response = await api.post('/applications', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    const application = response.data?.data || {}
    submissionResult.value = {
      score: Number(application.aiGeneratedScore ?? aiAnalysis.value?.score ?? 0),
      feedback: String(application.aiGeneratedFeedback ?? aiAnalysis.value?.feedback ?? ''),
      matchedSkills: aiAnalysis.value?.matchedSkills ?? [],
      missingSkills: aiAnalysis.value?.missingSkills ?? [],
    }
    successMessage.value = 'Application submitted successfully! Review the AI match below.'
  } catch (error: any) {
    if (error?.type === 'validation') {
      const validationValues = error.validation ? Object.values(error.validation).flat() : []
      const firstValidationMessage = validationValues.find((value: any) => typeof value === 'string' && value.trim().length > 0)
      errorMessage.value = firstValidationMessage || error.apiMessage || 'Validation failed while submitting application.'
    } else {
      const errorData = error.response?.data
      const validationValues = errorData?.errors
        ? Object.values(errorData.errors).flat()
        : []
      const firstValidationMessage = validationValues.find((value: any) => typeof value === 'string' && value.trim().length > 0)
      const apiMessage = errorData?.message
      errorMessage.value = firstValidationMessage || apiMessage || 'Failed to submit application'
    }
  } finally {
    submitting.value = false
  }
}

const loadJob = async () => {
  loading.value = true
  try {
    const response = await api.get(`/job-vacancies/${route.params.id}`)
    job.value = response.data?.data || null
  } catch (error) {
    console.error('Error loading job:', error)
    job.value = null
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadJob()
})
</script>
