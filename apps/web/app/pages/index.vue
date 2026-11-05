<template>
  <div class="space-y-7">
    <section class="rounded-2xl bg-primary px-6 py-10 text-primary-content sm:px-10">
      <div class="badge badge-outline mb-3 text-primary-content">Talento universitario en Guanajuato</div>
      <h1 class="max-w-2xl text-3xl font-black sm:text-5xl">Tu siguiente oportunidad comienza aquí.</h1>
      <p class="mt-4 max-w-xl opacity-90">Descubre empleos, prácticas profesionales y oportunidades para desarrollar tu carrera.</p>
      <NuxtLink to="/register" class="btn btn-accent mt-6">Crear perfil gratuito</NuxtLink>
    </section>
    <section>
      <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <div><h2 class="text-2xl font-bold">Vacantes disponibles</h2><p class="text-sm opacity-65">Empresas y oportunidades para estudiantes y egresados.</p></div>
        <input v-model="search" class="input input-bordered w-full sm:w-72" placeholder="Buscar título, ciudad o empresa" aria-label="Buscar vacantes" />
      </div>
      <p v-if="loading" class="loading loading-spinner text-primary" />
      <div v-else-if="error" role="alert" class="alert alert-error">{{ error }}</div>
      <div v-else-if="!filtered.length" class="rounded-xl border border-dashed border-base-300 bg-base-100 p-10 text-center">Sin vacantes por ahora. Las empresas podrán crear las primeras oportunidades.</div>
      <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <article v-for="job in filtered" :key="job.id" class="card border border-base-300 bg-base-100 shadow-sm">
          <div class="card-body">
            <div class="badge badge-secondary badge-outline">{{ job.modality }}</div>
            <h3 class="card-title">{{ job.title }}</h3>
            <p class="font-medium">{{ job.companyName }}</p>
            <p class="text-sm opacity-60">{{ job.location }}</p>
            <p class="line-clamp-3 text-sm">{{ job.description }}</p>
            <div class="card-actions mt-3"><NuxtLink :to="`/jobs/${job.id}`" class="btn btn-primary btn-sm">Ver vacante →</NuxtLink></div>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>
<script setup lang="ts">
import type { Job } from '~/types'
const { request } = useApi()
const jobs = ref<Job[]>([])
const search = ref('')
const loading = ref(true)
const error = ref('')
const filtered = computed(() => jobs.value.filter(job => `${job.title} ${job.companyName} ${job.location}`.toLowerCase().includes(search.value.toLowerCase())))
onMounted(async () => {
  try { jobs.value = await request<Job[]>('/jobs') }
  catch (e) { error.value = (e as Error).message }
  finally { loading.value = false }
})
</script>
