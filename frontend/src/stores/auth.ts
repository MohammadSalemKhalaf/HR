import { defineStore } from 'pinia'
import api, { ensureCsrf } from '@/api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any
  }),

  actions: {
    async fetchUser() {
      try {
        const { data } = await api.get('/user')
        this.user = data
        return data
      } catch (e) {
        this.user = null
        return null
      }
    },

    async login(credentials: { email: string; password: string }) {
      try {
        await ensureCsrf()
        await api.post('/login', credentials)
        await this.fetchUser()
      } catch (e: any) {
        if (e.type === 'validation') {
          throw e
        }

        throw e
      }
    },

    async logout() {
      try {
        await api.post('/logout')
      } catch (e) {
        // ignore
      } finally {
        this.user = null
        window.location.href = '/login'
      }
    }
  }
})