import { defineStore } from 'pinia'
import { createUserWithEmailAndPassword, signInWithEmailAndPassword, signOut, onAuthStateChanged, type User } from 'firebase/auth'
import type { Profile, Role } from '~/types'

export const useAuthStore = defineStore('auth', () => {
  const user = shallowRef<User | null>(null)
  const profile = ref<Profile | null>(null)
  const ready = ref(false)
  const busy = ref(false)
  let initialized = false

  async function loadProfile() {
    const currentUid = user.value?.uid
    if (!currentUid) { profile.value = null; return }
    try {
      const fetched = await useApi().request<Profile>('/me')
      if (user.value?.uid === currentUid) profile.value = fetched
    } catch {
      // Un usuario nuevo puede existir en Authentication antes de crear su perfil.
      if (user.value?.uid === currentUid && profile.value?.uid !== currentUid) profile.value = null
    }
  }

  async function initialize(): Promise<void> {
    if (initialized || !import.meta.client) return
    initialized = true
    const auth = useNuxtApp().$firebaseAuth
    if (!auth) { ready.value = true; return }
    onAuthStateChanged(auth, async (current) => {
      if (user.value?.uid !== current?.uid) profile.value = null
      user.value = current
      await loadProfile()
      ready.value = true
    })
  }

  async function register(input: { email: string; password: string; name: string; role: Exclude<Role, 'ADMIN'>; companyName: string; university: string }) {
    const auth = useNuxtApp().$firebaseAuth
    if (!auth) throw new Error('Configura Firebase antes de registrarte.')
    busy.value = true
    try {
      if (auth.currentUser?.email?.toLowerCase() === input.email.toLowerCase()) {
        user.value = auth.currentUser
      } else {
        const credential = await createUserWithEmailAndPassword(auth, input.email, input.password)
        user.value = credential.user
      }
      profile.value = await useApi().request<Profile>('/profiles', { method: 'POST', body: {
        name: input.name, role: input.role, companyName: input.companyName, university: input.university
      } })
    } finally { busy.value = false }
  }

  async function login(email: string, password: string) {
    const auth = useNuxtApp().$firebaseAuth
    if (!auth) throw new Error('Configura Firebase antes de iniciar sesión.')
    busy.value = true
    try {
      const credential = await signInWithEmailAndPassword(auth, email, password)
      user.value = credential.user
      await loadProfile()
    } finally { busy.value = false }
  }

  async function logout() {
    const auth = useNuxtApp().$firebaseAuth
    if (auth) await signOut(auth)
    user.value = null
    profile.value = null
    await navigateTo('/')
  }

  return { user, profile, ready, busy, initialize, loadProfile, register, login, logout }
})
