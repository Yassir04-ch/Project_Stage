<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()
const router = useRouter()

const user     = ref(null)
const authUser = ref(null)
const loading  = ref(false)
const error    = ref(null)

const initials = computed(() => {
  if (!user.value) return ''
  return `${user.value.firstname?.[0] ?? ''}${user.value.lastname?.[0] ?? ''}`.toUpperCase()
})

const authInitials = computed(() => {
  if (!authUser.value) return ''
  return `${authUser.value.firstname?.[0] ?? ''}${authUser.value.lastname?.[0] ?? ''}`.toUpperCase()
})

const getUser = async () => {
  loading.value = true
  error.value   = null
  try {
    const id = route.params.id
    const response = await api.get(`/getUser/${id}`)
    user.value = response.data.user
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Erreur lors du chargement'
  } finally {
    loading.value = false
  }
}

const getAuthUser = async () => {
  try {
    const res = await api.get('/profile')
    authUser.value = res.data.data
  } catch {}
}

const formatDate = (dateStr) => {
  if (!dateStr) return '—'
  try {
    return new Date(dateStr).toLocaleDateString('fr-FR', {
      day: '2-digit', month: 'long', year: 'numeric'
    })
  } catch {
    return dateStr.substring(0, 10)
  }
}

const getGenreBadge = (genre) => {
  if (!genre) return 'bg-slate-100 text-slate-600 border-slate-200'
  return genre.toLowerCase() === 'homme'
    ? 'bg-blue-50 text-blue-600 border-blue-200/60 shadow-sm'
    : 'bg-rose-50 text-rose-600 border-rose-200/60 shadow-sm'
}

const getContratBadge = (contrat) => {
  const map = {
    'CDI':       'bg-emerald-50 text-emerald-700 border-emerald-200/60',
    'CDD':       'bg-amber-50 text-amber-700 border-amber-200/60',
    'Stage':     'bg-indigo-50 text-indigo-700 border-indigo-200/60',
    'Freelance': 'bg-purple-50 text-purple-700 border-purple-200/60',
  }
  return map[contrat] ?? 'bg-slate-50 text-slate-700 border-slate-200'
}

onMounted(() => {
  getUser()
  getAuthUser()
})
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-white text-slate-600 font-sans antialiased flex overflow-hidden w-full selection:bg-indigo-600/10 selection:text-indigo-900">

    <aside class="bg-slate-950 text-slate-200 w-72 min-h-screen p-6 flex flex-col justify-between shadow-[4px_0_24px_rgba(0,0,0,0.08)] shrink-0 border-r border-slate-900 z-20">
      <div class="flex flex-col">
        <div class="flex items-center gap-3.5 mb-10 pb-5 border-b border-slate-900">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white text-xl font-black shadow-[0_4px_12px_rgba(99,102,241,0.3)]">
            D
          </div>
          <div>
            <h1 class="text-lg font-black tracking-wider text-white uppercase italic leading-none">DataExpress</h1>
            <p class="text-[10px] text-indigo-400 font-bold tracking-widest uppercase mt-1.5">Workspace Admin</p>
          </div>
        </div>

        <div class="space-y-2">
          <p class="px-3 text-[10px] font-bold text-slate-600 tracking-widest uppercase mb-3">Workspace</p>
          <nav class="space-y-1">
            <router-link to="/dashboard" class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/50 font-medium transition-all text-sm group text-left">
              <i class="fas fa-home text-base w-5 opacity-60 group-hover:opacity-100 transition-opacity"></i>
              <span>Dashboard</span>
            </router-link>

            <router-link to="/users" class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl font-medium transition-all text-sm text-left block" active-class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-[0_4px_15px_rgba(79,70,229,0.2)] rounded-xl">
              <i class="fas fa-users text-base w-5"></i>
              <span class="font-bold">Utilisateurs</span>
            </router-link>

            <router-link to="/projects" class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/50 font-medium transition-all text-sm group text-left">
              <i class="fas fa-folder text-base w-5 opacity-60 group-hover:opacity-100 transition-opacity"></i>
              <span>Projets</span>
            </router-link>

            <router-link to="/absences" class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/50 font-medium transition-all text-sm group text-left">
              <i class="fa-regular fa-calendar-minus text-base w-5 opacity-60 group-hover:opacity-100 transition-opacity"></i>
              <span>Absences</span>
            </router-link>

            <router-link to="/justification_create" class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/50 font-medium transition-all text-sm group text-left">
              <i class="fas fa-file-signature text-base w-5 opacity-60 group-hover:opacity-100 transition-opacity"></i>
              <span>Justifications</span>
            </router-link>
          </nav>
        </div>
      </div>

      <div class="mt-auto pt-4 border-t border-slate-900">
        <router-link to="/profile" class="bg-slate-900/50 border border-slate-900 rounded-xl p-3.5 flex items-center gap-3 hover:bg-slate-900 transition-all group w-full text-left">
          <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-black text-white text-xs uppercase shrink-0 shadow-md">
            <span v-if="authUser">{{ authInitials }}</span>
            <span v-else class="w-3 h-3 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
          </div>
          <div class="flex-1 min-w-0">
            <div v-if="authUser">
              <h2 class="text-xs font-bold text-white tracking-tight truncate uppercase group-hover:text-indigo-400 transition-colors">
                {{ authUser.firstname }} {{ authUser.lastname }}
              </h2>
              <p class="text-[9px] font-black text-slate-500 tracking-widest uppercase mt-0.5 truncate">
                {{ authUser.role?.name ?? 'Admin' }}
              </p>
            </div>
          </div>
          <span class="text-slate-700 group-hover:text-slate-400 text-xs shrink-0 transition-transform group-hover:translate-x-1">➔</span>
        </router-link>
      </div>
    </aside>

    <main class="flex-1 w-0 p-8 flex flex-col overflow-y-auto h-screen relative bg-white">
      
      <div class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-indigo-500/[0.03] blur-[150px] rounded-full pointer-events-none z-0"></div>

      <div class="backdrop-blur-md bg-white/80 rounded-2xl border border-slate-200/80 px-8 py-5 flex justify-between items-center mb-8 shadow-[0_4px_20px_rgba(0,0,0,0.02)] shrink-0 z-10 relative">
        <div>
          <h1 class="text-xl font-black text-slate-900 tracking-wide uppercase">Fiche Collaborateur</h1>
          <p class="text-xs text-slate-400 font-medium mt-1">Données d'identité de l'agent et paramètres contractuels</p>
        </div>
        <button @click="router.back()" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition-all flex items-center gap-2 bg-slate-50 hover:bg-slate-100/80 px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm active:scale-95">
          <i class="fas fa-arrow-left text-indigo-500"></i> Retour
        </button>
      </div>

      <div v-if="loading" class="flex flex-col items-center justify-center py-32 bg-slate-50/50 backdrop-blur-sm rounded-2xl border border-slate-200/60 shadow-sm flex-1 z-10">
        <div class="w-9 h-9 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-[10px] font-bold text-slate-400 mt-4 uppercase tracking-widest">Indexation de la ressource...</p>
      </div>

      <div v-else-if="error" class="bg-rose-50 border border-rose-200 text-rose-800 p-5 rounded-2xl flex items-center gap-4 shadow-sm mb-8 z-10">
        <div class="w-10 h-10 rounded-xl bg-rose-100 border border-rose-200/60 flex items-center justify-center text-rose-600 shrink-0">
          <i class="fas fa-circle-exclamation text-lg"></i>
        </div>
        <div>
          <h5 class="text-xs font-black uppercase tracking-wider text-rose-900">Échec d'Appel API</h5>
          <p class="text-xs font-medium mt-0.5 text-rose-700">{{ error }}</p>
        </div>
      </div>

      <div v-else-if="user" class="space-y-8 flex-1 max-w-6xl w-full z-10 relative">
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-[0_12px_40px_rgba(0,0,0,0.03)] flex flex-col relative">
          
          <div class="h-44 bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 relative overflow-hidden shrink-0">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(99,102,241,0.15),transparent)]"></div>
            <div class="absolute top-6 left-8 bg-white/10 backdrop-blur-md border border-white/10 px-3 py-1.5 rounded-lg text-[9px] font-mono text-white uppercase tracking-widest shadow-inner">
              <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>Dossier Actif
            </div>
            <div class="absolute bottom-5 right-8 text-[10px] font-mono text-slate-500/80 select-none tracking-widest">RECORD_UID: #00{{ user.id }}</div>
          </div>

          <div class="px-8 pb-8 relative flex flex-col md:flex-row md:items-end justify-between gap-6 -mt-20 border-b border-slate-100 bg-white z-10">
            <div class="flex flex-col md:flex-row md:items-end gap-6 min-w-0 flex-1">
              
              <div class="shrink-0 relative mx-auto md:mx-0">
                <div v-if="user.photo" class="w-36 h-36 rounded-2xl bg-white p-1.5 shadow-[0_12px_30px_rgba(0,0,0,0.1)] border border-slate-200/60 group overflow-hidden">
                  <img :src="`http://127.0.0.1:8000/storage/${user.photo}`" class="w-full h-full rounded-xl object-cover transition-transform duration-500 group-hover:scale-105" />
                </div>
                <div v-else class="w-36 h-36 rounded-2xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-purple-700 flex items-center justify-center text-white font-black text-4xl shadow-[0_8px_24px_rgba(99,102,241,0.25)] border-4 border-white uppercase tracking-wider">
                  {{ initials }}
                </div>
              </div>

              <div class="flex-1 min-w-0 pt-2 md:pt-0 text-center md:text-left">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight uppercase">
                  {{ user.firstname }} {{ user.lastname }}
                </h2>

                <p class="text-xs font-semibold text-slate-400 mt-2.5 flex items-center justify-center md:justify-start gap-2">
                  <i class="fas fa-at text-indigo-500 text-sm"></i> 
                  <span class="font-mono text-slate-600 bg-slate-50 border border-slate-200/60 px-3 py-1 rounded-xl shadow-inner">{{ user.email }}</span>
                </p>
                
                <div class="flex flex-wrap gap-2.5 mt-5 justify-center md:justify-start">
                  <span v-if="user.type_contrat" class="text-[10px] font-black px-3 py-1.5 rounded-xl border uppercase tracking-wider shadow-sm" :class="getContratBadge(user.type_contrat)">
                    <i class="fas fa-file-contract mr-1.5 opacity-80"></i>{{ user.type_contrat }}
                  </span>
                  <span v-if="user.genre" class="text-[10px] font-black px-3 py-1.5 rounded-xl border uppercase tracking-wider" :class="getGenreBadge(user.genre)">
                    <i class="fas mr-1.5" :class="user.genre.toLowerCase() === 'homme' ? 'fa-mars' : 'fa-venus'"></i>{{ user.genre }}
                  </span>
                  <span v-if="user.role" class="text-[10px] font-black px-3 py-1.5 rounded-xl border bg-slate-900 text-white border-slate-800 uppercase tracking-wider shadow-sm">
                    <i class="fas fa-user-shield mr-1.5 text-indigo-400 opacity-90"></i>{{ user.role.name }}
                  </span>
                </div>
              </div>
            </div>

            <div class="shrink-0 flex justify-center pb-1">
              <button @click="router.push(`/users/edit/${user.id}`)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-[0_4px_14px_rgba(79,70,229,0.3)] flex items-center gap-2.5 active:scale-95">
                <i class="fas fa-sliders text-[13px]"></i> Modifier le profil
              </button>
            </div>
          </div>

          <div class="p-8 bg-slate-50/40 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 border-b border-slate-100">
            
            <div class="bg-white border border-slate-200/70 p-5 rounded-2xl shadow-sm flex items-start gap-4 hover:border-slate-300 transition-all duration-200 group">
              <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner transition-colors"><i class="fas fa-passport text-base"></i></div>
              <div class="min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Code d'identité (CIN)</p>
                <p class="text-sm font-black text-slate-800 font-mono uppercase tracking-wider">{{ user.cin ?? '—' }}</p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/70 p-5 rounded-2xl shadow-sm flex items-start gap-4 hover:border-slate-300 transition-all duration-200 group">
              <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner transition-colors"><i class="fas fa-mobile-screen text-base"></i></div>
              <div class="min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Ligne Téléphonique</p>
                <p class="text-sm font-black text-slate-700 font-mono tracking-wide">{{ user.telephone ?? '—' }}</p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/70 p-5 rounded-2xl shadow-sm flex items-start gap-4 hover:border-slate-300 transition-all duration-200 group">
              <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner transition-colors"><i class="fas fa-cake-candles text-base"></i></div>
              <div class="min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Date de Naissance</p>
                <p class="text-sm font-bold text-slate-800 tracking-wide">{{ formatDate(user.date_naissance) }}</p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/70 p-5 rounded-2xl shadow-sm flex items-start gap-4 hover:border-slate-300 transition-all duration-200 group">
              <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner transition-colors"><i class="fas fa-timeline text-base"></i></div>
              <div class="min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Date d'intégration</p>
                <p class="text-sm font-bold text-slate-800 tracking-wide">{{ formatDate(user.date_embauche) }}</p>
              </div>
            </div>

            <div class="bg-emerald-50/30 border border-emerald-100 p-5 rounded-2xl shadow-sm flex items-start gap-4 hover:border-emerald-200 transition-all duration-200 group">
              <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100/50 flex items-center justify-center text-emerald-600 shrink-0 shadow-inner"><i class="fas fa-coins text-base"></i></div>
              <div class="min-w-0">
                <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest leading-none mb-2">Rémunération Mensuelle</p>
                <p class="text-base font-black text-emerald-700 font-mono tracking-wide">
                  {{ user.salaire ? `${Number(user.salaire).toLocaleString('fr-FR')} MAD` : '—' }}
                </p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/70 p-5 rounded-2xl shadow-sm flex items-start gap-4 hover:border-slate-300 transition-all duration-200 group">
              <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner transition-colors"><i class="fas fa-map-pin text-base"></i></div>
              <div class="min-w-0 flex-1">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Domiciliation légale</p>
                <p class="text-sm font-bold text-slate-700 truncate" :title="user.adresse">{{ user.adresse ?? '—' }}</p>
              </div>
            </div>

          </div>

          <div v-if="user.skills?.length" class="p-8 bg-white border-t border-slate-100">
            <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-5 flex items-center gap-2">
              <i class="fas fa-circle-nodes text-indigo-500 text-sm"></i> Matrice des compétences cartographiées
            </h3>
            <div class="flex flex-wrap gap-2.5">
              <span v-for="skill in user.skills" :key="skill.id" class="inline-flex items-center text-xs px-3.5 py-2 bg-slate-50 text-slate-700 rounded-xl border border-slate-200 shadow-sm transition-all hover:bg-slate-100/80 hover:border-slate-300 cursor-default">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-2.5 shadow-[0_0_6px_rgba(99,102,241,0.5)]"></span>
                <span class="font-bold tracking-wide text-slate-800">{{ skill.name }}</span>
                <span v-if="skill.pivot?.level" class="text-indigo-600 font-black bg-indigo-50 px-2 py-0.5 rounded-lg text-[9px] uppercase tracking-widest border border-indigo-100/60 ml-3">
                  LVL {{ skill.pivot.level }}
                </span>
              </span>
            </div>
          </div>

        </div>
      </div>

      <div v-else class="text-center py-24 bg-slate-50 border border-slate-200 rounded-2xl shadow-sm flex flex-col items-center justify-center flex-1 z-10">
        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 mb-4 shadow-inner text-xl">
          <i class="fas fa-database text-rose-500/80"></i>
        </div>
        <h3 class="text-base font-black text-slate-800 uppercase tracking-wider">Donnée Introuvable</h3>
        <p class="text-xs text-slate-400 max-w-xs mx-auto mt-2 font-medium leading-relaxed">Le registre demandé n'est pas répertorié ou vos habilitations actuelles rejettent son accès.</p>
      </div>

    </main>
  </div>
</template>