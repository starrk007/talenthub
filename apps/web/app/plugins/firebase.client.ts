import { initializeApp, getApps, getApp } from 'firebase/app'
import { getAuth } from 'firebase/auth'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig().public
  if (!config.firebaseApiKey || !config.firebaseProjectId || !config.firebaseAppId) {
    console.warn('Configura NUXT_PUBLIC_FIREBASE_* antes de iniciar sesión.')
    return { provide: { firebaseAuth: null as ReturnType<typeof getAuth> | null } }
  }
  const app = getApps().length ? getApp() : initializeApp({
    apiKey: config.firebaseApiKey,
    authDomain: config.firebaseAuthDomain,
    projectId: config.firebaseProjectId,
    appId: config.firebaseAppId
  })
  return { provide: { firebaseAuth: getAuth(app) } }
})
