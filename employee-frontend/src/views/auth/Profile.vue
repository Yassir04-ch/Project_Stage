<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()  // ✅ katakhod id men URL

const user    = ref(null)
const loading = ref(false)
const error   = ref(null)

const initials = computed(() => {
  if (!user.value) return ''
  return `${user.value.firstname?.[0] ?? ''}${user.value.lastname?.[0] ?? ''}`.toUpperCase()
})

const getUser = async () => {
  loading.value = true
  error.value   = null
  try {
    const id = route.params.id 
    const response = await api.get(`/getUser/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    user.value = response.data.user
    console.log(user.value)
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Erreur lors du chargement'
    console.error(err.response?.data)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return '—'
  try {
    return new Date(dateStr).toLocaleDateString('fr-FR', {
      day: '2-digit', month: 'short', year: 'numeric'
    })
  } catch {
    return dateStr.substring(0, 10)
  }
}

const getGenreBadge = (genre) => {
  if (!genre) return 'bg-slate-100 text-slate-700 border-slate-200'
  return genre.toLowerCase() === 'homme'
    ? 'bg-blue-50 text-blue-700 border-blue-100'
    : 'bg-pink-50 text-pink-700 border-pink-100'
}

const getContratBadge = (contrat) => {
  const map = {
    'CDI':       'bg-emerald-50 text-emerald-700 border-emerald-100',
    'CDD':       'bg-amber-50 text-amber-700 border-amber-100',
    'Stage':     'bg-indigo-50 text-indigo-700 border-indigo-100',
    'Freelance': 'bg-purple-50 text-purple-700 border-purple-100',
    'Interim':   'bg-slate-50 text-slate-600 border-slate-200',
  }
  return map[contrat] ?? 'bg-slate-50 text-slate-700 border-slate-200'
}

onMounted(() => {
  getUser()
})
</script>

<template>
  <div class="min-h-screen bg-[#f8fafc] py-12 px-4 sm:px-6 antialiased font-sans">
    <div class="max-w-2xl mx-auto">

      <!-- ⏳ LOADING STATE (Sleek Shell) -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="relative flex items-center justify-center">
          <div class="w-12 h-12 border-3 border-indigo-600/10 border-t-indigo-600 rounded-full animate-spin"></div>
        </div>
        <p class="text-xs font-semibold text-slate-400 mt-4 uppercase tracking-widest">Chargement du profil...</p>
      </div>

      <!-- ❌ ERROR STATE -->
      <div v-else-if="error" class="bg-rose-50 border border-rose-100 text-rose-800 p-4 rounded-xl flex items-center gap-3 shadow-sm shadow-rose-100/50">
        <div class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-600 shrink-0">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
          <h5 class="text-xs font-bold uppercase tracking-wider text-rose-900">Une erreur est survenue</h5>
          <p class="text-sm font-medium opacity-90 mt-0.5">{{ error }}</p>
        </div>
      </div>

      <!-- 👤 MAIN USER CARD -->
      <div v-else-if="user" class="bg-white rounded-2xl border border-slate-200/60 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
        
        <!-- Premium Header Banner Pattern Cover -->
        <div class="h-24 bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 relative"></div>

        <!-- Header Profile Info Block -->
        <div class="px-6 pb-6 relative flex flex-col sm:flex-row sm:items-end gap-4 -mt-10 border-b border-slate-100">
          
          <!-- Photo Container -->
          <div class="relative shrink-0">
            <div v-if="user.photo" class="w-24 h-24 rounded-2xl bg-white p-1 shadow-md shadow-slate-200/50 border border-slate-100">
              <img :src="`/storage/${user.photo}`" class="w-full h-full rounded-xl object-cover" />
            </div>
            <div v-else class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 p-1 shadow-md shadow-indigo-200/50 flex items-center justify-center text-white font-bold text-2xl tracking-wider border-2 border-white">
              {{ initials }}
            </div>
          </div>

          <!-- User Names & Email Title Details -->
          <div class="flex-1 min-w-0 pt-2 sm:pt-0">
            <div class="flex flex-wrap items-center gap-2">
              <h1 class="text-xl font-bold text-slate-950 tracking-tight truncate">
                {{ user.firstname }} {{ user.lastname }}
              </h1>
            </div>
            <p class="text-sm font-medium text-slate-500 flex items-center gap-1.5 mt-0.5">
              <svg class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
              {{ user.email }}
            </p>

            <!-- Status Tags Badges Row -->
            <div class="flex flex-wrap gap-1.5 mt-3">
              <span v-if="user.type_contrat" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border tracking-wide uppercase shadow-sm" :class="getContratBadge(user.type_contrat)">
                {{ user.type_contrat }}
              </span>
              <span v-if="user.genre" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border tracking-wide uppercase shadow-sm" :class="getGenreBadge(user.genre)">
                {{ user.genre }}
              </span>
              <span v-if="user.role" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border bg-blue-50 text-blue-700 border-blue-100 tracking-wide uppercase shadow-sm">
                {{ user.role.name }}
              </span>
            </div>
          </div>
        </div>

        <!-- 📊 DETAILED GRID LAYOUT MATRIX -->
        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-slate-50/30">

          <!-- Row item group 1 -->
          <div class="p-5 flex flex-col justify-center border-b border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 00-2 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 014 0"/></svg>
              CIN / Identifiant
            </span>
            <p class="text-sm font-semibold text-slate-800 tracking-wide">{{ user.cin ?? '—' }}</p>
          </div>

          <div class="p-5 flex flex-col justify-center border-b border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.72l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.72.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              Téléphone
            </span>
            <p class="text-sm font-semibold text-slate-800 tracking-wide">{{ user.telephone ?? '—' }}</p>
          </div>

          <!-- Row item group 2 -->
          <div class="p-5 flex flex-col justify-center border-b border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              Date de naissance
            </span>
            <p class="text-sm font-semibold text-slate-800">{{ formatDate(user.date_naissance) }}</p>
          </div>

          <div class="p-5 flex flex-col justify-center border-b border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              Date d'embauche
            </span>
            <p class="text-sm font-semibold text-slate-800">{{ formatDate(user.date_embauche) }}</p>
          </div>

          <!-- Row item group 3 -->
          <div class="p-5 flex flex-col justify-center border-b sm:border-b-0 border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Salaire Mensuel
            </span>
            <p class="text-sm font-bold text-slate-900 tracking-tight">
              {{ user.salaire ? `${Number(user.salaire).toLocaleString('fr-FR')} MAD` : '—' }}
            </p>
          </div>

          <div class="p-5 flex flex-col justify-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Adresse Résidentielle
            </span>
            <p class="text-sm font-semibold text-slate-800 truncate" :title="user.adresse">{{ user.adresse ?? '—' }}</p>
          </div>

        </div>

        <!-- 🛠️ SKILLS SECTION -->
        <div v-if="user.skills?.length" class="p-5 border-t border-slate-100 bg-white">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block flex items-center gap-1.5">
            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            Compétences Techniques
          </span>
          <div class="flex flex-wrap gap-1.5">
            <span v-for="skill in user.skills" :key="skill.id" class="inline-flex items-center text-xs font-semibold px-3 py-1 bg-slate-50 text-slate-700 rounded-lg border border-slate-100 shadow-sm">
              {{ skill.name }}
              <span v-if="skill.pivot?.level" class="text-indigo-600 font-bold bg-indigo-50 px-1.5 py-0.5 rounded text-[10px] ml-2">
                {{ skill.pivot.level }}
              </span>
            </span>
          </div>
        </div>

      </div>

      <!-- 🚫 NOT FOUND STATE -->
      <div v-else class="text-center py-16 bg-white border border-slate-100 rounded-2xl shadow-sm">
        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mx-auto mb-3">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-sm font-semibold text-slate-500">Utilisateur introuvable</p>
        <p class="text-xs text-slate-400 mt-1">L'identifiant spécifié n'existe pas ou a été archivé.</p>
      </div>

    </div>
  </div>
</template>