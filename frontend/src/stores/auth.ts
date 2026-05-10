import { defineStore } from 'pinia'
import api, { setAuthToken, clearAuthToken, getAuthToken } from '@/api/axios'

export interface User {
  id: number
  name: string
  email: string
  role?: string
  role_id?: number
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: getAuthToken() as string | null,
    loading: false,
    error: null as string | null
  }),

  getters: {
    isAuthenticated: (state) => !!state.user && !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isCompany: (state) => state.user?.role === 'company' || state.user?.role === 'company_owner',
    isManager: (state) => state.user?.role === 'manager',
    isEmployee: (state) => state.user?.role === 'employee',
    isJobSeeker: (state) => state.user?.role === 'job_seeker'
  },

  actions: {
    /**
     * Restore auth from localStorage on app init
     */
    restoreAuth() {
      const token = getAuthToken()
      console.log('[Auth] Restoring auth from localStorage:', token ? '✓ Token found' : '✗ No token')
      if (token) {
        this.token = token
        setAuthToken(token)
      }
    },

    /**
     * Fetch current user profile
     */
    async fetchUser() {
      if (!this.token) {
        console.log('[Auth] No token available, skipping fetchUser')
        return null
      }
      try {
        console.log('[Auth] Fetching user profile...')
        const { data } = await api.get('/auth/me')
        console.log('[Auth] User profile response:', data)
        const payload = data.data || data
        this.user = {
          ...(payload.user || payload),
          role: payload.role || payload.user?.role,
          role_id: payload.user?.role_id || payload.role_id
        }
        this.error = null
        return this.user
      } catch (e) {
        console.error('[Auth] Error fetching user:', e)
        this.user = null
        this.token = null
        clearAuthToken()
        return null
      }
    },

    /**
     * Login user with email and password
     */
    async login(credentials: { email: string; password: string }) {
      this.loading = true
      this.error = null
      console.log('[Auth] Login attempt with email:', credentials.email)
      
      try {
        console.log('[Auth] Sending login request to /api/auth/login')
        console.log('[Auth] Request payload:', credentials)
        
        const response = await api.post('/auth/login', credentials)
        console.log('[Auth] Full response object:', response)
        console.log('[Auth] Response data:', response.data)

        // Extract data from response
        const responseData = response.data
        console.log('[Auth] Response data structure:', {
          hasData: !!responseData.data,
          hasToken: !!responseData.data?.token,
          tokenValue: responseData.data?.token ? '✓' : '✗'
        })

        // Try multiple token locations
        let token = null
        let userData = null

        // Try data.token first (most likely)
        if (responseData.data?.token) {
          token = responseData.data.token
          console.log('[Auth] ✓ Token found at data.token')
        }
        // Try data.access_token
        else if (responseData.data?.access_token) {
          token = responseData.data.access_token
          console.log('[Auth] ✓ Token found at data.access_token')
        }
        // Try root level token
        else if (responseData.token) {
          token = responseData.token
          console.log('[Auth] ✓ Token found at root level')
        }
        // Try root level access_token
        else if (responseData.access_token) {
          token = responseData.access_token
          console.log('[Auth] ✓ Token found at root level access_token')
        }

        if (!token) {
          console.error('[Auth] ✗ No token found in response:', responseData)
          this.error = 'Token not found in response'
          throw new Error(this.error)
        }

        // Extract user data
        if (responseData.data?.user) {
          userData = {
            ...responseData.data.user,
            role: responseData.data.role || responseData.data.user.role,
            role_id: responseData.data.user.role_id || responseData.data.role_id
          }
          console.log('[Auth] User data from data.user:', userData)
        } else if (responseData.data?.user_id) {
          // If we have user_id but not full user object, create minimal user
          userData = {
            id: responseData.data.user_id,
            email: credentials.email,
            role: responseData.data.role || 'unknown',
            role_id: responseData.data.role_id
          }
          console.log('[Auth] Created user object from user_id:', userData)
        } else {
          userData = { id: null, email: credentials.email, role: responseData.data?.role, role_id: responseData.data?.role_id }
          console.log('[Auth] Created minimal user object:', userData)
        }

        // Store token and user
        this.token = token
        this.user = userData as User
        
        console.log('[Auth] ✓ Token stored:', token.substring(0, 20) + '...')
        console.log('[Auth] ✓ User object:', this.user)
        
        // Persist token
        localStorage.setItem('auth_token', token)
        setAuthToken(token)
        console.log('[Auth] ✓ Token persisted to localStorage and axios')
        
        return { success: true, user: this.user }
      } catch (e: any) {
        console.error('[Auth] Login failed:', e)
        console.error('[Auth] Error type:', e.type)
        console.error('[Auth] Error response:', e.response?.data)
        
        this.user = null
        this.token = null
        clearAuthToken()
        
        if (e.type === 'validation') {
          this.error = 'Validation failed'
          console.error('[Auth] Validation errors:', e.validation)
          throw e
        }
        
        if (e.response?.status === 401) {
          this.error = 'Invalid email or password'
          console.error('[Auth] Invalid credentials')
          throw new Error(this.error)
        }
        
        this.error = e.response?.data?.message || e.message || 'Login failed'
        console.error('[Auth] Final error:', this.error)
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Get redirect path based on user role
     */
    getRedirectPath(): string {
      if (!this.user) return '/login'
      
      const role = this.user.role
      console.log('[Auth] Getting redirect path for role:', role)
      
      switch (role) {
        case 'admin':
          return '/admin'
        case 'company':
        case 'company_owner':
          return '/company'
        case 'manager':
          return '/manager'
        case 'employee':
          return '/employee'
        case 'job_seeker':
          return '/jobs'
        default:
          console.warn('[Auth] Unknown role, defaulting to /')
          return '/'
      }
    },

    /**
     * Logout user
     */
    async logout() {
      this.loading = true
      console.log('[Auth] Logout initiated')
      this.user = null
      this.token = null
      this.error = null
      clearAuthToken()
      this.loading = false
      console.log('[Auth] ✓ Logout complete, redirecting to login')
      window.location.href = '/login'
    },

    /**
     * Clear error message
     */
    clearError() {
      this.error = null
    }
  }
})