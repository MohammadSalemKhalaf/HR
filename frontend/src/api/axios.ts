import axios from 'axios'

// Use backend URL directly (not proxied) for SPA to work properly
const BACKEND_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8002'

const api = axios.create({
  baseURL: `${BACKEND_URL}/api`,
  withCredentials: true,
  headers: {
    Accept: 'application/json'
  }
})

console.log('[Axios] Configured with baseURL:', api.defaults.baseURL)

// Request defaults
api.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

/**
 * Set authorization token for subsequent requests
 */
export function setAuthToken(token: string | null) {
  if (token) {
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`
    console.log('[Axios] ✓ Authorization header set with token:', token.substring(0, 20) + '...')
  } else {
    delete api.defaults.headers.common['Authorization']
    console.log('[Axios] ✓ Authorization header cleared')
  }
}

/**
 * Get stored authorization token from localStorage
 */
export function getAuthToken(): string | null {
  const token = localStorage.getItem('auth_token')
  if (token) {
    console.log('[Axios] Token restored from localStorage:', token.substring(0, 20) + '...')
  }
  return token
}

/**
 * Clear stored authorization token
 */
export function clearAuthToken() {
  localStorage.removeItem('auth_token')
  console.log('[Axios] ✓ Token cleared from localStorage')
}

/**
 * On init, restore token from localStorage if available
 */
if (typeof window !== 'undefined') {
  const token = getAuthToken()
  if (token) {
    setAuthToken(token)
  }
}

// Request interceptor: log outgoing requests
api.interceptors.request.use(
  (config) => {
    console.log('[Axios Request]', {
      method: config.method?.toUpperCase(),
      url: config.url,
      baseURL: config.baseURL,
      hasAuth: !!config.headers.Authorization,
      data: config.data
    })
    return config
  },
  (error) => {
    console.error('[Axios Request Error]', error)
    return Promise.reject(error)
  }
)

// Response interceptor: handle auth and validation globally
api.interceptors.response.use(
  (response) => {
    console.log('[Axios Response]', {
      status: response.status,
      statusText: response.statusText,
      url: response.config.url,
      data: response.data
    })
    return response
  },
  (error) => {
    console.error('[Axios Response Error]', {
      status: error.response?.status,
      statusText: error.response?.statusText,
      url: error.config?.url,
      data: error.response?.data,
      message: error.message
    })

    if (!error.response) return Promise.reject(error)

    const { status, data } = error.response

    if (status === 401) {
      console.warn('[Axios] 401 Unauthorized - clearing auth and redirecting to login')
      // Clear token and redirect to login
      clearAuthToken()
      setAuthToken(null)
      window.location.href = '/login'
      return Promise.reject(error)
    }

    if (status === 422 && data && data.errors) {
      console.warn('[Axios] 422 Validation Error', data.errors)
      // Normalize validation errors into a simple object
      const err = new Error('Validation Error') as any
      err.type = 'validation'
      err.validation = data.errors
      return Promise.reject(err)
    }

    return Promise.reject(error)
  }
)

export default api
