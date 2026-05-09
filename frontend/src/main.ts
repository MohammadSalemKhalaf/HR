import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './styles/tailwind.css'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import { useAuthStore } from '@/stores/auth'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(Toast, { position: 'top-right' })

// Fetch current user before mounting so route guards can act
const auth = useAuthStore(pinia)
auth.fetchUser().finally(() => {
	app.mount('#app')
})
