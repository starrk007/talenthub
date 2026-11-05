import type { ApiResponse } from '~/types'
import { getIdToken } from 'firebase/auth'

export function useApi() {
  const apiBase = useRuntimeConfig().public.apiBase
  async function request<T>(path: string, options: { method?: string; body?: object } = {}): Promise<T> {
    const auth = useNuxtApp().$firebaseAuth
    const headers: Record<string, string> = { 'Content-Type': 'application/json' }
    if (auth?.currentUser) {
      headers.Authorization = `Bearer ${await getIdToken(auth.currentUser)}`
    }
    const url = `${apiBase.replace(/\/$/, '')}/api/v1${path}`
    try {
      const response = await $fetch<ApiResponse<T>>(url, {
        method: (options.method ?? 'GET') as 'GET' | 'POST' | 'PATCH' | 'DELETE',
        body: options.body,
        headers
      })
      return response.data
    } catch (error: unknown) {
      const failure = error as { data?: { error?: string }; message?: string }
      throw new Error(failure?.data?.error || failure?.message || 'Error de conexión con la API.')
    }
  }
  return { request }
}
