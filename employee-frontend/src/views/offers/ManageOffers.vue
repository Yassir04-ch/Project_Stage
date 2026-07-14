<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/api/axios'
import jobOfferService from "@/services/jobOfferService"
import { useRouter } from 'vue-router'

const router = useRouter()

const offers = ref([])
const loading = ref(false)
const error = ref("")
const currentUser = ref(null)

const getData = async () => {
  loading.value = true
  error.value = ""
  try {
    // 1. Kan-jibo les offres
    const resOffers = await jobOfferService.getAllOffers()
    offers.value = resOffers.data.data || []

    // 2. Kan-jibo l-utilisateur connecté 
    const userResponse = await api.get('/users', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    currentUser.value = userResponse.data.admin || null

  } catch (err) {
    console.error(err)
    error.value = "Impossible de charger les données. Réessayez."
  } finally {
    loading.value = false
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return '--'
  try {
    const date = new Date(dateStr)
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })
  } catch (e) {
    return dateStr.substring(0, 10)
  }
}

const statusLabel = (status) => {
  const labels = {
    open: "Ouverte",
    closed: "Fermée",
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  if (!status) return 'bg-slate-100 text-slate-700 border-slate-200'
  const st = status.toLowerCase()
  if (st === 'open' || st === 'ouverte') {
    return 'bg-emerald-50 text-emerald-700 border-emerald-200/60'
  }
  return 'bg-rose-50 text-rose-700 border-rose-200/60'
}

const confirmDelete = async (offer) => {
  if (!window.confirm(`Supprimer l'offre "${offer.title}" ? Cette action est irréversible.`)) {
    return
  }
  try {
    await jobOfferService.deleteOffer(offer.id)
    offers.value = offers.value.filter((o) => o.id !== offer.id)
  } catch (e) {
    window.alert("La suppression a échoué. Réessayez.")
  }
}

const canAccessSkills = computed(() =>
  ["Administrateur", "Ressources Humaines"].includes(currentUser.value?.role?.name)
)

onMounted(() => {
  getData()
})
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased flex overflow-hidden w-full">

    <!-- 🟦 SIDEBAR (ASIDE) -->
    <aside class="bg-slate-950 text-slate-200 w-72 min-h-screen p-6 flex flex-col justify-between shadow-2xl shrink-0 border-r border-slate-800/40 z-20">
      
      <div class="flex flex-col">
        <div class="flex items-center gap-3.5 mb-10 pb-4 border-b border-slate-900">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 text-blue-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-indigo-500/20">
              D
          </div>
          <div>
            <h1 class="text-lg font-black tracking-tight text-white uppercase italic">DATAXPRESS</h1>
            <p class="text-xs text-indigo-400 font-bold tracking-wider uppercase">Workspace Admin</p>
          </div>
        </div>

        <div class="space-y-2">
          <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Workspace</p>
          <nav class="space-y-1.5">
            <button @click="router.push('/statystic')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">🏠</span>
              <span>Dashboard</span>
            </button>

            <button @click="router.push('/projects')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">📁</span>
              <span>Projects</span>
            </button>

            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 group transition-all text-sm text-left">
              <i class="fas fa-briefcase text-base group-hover:scale-110 transition-transform"></i>
              <span>Offres d'emploi</span>
            </button>

            <button v-if="canAccessSkills" @click="router.push('/users')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">👥</span>
              <span>Utilisateurs</span>
            </button>

            <button v-if="canAccessSkills" @click="router.push('/absences')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fa-regular fa-calendar-minus text-base w-5 group-hover:scale-110 transition-transform"></i>
              <span>Absences</span>
            </button>

            <button v-if="canAccessSkills" @click="router.push('/skills')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fa-solid fa-brain text-base w-5 group-hover:scale-110 transition-transform"></i>
              <span>Compétences</span>
            </button>

            <button @click="router.push('/services')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fas fa-building text-base w-5"></i>
              <span>Services</span>
            </button>
          </nav>
        </div>
      </div>

      <!-- Profil section f l-bottom -->
      <div class="mt-auto pt-4 border-t border-slate-900">
        <router-link
          to="/profile"
          class="bg-slate-900/80 border border-slate-800/40 rounded-xl p-3.5 flex items-center gap-3 shadow-inner hover:bg-slate-900 hover:border-slate-700/60 transition-all group cursor-pointer w-full text-left block"
        >
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center font-black text-white text-sm uppercase tracking-wide border border-indigo-400/20 shrink-0">
            <span v-if="currentUser">{{ currentUser.firstname?.[0] }}{{ currentUser.lastname?.[0] }}</span>
            <span v-else class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
          </div>

          <div class="flex-1 min-w-0">
            <div v-if="currentUser">
              <h2 class="text-sm font-bold text-white tracking-tight truncate uppercase group-hover:text-indigo-400 transition-colors">
                {{ currentUser.firstname }} {{ currentUser.lastname }}
              </h2>
              <p class="text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-0.5 truncate">
                {{ currentUser.role?.name ?? 'Admin' }}
              </p>
            </div>
            
            <div v-else class="space-y-1">
              <div class="h-3 w-24 bg-slate-800 rounded animate-pulse"></div>
              <div class="h-2 w-16 bg-slate-800 rounded animate-pulse"></div>
            </div>
          </div>

          <span class="text-slate-600 group-hover:text-slate-400 transition-colors text-xs shrink-0 pl-1">➔</span>
        </router-link>
      </div>
    </aside>

    <!-- ⬜ CONTENU PRINCIPAL (MAIN) -->
    <main class="flex-1 w-0 p-8 max-w-full flex flex-col justify-start overflow-y-auto h-screen animate-fade-in">

      <!-- NAVBAR HEADER -->
      <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex justify-between items-center mb-8 shadow-sm shrink-0">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Gestion des Offres</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Créez et gérez vos offres d'emploi ainsi que les candidatures reçues.</p>
        </div>
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/60 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-500 shrink-0 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
          Total Offres : <span class="text-slate-900 ml-1 font-black">{{ offers.length }}</span>
        </div>
      </div>

      <!-- MESSAGE D'ERREUR GLOBALE -->
      <div v-if="error" class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm font-semibold flex items-center gap-2.5">
        <i class="fas fa-exclamation-circle text-rose-500"></i>
        <span>{{ error }}</span>
      </div>

      <!-- TABLE / CONTAINER DES OFFRES -->
      <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden flex flex-col">
        
        <!-- Header de la table -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white gap-4 flex-wrap">
          <h2 class="font-bold text-slate-900 text-sm tracking-tight flex items-center gap-2">
            <i class="fas fa-list text-indigo-500"></i> Catalogue des Offres d'emploi
          </h2>
          
          <div class="flex items-center gap-2.5">
            <router-link 
              :to="{ name: 'offers.create' }" 
              class="text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl shadow-md shadow-indigo-600/15 transition-all flex items-center gap-2 active:scale-95"
            >
              <i class="fas fa-plus"></i>
              <span>Nouvelle offre</span>
            </router-link>

            <button @click="getData" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 px-3 py-2 rounded-xl border border-slate-200">
              <i class="fas fa-sync-alt" :class="{'animate-spin': loading}"></i> 
              <span>Rafraîchir</span>
            </button>
          </div>
        </div>

        <!-- Tableau des Offres -->
        <div class="overflow-x-auto w-full">
          <table class="w-full table-fixed border-collapse">
            <thead>
              <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-5 py-4 text-left w-[25%]">Titre de l'offre</th>
                <th class="px-5 py-4 text-left w-[15%]">Lieu</th>
                <th class="px-5 py-4 text-left w-[15%]">Type Contrat</th>
                <th class="px-5 py-4 text-left w-[15%]">Échéance</th>
                <th class="px-5 py-4 text-center w-[15%]">Candidatures</th>
                <th class="px-5 py-4 text-center w-[15%]">Actions/Status</th>
              </tr>
            </thead>
            
            <tbody class="divide-y divide-slate-100">
              <!-- Loading Skeleton -->
              <template v-if="loading">
                <tr v-for="n in 3" :key="n" class="animate-pulse">
                  <td class="px-5 py-4" v-for="i in 6" :key="i">
                    <div class="h-4 bg-slate-100 rounded w-full my-1"></div>
                  </td>
                </tr>
              </template>

              <!-- Liste des Offres -->
              <template v-else-if="offers.length">
                <tr v-for="offer in offers" :key="offer.id" class="hover:bg-slate-50/50 transition-colors group">
                  
                  <!-- Titre -->
                  <td class="px-5 py-4 font-bold text-slate-800 text-sm">
                    <div class="flex items-center gap-2.5 min-w-0">
                      <div class="w-2 h-2 rounded-full bg-slate-300 group-hover:bg-indigo-500 transition-all shrink-0 group-hover:scale-125"></div>
                      <span class="truncate block uppercase text-xs font-bold text-slate-900" :title="offer.title">{{ offer.title }}</span>
                    </div>
                  </td>

                  <!-- Lieu -->
                  <td class="px-5 py-4 text-slate-500 text-xs font-medium">
                    <span class="flex items-center gap-1.5">
                      📍 {{ offer.location || '—' }}
                    </span>
                  </td>

                  <!-- Contrat -->
                  <td class="px-5 py-4 text-slate-500 text-xs font-semibold">
                    <span class="bg-slate-100 border border-slate-200/40 text-slate-700 px-2.5 py-1 rounded-lg">
                      {{ offer.contract_type || '—' }}
                    </span>
                  </td>

                  <!-- Échéance (Deadline) -->
                  <td class="px-5 py-4">
                    <span class="text-slate-600 bg-slate-50 border border-slate-200/60 px-2 py-1 rounded font-mono text-xs font-semibold">
                      {{ formatDate(offer.deadline) }}
                    </span>
                  </td>

                  <!-- Candidatures link -->
                  <td class="px-5 py-4 text-center">
                    <router-link 
                      :to="{ name: 'offers.applications', params: { id: offer.id } }"
                      class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors"
                    >
                      <i class="far fa-envelope"></i>
                      <span>Voir candidatures</span>
                    </router-link>
                  </td>

                  <!-- Actions & Status badge -->
                  <td class="px-5 py-4">
                    <div class="flex items-center justify-center gap-2.5">
                      <span class="inline-flex items-center text-[10px] font-black px-2 py-0.5 rounded-md border uppercase tracking-wider shadow-sm" :class="getStatusClass(offer.status)">
                        {{ statusLabel(offer.status) }}
                      </span>
                      
                      <!-- Modifier -->
                      <router-link 
                        :to="{ name: 'offers.edit', params: { id: offer.id } }" 
                        class="w-7 h-7 bg-white hover:bg-slate-100 border border-slate-200 text-slate-500 rounded-lg flex items-center justify-center transition-all shadow-sm shrink-0 active:scale-95"
                        title="Modifier"
                      >
                        <i class="fas fa-edit text-[10px]"></i>
                      </router-link>

                      <!-- Supprimer -->
                      <button 
                        @click="confirmDelete(offer)" 
                        class="w-7 h-7 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 text-rose-500 rounded-lg flex items-center justify-center transition-all shadow-sm shrink-0 active:scale-95"
                        title="Supprimer"
                      >
                        <i class="fas fa-trash-alt text-[10px]"></i>
                      </button>
                    </div>
                  </td>

                </tr>
              </template>

              <!-- Cas : Pas d'offres -->
              <tr v-else>
                <td colspan="6" class="p-12 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/20">
                  <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-3">
                    <i class="fas fa-briefcase text-sm"></i>
                  </div>
                  Aucune offre d'emploi active dans le catalogue
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </main>

  </div>
</template>