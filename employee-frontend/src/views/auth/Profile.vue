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
        day: '2-digit', month: 'short', year: 'numeric'
        })
    } catch {
        return dateStr.substring(0, 10)
    }
    }

    const getGenreBadge = (genre) => {
    if (!genre) return 'bg-slate-100 text-slate-700 border-slate-200'
    return genre.toLowerCase() === 'homme'
        ? 'bg-blue-50 text-blue-700 border-blue-200/60'
        : 'bg-pink-50 text-pink-700 border-pink-200/60'
    }

    const getContratBadge = (contrat) => {
    const map = {
        'CDI':       'bg-emerald-50 text-emerald-700 border-emerald-200/60',
        'CDD':       'bg-amber-50 text-amber-700 border-amber-200/60',
        'Stage':     'bg-indigo-50 text-indigo-700 border-indigo-200/60',
        'Freelance': 'bg-purple-50 text-purple-700 border-purple-200/60',
        'Interim':   'bg-slate-50 text-slate-600 border-slate-200',
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

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased flex overflow-hidden w-full">

    <!-- ─── 💻 SIDEBAR FRAMEWORK ─── -->
    <aside class="bg-slate-950 text-slate-200 w-72 min-h-screen p-6 flex flex-col justify-between shadow-2xl shrink-0 border-r border-slate-800/40">
      <div class="flex flex-col">
        <div class="flex items-center gap-3.5 mb-10 pb-4 border-b border-slate-900">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white text-xl font-black shadow-md">
            D
          </div>
          <div>
            <h1 class="text-lg font-black tracking-tight text-white uppercase italic">DataExpress</h1>
            <p class="text-xs text-indigo-400 font-bold tracking-wider uppercase">Workspace Admin</p>
          </div>
        </div>

        <div class="space-y-2">
          <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Workspace</p>
          <nav class="space-y-1.5">
            <router-link to="/dashboard" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left block">
              <i class="fas fa-home text-base w-5 opacity-70 group-hover:opacity-100"></i>
              <span>Dashboard</span>
            </router-link>

            <router-link to="/users" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left block" active-class="bg-indigo-600 text-white font-semibold shadow-md">
              <i class="fas fa-users text-base w-5"></i>
              <span>Utilisateurs</span>
            </router-link>

            <router-link to="/projects" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left block">
              <i class="fas fa-folder text-base w-5 opacity-70 group-hover:opacity-100"></i>
              <span>Projets</span>
            </router-link>

            <router-link to="/absences" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left block">
              <i class="fa-regular fa-calendar-minus text-base w-5 opacity-70 group-hover:opacity-100"></i>
              <span>Absences</span>
            </router-link>

            <router-link to="/justification_create" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left block">
              <i class="fas fa-file-signature text-base w-5 opacity-70 group-hover:opacity-100"></i>
              <span>Justifications</span>
            </router-link>
          </nav>
        </div>
      </div>

      <div class="mt-auto pt-4 border-t border-slate-900">
        <router-link to="/profile" class="bg-slate-900/80 border border-slate-800/40 rounded-xl p-3.5 flex items-center gap-3 shadow-inner hover:bg-slate-900 hover:border-slate-700/60 transition-all group cursor-pointer w-full text-left block">
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center font-black text-white text-sm uppercase shrink-0">
            <span v-if="authUser">{{ authInitials }}</span>
            <span v-else class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
          </div>
          <div class="flex-1 min-w-0">
            <div v-if="authUser">
              <h2 class="text-sm font-bold text-white tracking-tight truncate uppercase group-hover:text-indigo-400 transition-colors">
                {{ authUser.firstname }} {{ authUser.lastname }}
              </h2>
              <p class="text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-0.5 truncate">
                {{ authUser.role?.name ?? 'Admin' }}
              </p>
            </div>
          </div>
          <span class="text-slate-600 group-hover:text-slate-400 text-xs shrink-0 pl-1">➔</span>
        </router-link>
      </div>
    </aside>

    <!-- ─── 📊 MAIN WORKSPACE CONTAINER ─── -->
    <main class="flex-1 w-0 p-8 max-w-full flex flex-col justify-start overflow-y-auto h-screen">

      <!-- TOP HEADER BAR UNIT -->
      <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex justify-between items-center mb-8 shadow-sm shrink-0">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Profil Employé</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Détails de l'identité et contrat de l'utilisateur.</p>
        </div>
        <button @click="router.back()" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors flex items-center gap-2 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm active:scale-95">
          <i class="fas fa-arrow-left"></i> Retour
        </button>
      </div>

      <!-- LOADING MATRIX HANDLER -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-slate-200/60 shadow-sm flex-1">
        <div class="w-10 h-10 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-xs font-semibold text-slate-400 mt-4 uppercase tracking-widest">Chargement de la fiche...</p>
      </div>

      <!-- CORE ERROR BLOCK -->
      <div v-else-if="error" class="bg-rose-50 border border-rose-200 text-rose-800 p-5 rounded-2xl flex items-center gap-4 shadow-sm mb-8">
        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 shrink-0 shadow-sm">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
          <h5 class="text-xs font-black uppercase tracking-wider text-rose-900">Erreur Critique</h5>
          <p class="text-sm font-medium mt-0.5 text-rose-700/90">{{ error }}</p>
        </div>
      </div>

      <!-- MAIN PROFILE FRAMEWORK -->
      <div v-else-if="user" class="space-y-8 flex-1">
        <div class="bg-white rounded-2xl border border-slate-200/60 overflow-hidden shadow-sm flex flex-col relative">
          
          <!-- Geometric Graphic Banner -->
          <div class="h-40 bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 relative overflow-hidden shrink-0">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(99,102,241,0.08),transparent)]"></div>
          </div>

          <!-- Identity Module -->
          <div class="px-8 pb-6 relative flex flex-col sm:flex-row sm:items-end gap-6 -mt-14 border-b border-slate-100 bg-white">
            <div class="shrink-0 relative z-10">
              <div v-if="user.photo" class="w-28 h-28 rounded-2xl bg-white p-1.5 shadow-md border border-slate-200/60">
                <img :src="`http://127.0.0.1:8000/storage/${user.photo}`" class="w-full h-full rounded-xl object-cover" />
              </div>
              <div v-else class="w-28 h-28 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-black text-3xl border-4 border-white shadow-md uppercase tracking-wider">
                {{ initials }}
              </div>
            </div>

            <div class="flex-1 min-w-0 pt-2 sm:pt-0">
              <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase truncate">
                {{ user.firstname }} {{ user.lastname }}
              </h2>
              <p class="text-xs font-medium text-slate-400 mt-1 flex items-center gap-2">
                <i class="fas fa-envelope text-indigo-500/80"></i> <span class="font-mono text-slate-600">{{ user.email }}</span>
              </p>
              
              <div class="flex flex-wrap gap-2 mt-4">
                <span v-if="user.type_contrat" class="text-[10px] font-extrabold px-3 py-1 rounded-full border uppercase tracking-wider shadow-sm" :class="getContratBadge(user.type_contrat)">
                  {{ user.type_contrat }}
                </span>
                <span v-if="user.genre" class="text-[10px] font-extrabold px-3 py-1 rounded-full border uppercase tracking-wider shadow-sm" :class="getGenreBadge(user.genre)">
                  <i class="fas" :class="user.genre.toLowerCase() === 'homme' ? 'fa-mars text-blue-500' : 'fa-venus text-pink-500'"></i> {{ user.genre }}
                </span>
                <span v-if="user.role" class="text-[10px] font-extrabold px-3 py-1 rounded-full border bg-indigo-50 text-indigo-700 border-indigo-200/60 uppercase tracking-wider shadow-sm">
                  <i class="fas fa-shield-alt text-indigo-400"></i> {{ user.role.name }}
                </span>
              </div>
            </div>
          </div>

          <!-- Technical Specs Grid -->
          <div class="p-8 bg-slate-50/40 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            
            <!-- Card - CIN -->
            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-id-card text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Numéro CIN</p>
                <p class="text-xs font-bold text-slate-900 font-mono uppercase">{{ user.cin ?? '—' }}</p>
              </div>
            </div>

            <!-- Card - Tel -->
            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-phone text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Téléphone</p>
                <p class="text-xs font-bold text-slate-700 font-mono">{{ user.telephone ?? '—' }}</p>
              </div>
            </div>

            <!-- Card - Born -->
            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-birthday-cake text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Date de Naissance</p>
                <p class="text-xs font-bold text-slate-700">{{ formatDate(user.date_naissance) }}</p>
              </div>
            </div>

            <!-- Card - Hire -->
            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-calendar-check text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Date d'embauche</p>
                <p class="text-xs font-bold text-slate-700">{{ formatDate(user.date_embauche) }}</p>
              </div>
            </div>

            <!-- Card - Salary -->
            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-500 shrink-0"><i class="fas fa-wallet text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-emerald-600/80 uppercase tracking-widest leading-none mb-1">Salaire Mensuel</p>
                <p class="text-sm font-black text-emerald-700 font-mono">
                  {{ user.salaire ? `${Number(user.salaire).toLocaleString('fr-FR')} MAD` : '—' }}
                </p>
              </div>
            </div>

            <!-- Card - Address -->
            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-map-marker-alt text-xs"></i></div>
              <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Adresse Domicile</p>
                <p class="text-xs font-bold text-slate-700 truncate" :title="user.adresse">{{ user.adresse ?? '—' }}</p>
              </div>
            </div>

          </div>

          <!-- Competences Section -->
          <div v-if="user.skills?.length" class="p-8 border-t border-slate-100 bg-white">
            <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
              <i class="fas fa-graduation-cap text-indigo-500"></i> Compétences & expertises techniques
            </h3>
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in user.skills" :key="skill.id" class="inline-flex items-center text-xs font-bold px-3 py-1.5 bg-slate-50 text-slate-700 rounded-xl border border-slate-200/60 shadow-sm transition-colors hover:bg-slate-100">
                <i class="fas fa-check-circle text-[10px] text-indigo-500 mr-2"></i>
                <span>{{ skill.name }}</span>
                <span v-if="skill.pivot?.level" class="text-indigo-700 font-black bg-indigo-50/80 px-2 py-0.5 rounded-lg text-[9px] uppercase tracking-wider border border-indigo-100 ml-2">
                  Lvl: {{ skill.pivot.level }}
                </span>
              </span>
            </div>
          </div>

        </div>
      </div>

      <!-- ABSOLUTE DISCOVERY FALLBACK -->
      <div v-else class="text-center py-16 bg-white border border-slate-200/60 rounded-2xl shadow-sm flex flex-col items-center justify-center flex-1">
        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100 mb-3 text-lg">
          <i class="fas fa-search"></i>
        </div>
        <h3 class="text-sm font-bold text-slate-700">Utilisateur introuvable</h3>
        <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1 font-medium">L'identifiant spécifié n'existe pas ou a été purgé des serveurs.</p>
      </div>

    </main>
  </div>
</template>