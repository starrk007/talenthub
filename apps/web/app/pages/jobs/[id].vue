<template>
  <div class="mx-auto max-w-3xl">
    <NuxtLink to="/" class="link link-primary text-sm">← Volver a vacantes</NuxtLink>
    <div v-if="loading" class="mt-6 loading loading-spinner" />
    <p v-else-if="error" class="alert alert-error mt-5">{{ error }}</p>
    <article v-else-if="job" class="mt-5 rounded-xl bg-base-100 p-7">
      <div class="badge badge-secondary mb-3">{{ job.modality }}</div>
      <h1 class="text-3xl font-bold">{{ job.title }}</h1>
      <p class="mt-3 font-medium text-primary">{{ job.companyName }}</p>
      <p class="text-sm opacity-65">{{ job.location }}</p>
      <div class="divider" />
      <p class="whitespace-pre-line leading-7">{{ job.description }}</p>
      <p class="mt-5 text-sm opacity-65">Las postulaciones se implementan en la sesión 04.</p>
    </article>
  </div>
</template>
<script setup lang="ts">
import type { Job } from '~/types'
const route = useRoute()
const { request } = useApi()
const job = ref<Job | null>(null)
const error = ref('')
const loading = ref(true)
onMounted(async () => {
  try { job.value = await request<Job>(`/jobs/${route.params.id}`) }
  catch (e) { error.value = (e as Error).message }
  finally { loading.value = false }
})
</script>
