<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/api/axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const projects = ref([])
const loading = ref(false)
const currentUser = ref(null)

const getData = async () => {
  loading.value = true
  try {
    const projectResponse = await api.get('/projects', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    projects.value = projectResponse.data.data || []

    const userResponse = await api.get('/users', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    currentUser.value = userResponse.data.admin || null

  } catch (error) {
    console.log(error.response?.data)
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

const getStatusClass = (status) => {
  if (!status) return 'bg-slate-100 text-slate-700 border-slate-200'
  const st = status.toLowerCase()
  if (st.includes('progress') || st.includes('enours') || st.includes('encours')) return 'bg-amber-50 text-amber-700 border-amber-200/60'
  if (st.includes('done') || st.includes('terminé') || st.includes('completed')) return 'bg-emerald-50 text-emerald-700 border-emerald-200/60'
  return 'bg-indigo-50 text-indigo-700 border-indigo-200/60'
}

const canAccessSkills = computed(() =>
  ["Administrateur", "Ressources Humaines"].includes(currentUser.value?.role?.name)
);

onMounted(() => {
  getData()
})
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased flex overflow-hidden w-full">

    <aside class="bg-slate-950 text-slate-200 w-72 min-h-screen p-6 flex flex-col justify-between shadow-2xl shrink-0 border-r border-slate-800/40">
      
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

            <button @click="router.push('/projects')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 group transition-all text-sm text-left">
              <span class="text-base group-hover:scale-110 transition-transform">📁</span>
              <span>Projects</span>
            </button>

            <button v-if="canAccessSkills"  @click="router.push('/users')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">👥</span>
              <span>Utilisateurs</span>
            </button>

            <button v-if="canAccessSkills"  @click="router.push('/absences')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fa-regular fa-calendar-minus text-base w-5 group-hover:scale-110 transition-transform"></i>
              <span>Absences</span>
            </button>

            <button v-if="canAccessSkills" @click="router.push('/skills')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
            <i class="fa-solid fa-brain text-base w-5 group-hover:scale-110 transition-transform"></i><span>Compétences</span>
           </button>

            <button @click="router.push('/services')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fas fa-building text-base w-5"></i><span>Services</span>
            </button>
          </nav>
        </div>
      </div>

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

    <main class="flex-1 w-0 p-8 max-w-full flex flex-col justify-start overflow-y-auto h-screen">

      <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex justify-between items-center mb-8 shadow-sm shrink-0">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Projects Matrix</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Suivi en temps réel de vos projets actifs et livrables.</p>
        </div>
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/60 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-500 shrink-0 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
          Total Projets : <span class="text-slate-900 ml-1 font-black">{{ projects.length }}</span>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden flex flex-col">
        
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white gap-4 flex-wrap">
          <h2 class="font-bold text-slate-900 text-sm tracking-tight flex items-center gap-2">
            <i class="fas fa-list text-indigo-500"></i> Catalogue des Projets
          </h2>
          
          <div class="flex items-center gap-2.5">
            <router-link 
              to="/project" 
              class="text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl shadow-md shadow-indigo-600/15 transition-all flex items-center gap-2 active:scale-95"
            >
              <i class="fas fa-plus"></i>
              <span>Ajouter Projet</span>
            </router-link>

            <button @click="getData" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 px-3 py-2 rounded-xl border border-slate-200">
              <i class="fas fa-sync-alt" :class="{'animate-spin': loading}"></i> 
              <span>Rafraîchir</span>
            </button>
          </div>
        </div>

        <div class="overflow-x-auto w-full">
          <table class="w-full table-fixed border-collapse">
            <thead>
              <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-5 py-4 text-left w-[22%]">Project Name</th>
                <th class="px-5 py-4 text-left w-[28%]">Description</th>
                <th class="px-5 py-4 text-left w-[18%]">Budget</th>
                <th class="px-5 py-4 text-left w-[20%]">Timeline Interval</th>
                <th class="px-5 py-4 text-center w-[12%]">Actions/Status</th>
              </tr>
            </thead>
            
            <tbody class="divide-y divide-slate-100">
              <template v-if="loading">
                <tr v-for="n in 3" :key="n" class="animate-pulse">
                  <td class="px-5 py-4" v-for="i in 5" :key="i">
                    <div class="h-4 bg-slate-100 rounded w-full my-1"></div>
                  </td>
                </tr>
              </template>

              <template v-else-if="projects.length">
                <tr v-for="project in projects" :key="project.id" class="hover:bg-slate-50/50 transition-colors group">
                  
                  <td class="px-5 py-4 font-bold text-slate-800 text-sm">
                    <div class="flex items-center gap-2.5 min-w-0">
                      <div class="w-2 h-2 rounded-full bg-slate-300 group-hover:bg-indigo-500 transition-all shrink-0 group-hover:scale-125"></div>
                      <span class="truncate block uppercase text-xs font-bold text-slate-900" :title="project.name">{{ project.name }}</span>
                    </div>
                  </td>

                  <td class="px-5 py-4 text-slate-500 text-xs font-medium">
                    <p class="truncate block" :title="project.description">
                      {{ project.description || 'Aucune description disponible' }}
                    </p>
                  </td>

                  <td class="px-5 py-4 text-xs">
                    <span class="inline-flex bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 font-mono text-slate-700 font-bold tracking-tight shadow-sm">
                      💵 {{ typeof project.budget === 'number' ? project.budget.toLocaleString('fr-FR') : project.budget }} MAD
                    </span>
                  </td>

                  <td class="px-5 py-4">
                    <div class="flex items-center gap-1.5 text-[11px] font-semibold">
                      <span class="text-slate-700 bg-indigo-50 border border-indigo-100/40 px-2 py-0.5 rounded font-mono">{{ formatDate(project.start_date) }}</span>
                      <i class="fas fa-long-arrow-right text-slate-300 text-[10px]"></i>
                      <span class="text-slate-600 bg-slate-100 border border-slate-200/60 px-2 py-0.5 rounded font-mono">{{ formatDate(project.end_date) }}</span>
                    </div>
                  </td>

                  <td class="px-5 py-4">
                    <div class="flex items-center justify-center gap-2">
                      <span class="inline-flex items-center text-[10px] font-black px-2 py-0.5 rounded-md border uppercase tracking-wider shadow-sm" :class="getStatusClass(project.status)">
                        {{ project.status ?? 'N/A' }}
                      </span>
                      
                      <router-link :to="`/projects/${project.id}`" class="w-7 h-7 bg-white hover:bg-indigo-600 border border-slate-200 hover:border-indigo-600 text-slate-400 hover:text-white rounded-lg flex items-center justify-center transition-all shadow-sm shrink-0 active:scale-95">
                        <i class="fas fa-chevron-right text-[10px]"></i>
                      </router-link>
                    </div>
                  </td>

                </tr>
              </template>

              <tr v-else>
                <td colspan="5" class="p-12 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/20">
                  <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-3">
                    <i class="fas fa-folder-open text-sm"></i>
                  </div>
                  Aucun projet actif dans le catalogue
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </main>

  </div>
</template>