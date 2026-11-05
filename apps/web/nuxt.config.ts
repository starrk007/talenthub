import tailwindcss from '@tailwindcss/vite'

export default defineNuxtConfig({
  compatibilityDate: '2026-07-01',
  ssr: false,
  modules: ['@pinia/nuxt'],
  css: ['~/assets/css/main.css'],
  vite: { plugins: [tailwindcss()] },
  app: { head: { title: 'TalentHub GTO | Empleo universitario', meta: [{ name: 'description', content: 'Vacantes, prácticas profesionales y talento universitario en Guanajuato.' }] } },
  runtimeConfig: {
    public: {
      apiBase: '',
      firebaseApiKey: '',
      firebaseAuthDomain: '',
      firebaseProjectId: '',
      firebaseAppId: ''
    }
  }
})