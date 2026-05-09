import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  withCredentials: true,
  headers: {
    Accept: 'application/json'
  }
})

// Request defaults
api.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Optional helper to ensure Laravel's CSRF cookie is present (call before POST if needed)
export async function ensureCsrf() {
  try {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
  } catch (e) {
    // ignore — backend may not use sanctum
  }
}

// Response interceptor: handle auth and validation globally
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (!error.response) return Promise.reject(error)

    const { status, data } = error.response

    if (status === 401) {
      // Redirect to login page (frontend route) so session-based auth can be re-established
      window.location.href = '/login'
      return Promise.reject(error)
    }

    if (status === 422 && data && data.errors) {
      // Normalize validation errors into a simple map
      const validation = data.errors
      const err = new Error('Validation Error') as any
      err.type = 'validation'
      err.validation = validation
      return Promise.reject(err)
    }

    return Promise.reject(error)
  }
)

export default api
