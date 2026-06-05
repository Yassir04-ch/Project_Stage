<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'

const projects = ref([])
const loading = ref(false)

const getProject = async () => {
  loading.value = true
  try {
    const response = await api.get('/projects', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    projects.value = response.data.data
    console.log(projects.value)
  } catch (error) {
    console.log(error.response?.data)
  } finally {
    loading.value = false
  }
}

// 🔥 Helper function bach n-format-iw l-date l-twila lli mkhssra l-wsa3eyya
const formatDate = (dateStr) => {
  if (!dateStr) return '--'
  try {
    const date = new Date(dateStr)
    // format standard: DD/MM/YYYY oula DD MMM YYYY
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })
  } catch (e) {
    return dateStr.substring(0, 10) // fallback ila t-bloka
  }
}

const getStatusClass = (status) => {
  if (!status) return 'bg-slate-100 text-slate-700'
  const st = status.toLowerCase()
  if (st.includes('progress') || st.includes('encours')) return 'bg-amber-50 text-amber-700 border-amber-200/60'
  if (st.includes('done') || st.includes('terminé') || st.includes('completed')) return 'bg-emerald-50 text-emerald-700 border-emerald-200/60'
  return 'bg-indigo-50 text-indigo-700 border-indigo-200/60'
}

onMounted(() => {
  getProject()
})
</script>

<template>
  <!-- CDN icon link integration wrapper just in case -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- w-screen o overflow-hidden hna bach l-page t7bess matfoutch l-limits -->
  <div class="w-screen min-h-screen bg-[#f8fafc] text-slate-600 font-sans antialiased flex overflow-hidden">

    <!-- ─── SIDEBAR ASIDE (Width fixed dyal 72 = 288px) ─── -->
    <aside class="w-72 bg-slate-900 text-slate-400 min-h-screen flex flex-col justify-between p-5 border-r border-slate-800/40 shrink-0">
      <div class="space-y-8 relative z-10">
        <!-- Brand Logo Frame -->
        <div class="flex items-center gap-3.5 px-2 mt-2">
          <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black shadow-md shadow-indigo-600/20">
            <i class="fas fa-chart-network text-sm"></i>
          </div>
          <div>
            <h1 class="font-bold text-lg text-white tracking-tight leading-none">DataExpress</h1>
            <p class="text-[11px] text-slate-500 mt-1 font-medium uppercase tracking-wider">Enterprise Platform</p>
          </div>
        </div>

        <!-- Navigation Links -->
        <div class="space-y-2">
          <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Workspace</p>
          <nav class="space-y-1">
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold hover:text-white hover:bg-slate-800/60 transition-all">
              <i class="fas fa-th-large text-base w-5 text-slate-500"></i>
              Dashboard
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-lg shadow-indigo-600/10 transition-all">
              <i class="fas fa-folder text-base w-5"></i>
              Projects
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold hover:text-white hover:bg-slate-800/60 transition-all">
              <i class="fas fa-users text-base w-5 text-slate-500"></i>
              Clients
            </a>
          </nav>
        </div>
      </div>

      <!-- Bottom Profile/Settings -->
      <div class="border-t border-slate-800/80 pt-4 mb-2 px-2 flex items-center justify-between relative z-10">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-white font-bold text-xs uppercase">UX</div>
          <span class="text-xs font-medium text-slate-300">User Workspace</span>
        </div>
        <button class="text-slate-500 hover:text-slate-300 transition-colors"><i class="fas fa-cog"></i></button>
      </div>
    </aside>

    <!-- ─── MAIN ZONE (w-0 o flex-1 kakhlliha t-fit 100% bla tdfa3 l-sidebar) ─── -->
    <main class="flex-1 w-0 p-6 lg:p-8 max-w-full flex flex-col justify-start space-y-6 overflow-y-auto">

      <!-- TOP HEADER SUMMARY SECTION -->
      <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-slate-100 shadow-sm gap-4">
        <div>
          <h1 class="text-xl font-black text-slate-800 tracking-tight">Projects Matrix</h1>
          <p class="text-xs text-slate-400 mt-0.5">Suivi en temps réel de vos projets actifs</p>
        </div>
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-xl text-xs font-medium text-slate-500 shrink-0">
          <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
          Total Projets : <strong class="text-slate-800 font-bold ml-1">{{ projects.length }}</strong>
        </div>
      </div>

      <!-- CORE CONTENT - TABLE STRUCTURE FIXED -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        
        <!-- Table Utility Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h2 class="font-bold text-slate-700 text-xs uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-list text-slate-400"></i> Projects Catalog
          </h2>
          <button @click="getProject" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition-colors flex items-center gap-1.5">
            <i class="fas fa-sync-alt" :class="{'animate-spin': loading}"></i> Rafraîchir
          </button>
        </div>

        <!-- LOADING STATE -->
        <div v-if="loading" class="divide-y divide-slate-100">
          <div v-for="n in 3" :key="n" class="p-6 animate-pulse flex items-center justify-between gap-6">
            <div class="space-y-2 flex-1">
              <div class="h-4 bg-slate-100 rounded w-1/4"></div>
              <div class="h-3 bg-slate-50 rounded w-1/2"></div>
            </div>
          </div>
        </div>

        <!-- DATA TABLE - layout-fixed integration bach t-force widths dyal columns dynamic -->
        <div v-else-if="projects.length" class="w-full">
          <table class="w-full table-fixed border-collapse">
            <thead>
              <tr class="bg-slate-50/40 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-4 py-3.5 text-left w-[20%]">Project Name</th>
                <th class="px-4 py-3.5 text-left w-[25%]">Description</th>
                <th class="px-4 py-3.5 text-left w-[18%]">Budget</th>
                <th class="px-4 py-3.5 text-left w-[23%]">Timeline Interval</th>
                <th class="px-4 py-3.5 text-center w-[14%]">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="project in projects" :key="project.id" class="hover:bg-slate-50/50 transition-colors group">
                
                <!-- Project Name -->
                <td class="px-4 py-3.5 font-bold text-slate-800 text-sm">
                  <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-slate-300 group-hover:bg-indigo-500 transition-colors shrink-0"></div>
                    <span class="truncate block" :title="project.name">{{ project.name }}</span>
                  </div>
                </td>

                <!-- Description -->
                <td class="px-4 py-3.5 text-slate-400 text-xs">
                  <p class="truncate block" :title="project.description">
                    {{ project.description || 'Aucune description' }}
                  </p>
                </td>

                <!-- Budget -->
                <td class="px-4 py-3.5 font-semibold text-slate-700 text-xs">
                  <span class="inline-flex bg-slate-100/70 border border-slate-200/60 px-2 py-0.5 rounded-lg text-slate-700 font-mono tracking-tight">
                    💵 {{ typeof project.budget === 'number' ? project.budget.toLocaleString() : project.budget }} DH
                  </span>
                </td>

                <!-- Timeline Interval (🔥 Fix dyal dates formatting) -->
                <td class="px-4 py-3.5 text-slate-400 text-xs">
                  <div class="flex items-center gap-1 text-[11px]">
                    <span class="text-slate-700 bg-indigo-50/60 px-1.5 py-0.5 rounded font-mono">{{ formatDate(project.start_date) }}</span>
                    <i class="fas fa-long-arrow-right text-slate-300 text-[10px]"></i>
                    <span class="text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded font-mono">{{ formatDate(project.end_date) }}</span>
                  </div>
                </td>

                <!-- Status / Detail Link Button combined into one clear container -->
                <td class="px-4 py-3.5 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <span class="inline-flex items-center text-[10px] font-extrabold px-2 py-0.5 rounded-md border uppercase tracking-wider shadow-sm" :class="getStatusClass(project.status)">
                      {{ project.status }}
                    </span>
                    
                    <router-link :to="`/projects/${project.id}`" class="p-1 bg-white hover:bg-indigo-600 border border-slate-200 hover:border-indigo-600 text-slate-400 hover:text-white rounded-lg transition-all shadow-sm">
                      <i class="fas fa-chevron-right text-[10px]"></i>
                    </router-link>
                  </div>
                </td>

              </tr>
            </tbody>
          </table>
        </div>

        <!-- EMPTY STATE -->
        <div v-else class="p-12 text-center text-slate-400">
          <h3 class="font-bold text-slate-700 text-sm uppercase">Aucun Projet Actif</h3>
        </div>

      </div>
    </main>

  </div>
</template>