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

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased py-10 px-4 sm:px-6 lg:px-8 selection:bg-indigo-600/10 selection:text-indigo-900">
    
    <div class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-indigo-500/[0.02] blur-[150px] rounded-full pointer-events-none z-0"></div>

    <div class="max-w-5xl mx-auto w-full space-y-6 relative z-10">
      
      <div class="backdrop-blur-md bg-white/80 rounded-2xl border border-slate-200/80 px-6 py-4 flex justify-between items-center shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
        <div class="flex items-center gap-3">
          <button @click="router.back()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200/70 text-slate-700 flex items-center justify-center transition-all active:scale-95 border border-slate-200/40" title="Retour">
            <i class="fas fa-arrow-left text-sm"></i>
          </button>
          <div>
            <h1 class="text-lg font-black text-slate-900 tracking-wide uppercase leading-none">Fiche Collaborateur</h1>
            <p class="text-[11px] text-slate-400 font-medium mt-1">Données d'identité de l'agent et paramètres contractuels</p>
          </div>
        </div>
        
        <button @click="router.back()" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition-all flex items-center gap-2 bg-white hover:bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm active:scale-95">
          <i class="fas fa-chevron-left text-indigo-500 text-[10px]"></i> Retour à la liste
        </button>
      </div>

      <div v-if="loading" class="flex flex-col items-center justify-center py-32 bg-white rounded-2xl border border-slate-200/60 shadow-sm">
        <div class="w-9 h-9 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-[10px] font-bold text-slate-400 mt-4 uppercase tracking-widest">Indexation de la ressource...</p>
      </div>

      <div v-else-if="error" class="bg-rose-50 border border-rose-200 text-rose-800 p-5 rounded-2xl flex items-center gap-4 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-rose-100 border border-rose-200/60 flex items-center justify-center text-rose-600 shrink-0">
          <i class="fas fa-circle-exclamation text-lg"></i>
        </div>
        <div>
          <h5 class="text-xs font-black uppercase tracking-wider text-rose-900">Échec d'Appel API</h5>
          <p class="text-xs font-medium mt-0.5 text-rose-700">{{ error }}</p>
        </div>
      </div>

      <div v-else-if="user" class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-[0_12px_40px_rgba(0,0,0,0.03)] flex flex-col relative">
        
        <div class="h-40 bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 relative overflow-hidden shrink-0">
          <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(99,102,241,0.15),transparent)]"></div>
          <div class="absolute top-5 left-6 bg-white/10 backdrop-blur-md border border-white/10 px-3 py-1.5 rounded-lg text-[9px] font-mono text-white uppercase tracking-widest shadow-inner">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>Dossier Actif
          </div>
          <div class="absolute bottom-5 right-6 text-[10px] font-mono text-slate-500/80 select-none tracking-widest">RECORD_UID: #00{{ user.id }}</div>
        </div>

        <div class="px-6 pb-6 relative flex flex-col md:flex-row md:items-end justify-between gap-6 -mt-16 border-b border-slate-100 bg-white z-10">
          <div class="flex flex-col md:flex-row md:items-end gap-5 min-w-0 flex-1">
            
            <div class="shrink-0 relative mx-auto md:mx-0">
              <div v-if="user.photo" class="w-32 h-32 rounded-2xl bg-white p-1.5 shadow-[0_12px_30px_rgba(0,0,0,0.1)] border border-slate-200/60 overflow-hidden">
                <img :src="`http://127.0.0.1:8000/storage/${user.photo}`" class="w-full h-full rounded-xl object-cover" />
              </div>
              <div v-else class="w-32 h-32 rounded-2xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-purple-700 flex items-center justify-center text-white font-black text-3xl shadow-[0_8px_24px_rgba(99,102,241,0.25)] border-4 border-white uppercase tracking-wider">
                {{ initials }}
              </div>
            </div>

            <div class="flex-1 min-w-0 pt-2 md:pt-0 text-center md:text-left">
              <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase">
                {{ user.firstname }} {{ user.lastname }}
              </h2>

              <p class="text-xs font-semibold text-slate-400 mt-2 flex items-center justify-center md:justify-start gap-2">
                <i class="fas fa-at text-indigo-500 text-sm"></i> 
                <span class="font-mono text-slate-600 bg-slate-50 border border-slate-200/60 px-3 py-0.5 rounded-xl shadow-inner">{{ user.email }}</span>
              </p>
              
              <div class="flex flex-wrap gap-2 mt-4 justify-center md:justify-start">
                <span v-if="user.type_contrat" class="text-[10px] font-black px-2.5 py-1 rounded-xl border uppercase tracking-wider shadow-sm" :class="getContratBadge(user.type_contrat)">
                  <i class="fas fa-file-contract mr-1.5 opacity-80"></i>{{ user.type_contrat }}
                </span>
                <span v-if="user.genre" class="text-[10px] font-black px-2.5 py-1 rounded-xl border uppercase tracking-wider" :class="getGenreBadge(user.genre)">
                  <i class="fas mr-1.5" :class="user.genre.toLowerCase() === 'homme' ? 'fa-mars' : 'fa-venus'"></i>{{ user.genre }}
                </span>
                <span v-if="user.role" class="text-[10px] font-black px-2.5 py-1 rounded-xl border bg-slate-900 text-white border-slate-800 uppercase tracking-wider shadow-sm">
                  <i class="fas fa-user-shield mr-1.5 text-indigo-400 opacity-90"></i>{{ user.role.name }}
                </span>
              </div>
            </div>
          </div>

          <div class="shrink-0 flex flex-wrap justify-center items-center gap-3 pb-1">
            <router-link
              v-if="user"
              :to="{ name: 'absences_create', query: { employee_id: user.id } }"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm flex items-center gap-2 active:scale-95"
            >
              <i class="fas fa-calendar-times text-xs"></i> Absence
            </router-link>

            <button @click="router.push(`/users/edit/${user.id}`)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm flex items-center gap-2 active:scale-95">
              <i class="fas fa-sliders text-xs"></i> Modifier
            </button>
          </div>
        </div>

        <div class="p-6 bg-slate-50/40 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 border-b border-slate-100">
          
          <div class="bg-white border border-slate-200/70 p-4 rounded-xl shadow-sm flex items-start gap-3 hover:border-slate-300 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner"><i class="fas fa-passport text-sm"></i></div>
            <div class="min-w-0">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Code d'identité (CIN)</p>
              <p class="text-xs font-black text-slate-800 font-mono uppercase tracking-wider">{{ user.cin ?? '—' }}</p>
            </div>
          </div>

          <div class="bg-white border border-slate-200/70 p-4 rounded-xl shadow-sm flex items-start gap-3 hover:border-slate-300 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner"><i class="fas fa-mobile-screen text-sm"></i></div>
            <div class="min-w-0">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Ligne Téléphonique</p>
              <p class="text-xs font-black text-slate-700 font-mono tracking-wide">{{ user.telephone ?? '—' }}</p>
            </div>
          </div>

          <div class="bg-white border border-slate-200/70 p-4 rounded-xl shadow-sm flex items-start gap-3 hover:border-slate-300 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner"><i class="fas fa-cake-candles text-sm"></i></div>
            <div class="min-w-0">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Date de Naissance</p>
              <p class="text-xs font-bold text-slate-800 tracking-wide">{{ formatDate(user.date_naissance) }}</p>
            </div>
          </div>

          <div class="bg-white border border-slate-200/70 p-4 rounded-xl shadow-sm flex items-start gap-3 hover:border-slate-300 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner"><i class="fas fa-timeline text-sm"></i></div>
            <div class="min-w-0">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Date d'intégration</p>
              <p class="text-xs font-bold text-slate-800 tracking-wide">{{ formatDate(user.date_embauche) }}</p>
            </div>
          </div>

          <div class="bg-emerald-50/30 border border-emerald-100 p-4 rounded-xl shadow-sm flex items-start gap-3 hover:border-emerald-200 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-100/50 flex items-center justify-center text-emerald-600 shrink-0 shadow-inner"><i class="fas fa-coins text-sm"></i></div>
            <div class="min-w-0">
              <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest leading-none mb-1.5">Rémunération Mensuelle</p>
              <p class="text-sm font-black text-emerald-700 font-mono tracking-wide">
                {{ user.salaire ? `${Number(user.salaire).toLocaleString('fr-FR')} MAD` : '—' }}
              </p>
            </div>
          </div>

          <div class="bg-white border border-slate-200/70 p-4 rounded-xl shadow-sm flex items-start gap-3 hover:border-slate-300 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0 shadow-inner"><i class="fas fa-map-pin text-sm"></i></div>
            <div class="min-w-0 flex-1">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Domiciliation légale</p>
              <p class="text-xs font-bold text-slate-700 truncate" :title="user.adresse">{{ user.adresse ?? '—' }}</p>
            </div>
          </div>

        </div>

        <div class="p-6 bg-white border-t border-slate-100">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
              <i class="fas fa-layer-group text-indigo-500"></i> Matrice des compétences
            </h3>
            <span v-if="user.skills?.length" class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md">
              {{ user.skills.length }} Compétences
            </span>
          </div>

          <div v-if="user.skills?.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            <div v-for="skill in user.skills" :key="skill.id" class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200/70 rounded-xl transition-all hover:bg-slate-100/50 hover:border-slate-300">
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-2 h-2 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)] shrink-0"></div>
                <span class="font-bold text-slate-800 text-xs truncate tracking-wide">{{ skill.name }}</span>
              </div>
              
              <span v-if="skill.pivot?.level" class="text-indigo-600 font-black bg-indigo-50/80 px-2 py-0.5 rounded-lg text-[9px] border border-indigo-100 tracking-wider shrink-0">
                LVL {{ skill.pivot.level }}
              </span>
            </div>
          </div>

          <div v-else class="text-center py-6 bg-slate-50 border border-dashed border-slate-200 rounded-xl">
            <i class="fas fa-folder-open text-slate-300 text-lg mb-1.5 block"></i>
            <p class="text-[11px] font-medium text-slate-400">Aucune compétence cartographiée pour ce profil.</p>
          </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
          <button type="button" @click="router.back()" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2 transition-all active:scale-95 shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i> Retourner en arrière
          </button>
        </div>

      </div>

      <div v-else class="text-center py-24 bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col items-center justify-center">
        <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 mb-4 shadow-inner text-xl">
          <i class="fas fa-database text-rose-500/80"></i>
        </div>
        <h3 class="text-base font-black text-slate-800 uppercase tracking-wider">Donnée Introuvable</h3>
        <p class="text-xs text-slate-400 max-w-xs mx-auto mt-2 font-medium leading-relaxed">Le registre demandé n'est pas répertorié ou vos habilitations actuelles rejettent son accès.</p>
        <button @click="router.back()" class="mt-6 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-indigo-700 transition-all shadow-md">
          Retourner au tableau
        </button>
      </div>

    </div>
  </div>
</template>