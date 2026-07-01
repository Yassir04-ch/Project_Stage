<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const justifications = ref([])
const isDark = ref(localStorage.getItem("theme") !== "light");

const absenceId = computed(() => route.params.absenceId)

const stats = computed(() => {
  return {
    total: justifications.value.length,
    pending: justifications.value.filter(j => j.status === 'pending').length,
    approved: justifications.value.filter(j => j.status === 'approved').length,
    rejected: justifications.value.filter(j => j.status === 'rejected').length,
  }
})

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
};

const updateStatus = async (id, status) => {
  try {
    await api.put(`/justifications/${id}/status`, { status }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    })
    await loadJustifications()
  } catch (err) { console.log(err) }
}

const loadJustifications = async () => {
  loading.value = true
  try {
    const res = await api.get(`/justifications/absence/${absenceId.value}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    justifications.value = res.data.justifications || []
  } finally { 
    loading.value = false 
  }
}

onMounted(loadJustifications)
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div :class="isDark ? 'bg-slate-950 text-slate-400' : 'bg-slate-50 text-slate-600'" class="min-h-screen font-sans antialiased transition-colors duration-300">
    
    <nav :class="isDark ? 'bg-slate-950/40 border-slate-900/80' : 'bg-white/60 border-slate-200'" class="backdrop-blur-xl px-8 py-4 flex justify-between items-center border-b sticky top-0 z-50 transition-colors">
      <div class="flex items-center gap-4">
        <button @click="router.back()" :class="isDark ? 'bg-slate-900 hover:bg-slate-800 text-slate-300' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all">
          <i class="fas fa-arrow-left"></i>
        </button>
        <div>
          <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-xl font-black uppercase tracking-tight leading-none">Dossier d'Absence</h1>
          <p class="text-[10px] text-indigo-500 font-bold tracking-widest uppercase mt-1">ID: #{{ absenceId }}</p>
        </div>
      </div>
      <button @click="toggleTheme" :class="isDark ? 'bg-slate-900 text-amber-400 border-slate-800' : 'bg-white text-indigo-600 border-slate-200'" class="w-10 h-10 rounded-xl border flex items-center justify-center transition-all shadow-sm">
        <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'"></i>
      </button>
    </nav>

    <main class="max-w-7xl mx-auto px-6 md:px-12 py-10">
      
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-4 space-y-6">
          <div :class="isDark ? 'bg-slate-900/40 border-slate-900/80' : 'bg-white border-slate-200 shadow-sm'" class="p-6 rounded-3xl border">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center mb-4">
              <i class="fas fa-folder-open text-xl"></i>
            </div>
            <h2 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-lg font-black uppercase tracking-tight mb-1">Résumé du dossier</h2>
            <p class="text-xs text-slate-500 mb-6 font-medium">Consultez et gérez les preuves fournies pour cette absence.</p>
            
            <div class="space-y-3">
              <div :class="isDark ? 'bg-slate-900/50' : 'bg-slate-50'" class="flex justify-between items-center p-3 rounded-xl">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Documents</span>
                <span class="text-sm font-black text-indigo-500">{{ stats.total }}</span>
              </div>
              <div :class="isDark ? 'bg-emerald-500/10 text-emerald-500' : 'bg-emerald-50 text-emerald-600'" class="flex justify-between items-center p-3 rounded-xl">
                <span class="text-xs font-bold uppercase tracking-wider">Approuvés</span>
                <span class="text-sm font-black">{{ stats.approved }}</span>
              </div>
              <div :class="isDark ? 'bg-amber-500/10 text-amber-500' : 'bg-amber-50 text-amber-600'" class="flex justify-between items-center p-3 rounded-xl">
                <span class="text-xs font-bold uppercase tracking-wider">En attente</span>
                <span class="text-sm font-black">{{ stats.pending }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-8">
          
          <div v-if="loading" class="space-y-6">
            <div v-for="i in 3" :key="i" :class="isDark ? 'bg-slate-900/20 border-slate-900' : 'bg-white border-slate-100 shadow-sm'" class="rounded-3xl border p-8 animate-pulse">
              <div class="flex justify-between mb-6">
                <div class="space-y-3 w-1/2">
                  <div class="h-4 bg-slate-300 dark:bg-slate-800 rounded w-1/3"></div>
                  <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
                </div>
                <div class="h-6 w-20 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
              </div>
              <div class="h-16 bg-slate-100 dark:bg-slate-900/50 rounded-2xl w-full"></div>
            </div>
          </div>

          <div v-else-if="justifications.length === 0" :class="isDark ? 'bg-slate-900/20 border-slate-900' : 'bg-white border-slate-200 shadow-sm'" class="rounded-3xl border p-16 text-center flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-900 rounded-full flex items-center justify-center mb-6">
              <i class="fas fa-file-circle-xmark text-3xl text-slate-400"></i>
            </div>
            <h3 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-xl font-black uppercase mb-2">Aucune preuve trouvée</h3>
            <p class="text-sm text-slate-500">Ce dossier d'absence ne contient actuellement aucune justification.</p>
          </div>

          <div v-else class="space-y-6">
            <div v-for="j in justifications" :key="j.id" :class="isDark ? 'bg-slate-900/40 border-slate-900 hover:border-slate-800' : 'bg-white border-slate-200 hover:shadow-md'" class="rounded-3xl border p-6 md:p-8 transition-all duration-300 relative overflow-hidden group">
              
              <div class="absolute -right-20 -top-20 w-40 h-40 bg-indigo-500/5 blur-[80px] rounded-full pointer-events-none group-hover:bg-indigo-500/10 transition-colors"></div>

              <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8">
                <div>
                  <span :class="isDark ? 'bg-indigo-500/20 text-indigo-400' : 'bg-indigo-50 text-indigo-600'" class="text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg inline-block mb-3">
                    <i class="fas fa-tag mr-1.5 opacity-70"></i> {{ j.type }}
                  </span>
                  <h2 :class="isDark ? 'text-slate-100' : 'text-slate-900'" class="text-xl font-black leading-tight">{{ j.reason }}</h2>
                </div>
                
                <div :class="{
                  'bg-amber-500/10 text-amber-500 border-amber-500/20': j.status === 'pending',
                  'bg-emerald-500/10 text-emerald-500 border-emerald-500/20': j.status === 'approved',
                  'bg-rose-500/10 text-rose-500 border-rose-500/20': j.status === 'rejected',
                }" class="px-4 py-1.5 rounded-full text-[10px] font-black border uppercase tracking-widest flex items-center gap-2 shrink-0">
                  <i :class="{
                    'fa-solid fa-clock-rotate-left': j.status === 'pending',
                    'fa-solid fa-check': j.status === 'approved',
                    'fa-solid fa-xmark': j.status === 'rejected'
                  }"></i>
                  {{ j.status }}
                </div>
              </div>

              <div :class="isDark ? 'bg-slate-950/50 border-slate-800/60' : 'bg-slate-50 border-slate-200/60'" class="rounded-2xl p-4 md:p-5 flex flex-col md:flex-row items-center justify-between border gap-4">
                <div class="flex items-center gap-4 w-full md:w-auto">
                  <div class="w-10 h-10 bg-rose-500/10 text-rose-500 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fas fa-file-pdf text-lg"></i>
                  </div>
                  <div>
                    <span :class="isDark ? 'text-slate-300' : 'text-slate-800'" class="text-sm font-bold block">Document justificatif</span>
                    <span class="text-[10px] font-mono text-slate-500">ID-{{ j.id }}-PROOF.pdf</span>
                  </div>
                </div>
                
                <div class="flex gap-2 w-full md:w-auto">
                  <a :href="`http://localhost:8000/storage/${j.proof_file}`" target="_blank" 
                    :class="isDark ? 'hover:bg-slate-800 text-slate-300' : 'hover:bg-slate-200 text-slate-600'" class="flex-1 md:flex-none px-5 py-2.5 text-[10px] font-black uppercase rounded-xl transition-all text-center">
                    Aperçu
                  </a>
                  <a :href="`http://localhost:8000/storage/${j.proof_file}`" download
                    class="flex-1 md:flex-none px-5 py-2.5 text-[10px] font-black uppercase bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl transition-all shadow-[0_4px_12px_rgba(79,70,229,0.25)] text-center">
                    <i class="fas fa-download mr-1.5"></i> Télécharger
                  </a>
                </div>
              </div>

              <div v-if="j.status === 'pending'" class="mt-6 pt-6 border-t border-dashed" :class="isDark ? 'border-slate-800' : 'border-slate-200'">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center mb-4">Décision requise</p>
                <div class="flex gap-3">
                  <button @click="updateStatus(j.id, 'approved')"
                    class="flex-1 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 hover:text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 border border-emerald-500/20 hover:border-emerald-500 shadow-sm hover:shadow-emerald-500/20">
                    <i class="fas fa-check mr-2"></i> Approuver
                  </button>
                  <button @click="updateStatus(j.id, 'rejected')"
                    class="flex-1 bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 border border-rose-500/20 hover:border-rose-500 shadow-sm hover:shadow-rose-500/20">
                    <i class="fas fa-xmark mr-2"></i> Rejeter
                  </button>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>