<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()

const project = ref(null)
const loading = ref(false)
const statuses = ['planning', 'active', 'completed', 'cancelled']
const updatingStatus = ref(false)
const statusSuccess = ref(false)

const getProject = async () => {
    loading.value = true
    try {
        const response = await api.get(
            `/projects/${route.params.id}`,
            { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
        )
        project.value = response.data.data
    } catch (error) {
        console.log(error.response?.data)
    } finally {
        loading.value = false
    }
}

const updateStatus = async (newStatus) => {
    if (updatingStatus.value || project.value.status === newStatus) return
    updatingStatus.value = true
    statusSuccess.value = false
    try {
        await api.patch(
            `/projects/${route.params.id}/status`,
            { status: newStatus },
            { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
        )
        project.value.status = newStatus
        statusSuccess.value = true
        setTimeout(() => statusSuccess.value = false, 2500)
    } catch (error) {
        console.log(error.response?.data)
    } finally {
        updatingStatus.value = false
    }
}

const formatDate = (dateStr) => {
    if (!dateStr) return '--'
    try {
        return new Date(dateStr).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })
    } catch (e) {
        return dateStr.substring(0, 10)
    }
}

const getStatusClass = (status) => {
    if (!status) return 'bg-slate-100 text-slate-700 border-slate-200'
    const st = status.toLowerCase()
    if (st === 'active')    return 'bg-amber-50 text-amber-700 border-amber-200/80'
    if (st === 'completed') return 'bg-emerald-50 text-emerald-700 border-emerald-200/80'
    if (st === 'cancelled') return 'bg-rose-50 text-rose-700 border-rose-200/80'
    return 'bg-indigo-50 text-indigo-700 border-indigo-200/80'
}

const statusLabel = (s) => {
    const map = { planning: '📋 Planning', active: '⚙️ Active', completed: '✅ Fait', cancelled: '❌ Annulé' }
    return map[s] ?? s
}

onMounted(() => getProject())
</script>

<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="min-h-screen bg-slate-50/50 text-slate-600 font-sans antialiased p-4 sm:p-6 md:p-8 relative overflow-hidden">

        <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-indigo-600/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] bg-blue-600/5 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-4xl mx-auto space-y-5 relative z-10">

            <div class="flex items-center justify-between">
                <router-link to="/projects"
                    class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-indigo-600 bg-white border border-slate-200/60 px-4 py-2.5 rounded-xl shadow-xs transition-all active:scale-95">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    <span>Retour aux projets</span>
                </router-link>
                
                <button @click="getProject" :disabled="loading"
                    class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200/60 text-slate-400 hover:text-indigo-600 rounded-xl shadow-xs transition-all active:scale-95 disabled:opacity-50">
                    <i class="fas fa-sync-alt text-xs" :class="{'animate-spin': loading}"></i>
                </button>
            </div>

            <div v-if="loading" class="bg-white rounded-2xl border border-slate-200/60 p-6 sm:p-8 shadow-xs space-y-6 animate-pulse">
                <div class="h-7 bg-slate-100 rounded-lg w-1/3"></div>
                <div class="space-y-3">
                    <div class="h-4 bg-slate-100 rounded-md w-full"></div>
                    <div class="h-4 bg-slate-50 rounded-md w-5/6"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                    <div class="h-16 bg-slate-50 rounded-xl"></div>
                    <div class="h-16 bg-slate-50 rounded-xl"></div>
                    <div class="h-16 bg-slate-50 rounded-xl"></div>
                </div>
            </div>

            <div v-else-if="project" class="bg-white rounded-2xl border border-slate-200/60 shadow-xs overflow-hidden">

                <div class="p-6 sm:p-7 border-b border-slate-100 bg-slate-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100/60 flex items-center justify-center text-base shrink-0 shadow-xs">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div class="space-y-0.5">
                            <span class="text-[9px] font-extrabold text-indigo-600 uppercase tracking-wider bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100/50">Fiche Projet</span>
                            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">{{ project.name }}</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1.5 rounded-xl border uppercase tracking-wide shadow-xs"
                            :class="getStatusClass(project.status)">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ project.status || 'PLANNING' }}
                        </span>

                        <router-link
                            :to="{ name: 'project.assignments', params: { id: route.params.id } }"
                            class="inline-flex items-center gap-1.5 bg-white border border-slate-200 hover:border-indigo-300 hover:text-indigo-600 text-slate-600 text-xs font-semibold px-3.5 py-1.5 rounded-xl shadow-xs transition-all active:scale-[0.98]"
                        >
                            <i class="fas fa-project-diagram text-[10px] text-slate-400"></i>
                            <span>Planning</span>
                        </router-link> 
                    </div>
                </div>

                <div class="p-6 sm:p-7 grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="md:col-span-2 space-y-3">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-align-left text-slate-300"></i> Description Générale
                        </h3>
                        <div class="bg-slate-50/40 border border-slate-200/50 rounded-xl p-4 text-slate-600 text-xs leading-relaxed whitespace-pre-line min-h-[120px]">
                            {{ project.description || 'Aucune description fournie pour ce projet.' }}
                        </div>
                    </div>

                    <div class="space-y-4">

                        <div class="bg-white border border-slate-200/60 shadow-xs p-4 rounded-xl flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100/60 flex items-center justify-center text-sm shrink-0">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Budget Alloué</p>
                                <p class="text-base font-bold text-slate-800 tracking-tight">
                                    {{ typeof project.budget === 'number' ? project.budget.toLocaleString('fr-FR') : (project.budget ?? '--') }}
                                    <span class="text-xs font-semibold text-slate-400 ml-0.5">MAD</span>
                                </p>
                            </div>
                        </div>

                        <div class="bg-white border border-slate-200/60 shadow-xs p-4 rounded-xl space-y-3">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wide flex items-center gap-2 border-b border-slate-100 pb-2">
                                <i class="fas fa-calendar-alt text-slate-300"></i> Calendrier Exécution
                            </h4>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-1.5 text-slate-400 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                    <span>Début</span>
                                </div>
                                <span class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">{{ formatDate(project.start_date) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-0.5">
                                <div class="flex items-center gap-1.5 text-slate-400 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                    <span>Limite</span>
                                </div>
                                <span class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">{{ formatDate(project.end_date) }}</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="px-6 sm:px-7 pb-6">
                    <div class="bg-slate-50/50 border border-slate-200/60 rounded-xl p-4.5 space-y-3.5">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-sliders-h text-slate-300"></i> Pilotage & Cycle de vie
                        </h3>
                        
                        <div class="flex flex-wrap gap-2">
                            <button v-for="s in statuses" :key="s"
                                @click="updateStatus(s)"
                                :disabled="updatingStatus || project.status === s"
                                class="text-[11px] font-bold px-3.5 py-2 rounded-xl border uppercase tracking-wider transition-all active:scale-[0.97] disabled:cursor-not-allowed flex items-center justify-center"
                                :class="project.status === s
                                    ? getStatusClass(s) + ' shadow-xs font-extrabold ring-1 ring-offset-1 ring-slate-100'
                                    : 'bg-white text-slate-500 border-slate-200 hover:border-slate-300 hover:text-slate-800 disabled:opacity-40'"
                            >
                                <i v-if="updatingStatus && project.status !== s" class="fas fa-circle-notch animate-spin mr-1.5 text-[10px]"></i>
                                {{ statusLabel(s) }}
                            </button>
                        </div>

                        <transition
                            enter-active-class="transition-all duration-300" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition-all duration-200" leave-to-class="opacity-0">
                            <div v-if="statusSuccess"
                                class="flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-3 py-2 rounded-xl animate-fadeIn"
                            >
                                <i class="fas fa-check-circle"></i>
                                <span>Statut du projet synchronisé avec succès.</span>
                            </div>
                        </transition>
                    </div>
                </div>

            </div>

            <div v-else class="bg-white rounded-2xl border border-slate-200/60 p-12 text-center text-slate-400 shadow-xs animate-fadeIn">
                <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center text-lg mx-auto mb-4 border border-rose-100">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Projet Introuvable</h3>
                <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1 leading-relaxed">
                    Impossible de charger les détails requis. Le jeton d'accès a peut-être expiré ou la ressource n'existe plus.
                </p>
                <router-link to="/projects"
                    class="inline-flex items-center gap-2 mt-6 text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 border border-indigo-100 px-4 py-2.5 rounded-xl transition-all active:scale-95">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    <span>Retour à la liste</span>
                </router-link>
            </div>

        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(2px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.2s ease-out forwards; }
</style>