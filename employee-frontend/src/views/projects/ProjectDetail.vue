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
        return new Date(dateStr).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' })
    } catch (e) {
        return dateStr.substring(0, 10)
    }
}

const getStatusClass = (status) => {
    if (!status) return 'bg-slate-100 text-slate-700 border-slate-200'
    const st = status.toLowerCase()
    if (st === 'active')    return 'bg-amber-50 text-amber-700 border-amber-200'
    if (st === 'completed') return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    if (st === 'cancelled') return 'bg-red-50 text-red-600 border-red-200'
    return 'bg-indigo-50 text-indigo-700 border-indigo-200'
}

const statusLabel = (s) => {
    const map = { planning: '📋 Planning', active: '⚙️ Active', completed: '✅ Completed', cancelled: '❌ Cancelled' }
    return map[s] ?? s
}

onMounted(() => getProject())
</script>

<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="min-h-screen bg-[#f8fafc] text-slate-600 font-sans antialiased p-4 sm:p-6 md:p-10 relative overflow-hidden">

        <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-indigo-600/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] bg-blue-600/5 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-4xl mx-auto space-y-6 relative z-10">

            <!-- Top bar -->
            <div class="flex items-center justify-between">
                <router-link to="/projects"
                    class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-indigo-600 bg-white border border-slate-200/60 px-4 py-2.5 rounded-xl shadow-sm transition-all active:scale-95">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    <span>Retour aux projets</span>
                </router-link>
                <button @click="getProject" :disabled="loading"
                    class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200/60 text-slate-400 hover:text-indigo-600 rounded-xl shadow-sm transition-colors">
                    <i class="fas fa-sync-alt" :class="{'animate-spin': loading}"></i>
                </button>
            </div>

            <!-- Skeleton -->
            <div v-if="loading" class="bg-white rounded-3xl border border-slate-100 p-8 shadow-sm space-y-6 animate-pulse">
                <div class="h-8 bg-slate-100 rounded-xl w-1/3"></div>
                <div class="space-y-3">
                    <div class="h-4 bg-slate-100 rounded-lg w-full"></div>
                    <div class="h-4 bg-slate-50 rounded-lg w-5/6"></div>
                </div>
                <div class="grid grid-cols-3 gap-4 pt-4">
                    <div class="h-16 bg-slate-50 rounded-xl"></div>
                    <div class="h-16 bg-slate-50 rounded-xl"></div>
                    <div class="h-16 bg-slate-50 rounded-xl"></div>
                </div>
            </div>

            <!-- Main card -->
            <div v-else-if="project" class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

                <!-- Header -->
                <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600/10 text-indigo-600 flex items-center justify-center text-lg shrink-0 border border-indigo-100">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded-md">Fiche Projet</span>
                            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight mt-1">{{ project.name }}</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl border uppercase tracking-wider shadow-sm"
                            :class="getStatusClass(project.status)">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ project.status || 'PLANNING' }}
                        </span>
                    <router-link
                    :to="{
                    name:'project.assignments',
                    params:{ id: route.params.id }
                    }"
                    class="inline-flex items-center gap-1.5 bg-white border border-slate-200 hover:border-indigo-300 hover:text-indigo-600 text-slate-500 text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all active:scale-95"
                    >
                        <i class="fas fa-project-diagram text-[11px]"></i>
                        Planning
                    </router-link> 
                  </div>
                </div>

                <!-- Content -->
                <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-3 gap-8">

                    <!-- Description -->
                    <div class="md:col-span-2 space-y-4">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-align-left text-slate-300"></i> Description Générale
                        </h3>
                        <div class="bg-slate-50/60 border border-slate-100/80 rounded-2xl p-5 text-slate-600 text-sm leading-relaxed whitespace-pre-line min-h-[100px]">
                            {{ project.description || 'Aucune description fournie pour ce projet.' }}
                        </div>
                    </div>

                    <!-- Right column -->
                    <div class="space-y-5">

                        <!-- Budget -->
                        <div class="bg-white border border-slate-100 shadow-sm p-4 rounded-2xl flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base shrink-0">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Budget Alloué</p>
                                <p class="text-base font-black text-slate-800 font-mono mt-0.5">
                                    {{ typeof project.budget === 'number' ? project.budget.toLocaleString() : (project.budget ?? '--') }}
                                    <span class="text-xs font-bold text-slate-400">DH</span>
                                </p>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="bg-white border border-slate-100 shadow-sm p-5 rounded-2xl space-y-4">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wide flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-slate-300"></i> Calendrier du Projet
                            </h4>
                            <div class="flex items-center justify-between border-b border-slate-50 pb-2.5">
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                    <span>Date Début</span>
                                </div>
                                <span class="text-xs font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded-lg">{{ formatDate(project.start_date) }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-0.5">
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    <span>Date Limite</span>
                                </div>
                                <span class="text-xs font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded-lg">{{ formatDate(project.end_date) }}</span>
                            </div>
                        </div>

                        <!-- Planning quick link -->
                        <router-link :to="`/projects/${route.params.id}/planning`"
                            class="flex items-center gap-3 bg-white border border-slate-100 hover:border-indigo-200 shadow-sm p-4 rounded-2xl transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shrink-0 group-hover:bg-indigo-100 transition-colors">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Équipe & Gantt</p>
                                <p class="text-xs font-bold text-indigo-600 mt-0.5">Voir le planning →</p>
                            </div>
                        </router-link>

                    </div>
                </div>

                <!-- Status update -->
                <div class="px-6 sm:px-8 pb-8">
                    <div class="bg-slate-50/60 border border-slate-100 rounded-2xl p-5 space-y-4">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-sliders-h text-slate-300"></i> Modifier le statut
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="s in statuses" :key="s"
                                @click="updateStatus(s)"
                                :disabled="updatingStatus || project.status === s"
                                class="text-xs font-bold px-4 py-2 rounded-xl border uppercase tracking-wider transition-all active:scale-95 disabled:cursor-not-allowed"
                                :class="project.status === s
                                    ? getStatusClass(s) + ' shadow-sm'
                                    : 'bg-white text-slate-500 border-slate-200 hover:border-indigo-300 hover:text-indigo-600 disabled:opacity-40'"
                            >
                                <i v-if="updatingStatus && project.status !== s" class="fas fa-circle-notch animate-spin mr-1 text-[10px]"></i>
                                {{ statusLabel(s) }}
                            </button>
                        </div>
                        <transition
                            enter-active-class="transition-all duration-300" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition-all duration-200" leave-to-class="opacity-0">
                            <div v-if="statusSuccess"
                                class="flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-2 rounded-xl">
                                <i class="fas fa-check-circle"></i>
                                Statut mis à jour avec succès
                            </div>
                        </transition>
                    </div>
                </div>

            </div>

            <!-- Not found -->
            <div v-else class="bg-white rounded-3xl border border-slate-100 p-12 text-center text-slate-400 shadow-sm">
                <div class="w-14 h-14 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-xl mx-auto mb-4 border border-red-100">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Projet Introuvable</h3>
                <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1">
                    Impossible de charger les données. Le projet est inexistant ou vous n'avez pas l'autorisation d'y accéder.
                </p>
                <router-link to="/projects"
                    class="inline-flex items-center gap-2 mt-6 text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 border border-indigo-100 px-4 py-2.5 rounded-xl transition-all active:scale-95">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    Retour aux projets
                </router-link>
            </div>

        </div>
    </div>
</template>