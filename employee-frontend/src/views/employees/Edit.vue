<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const saving = ref(false)
const error = ref(null)
const success = ref(false)

// Form Data Matrix
const form = ref({
  firstname: '',
  lastname: '',
  email: '',
  cin: '',
  telephone: '',
  adresse: '',
  genre: '',
  date_naissance: '',
  date_embauche: '',
  type_contrat: '',
  salaire: '',
  photo: null
})

const authUser = ref(null)
const photoPreview = ref(null)

// Sidebar logic consistency
const authInitials = computed(() => {
  if (!authUser.value) return ''
  return `${authUser.value.firstname?.[0] ?? ''}${authUser.value.lastname?.[0] ?? ''}`.toUpperCase()
})

const fetchUserData = async () => {
  loading.value = true
  try {
    const id = route.params.id
    const response = await api.get(`/getUser/${id}`)
    const u = response.data.user
    
    // Mapping data to form
    form.value = { ...u, photo: null } // Photo starts as null unless changed
    if (u.photo) {
      photoPreview.value = `http://127.0.0.1:8000/storage/${u.photo}`
    }
  } catch (err) {
    error.value = "Impossible de charger les données de l'utilisateur."
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

const handleFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.photo = file
    photoPreview.value = URL.createObjectURL(file)
  }
}

const submitUpdate = async () => {
  saving.value = true
  error.value = null
  success.value = false

  try {
    const id = route.params.id
    const formData = new FormData()
    
    // Append all fields
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null) {
        formData.append(key, form.value[key])
      }
    })

    // Laravel requires _method: PUT for FormData updates via POST
    formData.append('_method', 'PUT')

    await api.post(`/updateUser/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    success.value = true
    setTimeout(() => router.push(`/getUser/${id}`), 1500)
  } catch (err) {
    error.value = err.response?.data?.message || "Erreur lors de la mise à jour."
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchUserData()
  getAuthUser()
})
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased flex overflow-hidden w-full">

    <aside class="bg-slate-950 text-slate-200 w-72 min-h-screen p-6 flex flex-col justify-between shadow-2xl shrink-0 border-r border-slate-800/40">
      <div class="flex flex-col">
        <div class="flex items-center gap-3.5 mb-10 pb-4 border-b border-slate-900 shadow-sm">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white text-xl font-black shadow-lg shadow-indigo-500/20">D</div>
          <div>
            <h1 class="text-lg font-black tracking-tight text-white uppercase italic leading-none">DATAXPRESS</h1>
            <p class="text-[10px] text-indigo-400 font-bold tracking-wider uppercase mt-1">Workspace Admin</p>
          </div>
        </div>

        <nav class="space-y-1.5">
          <router-link to="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group">
            <i class="fas fa-home w-5 opacity-70 group-hover:opacity-100"></i><span>Dashboard</span>
          </router-link>
          <router-link to="/users" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 text-sm">
            <i class="fas fa-users w-5"></i><span>Utilisateurs</span>
          </router-link>
          <router-link to="/projects" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group">
            <i class="fas fa-folder w-5 opacity-70 group-hover:opacity-100"></i><span>Projets</span>
          </router-link>
        </nav>
      </div>

      <div class="mt-auto pt-4 border-t border-slate-900">
        <div class="bg-slate-900/80 border border-slate-800/40 rounded-xl p-3 flex items-center gap-3 shadow-inner">
          <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center font-black text-white text-xs uppercase shrink-0">
            {{ authInitials }}
          </div>
          <div class="min-w-0">
            <h2 class="text-xs font-bold text-white truncate uppercase">{{ authUser?.firstname }}</h2>
            <p class="text-[9px] font-bold text-slate-500 tracking-widest uppercase truncate">{{ authUser?.role?.name }}</p>
          </div>
        </div>
      </div>
    </aside>

    <main class="flex-1 w-0 p-8 overflow-y-auto h-screen">
      
      <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex justify-between items-center mb-8 shadow-sm shrink-0">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Modifier Collaborateur</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Mise à jour des informations de l'employé #{{ route.params.id }}</p>
        </div>
        <button @click="router.back()" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-2 bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl shadow-sm active:scale-95 transition-all">
          <i class="fas fa-times"></i> Annuler
        </button>
      </div>

      <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-slate-200/60 shadow-sm">
        <div class="w-10 h-10 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
      </div>

      <div v-else-if="success" class="bg-emerald-50 border border-emerald-100 p-6 rounded-2xl flex flex-col items-center justify-center text-emerald-800 animate-bounce">
        <i class="fas fa-check-circle text-3xl mb-2"></i>
        <p class="font-bold">Modifications enregistrées avec succès !</p>
      </div>

      <form v-else-if="form" @submit.prevent="submitUpdate" class="space-y-8 max-w-4xl">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          
          <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm flex flex-col items-center">
              <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-wider mb-6 self-start">Photo de profil</h3>
              
              <div class="relative group">
                <img :src="photoPreview || 'https://ui-avatars.com/api/?name=User&background=6366f1&color=fff'" class="w-32 h-32 rounded-3xl object-cover border-4 border-slate-50 shadow-md">
                <label class="absolute inset-0 flex items-center justify-center bg-indigo-600/60 text-white rounded-3xl opacity-0 group-hover:opacity-100 cursor-pointer transition-all">
                  <i class="fas fa-camera text-xl"></i>
                  <input type="file" @change="handleFileChange" class="hidden" accept="image/*">
                </label>
              </div>
              <p class="text-[10px] text-slate-400 mt-4 text-center leading-relaxed font-medium">Cliquez pour changer la photo.<br>Format JPG, PNG (Max 2MB).</p>
            </div>

            <div class="bg-indigo-50 border border-indigo-100 p-5 rounded-2xl">
              <h4 class="text-[10px] font-bold text-indigo-700 uppercase tracking-widest mb-2"><i class="fas fa-info-circle mr-1"></i> Conseil Admin</h4>
              <p class="text-[11px] text-indigo-600/80 leading-relaxed font-medium">Assurez-vous que le <strong>Salaire</strong> et le <strong>Type de Contrat</strong> sont conformes aux grilles actuelles de l'entreprise.</p>
            </div>
          </div>

          <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-2xl border border-slate-200/60 shadow-sm space-y-6">
              
              <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-4 flex items-center gap-2">
                <i class="fas fa-user-edit text-indigo-500"></i> Informations Personnelles
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Prénom</label>
                  <input v-model="form.firstname" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" required>
                </div>
                <div class="space-y-1.5">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Nom</label>
                  <input v-model="form.lastname" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" required>
                </div>
                <div class="space-y-1.5 md:col-span-2">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Email Professionnel</label>
                  <input v-model="form.email" type="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" required>
                </div>
                <div class="space-y-1.5">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Numéro CIN</label>
                  <input v-model="form.cin" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono font-bold uppercase focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none tracking-wider">
                </div>
                <div class="space-y-1.5">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Téléphone</label>
                  <input v-model="form.telephone" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                </div>
              </div>

              <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-4 pt-4 flex items-center gap-2">
                <i class="fas fa-briefcase text-indigo-500"></i> Détails d'Emploi
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Type de Contrat</label>
                  <select v-model="form.type_contrat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold outline-none focus:border-indigo-500">
                    <option value="CDI">CDI</option>
                    <option value="CDD">CDD</option>
                    <option value="Stage">Stage</option>
                    <option value="Freelance">Freelance</option>
                    <option value="Interim">Intérim</option>
                  </select>
                </div>
                <div class="space-y-1.5">
                  <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest pl-1">Salaire (MAD)</label>
                  <input v-model="form.salaire" type="number" step="0.01" class="w-full bg-emerald-50/30 border border-emerald-100 rounded-xl px-4 py-3 text-sm font-black text-emerald-700 outline-none focus:border-emerald-500">
                </div>
                <div class="space-y-1.5">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Date d'embauche</label>
                  <input v-model="form.date_embauche" type="date" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold outline-none">
                </div>
                <div class="space-y-1.5">
                  <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Genre</label>
                  <div class="flex gap-4 pt-2">
                    <label class="flex items-center gap-2 text-sm font-bold cursor-pointer">
                      <input type="radio" v-model="form.genre" value="homme" class="accent-indigo-600 w-4 h-4"> Homme
                    </label>
                    <label class="flex items-center gap-2 text-sm font-bold cursor-pointer">
                      <input type="radio" v-model="form.genre" value="femme" class="accent-pink-600 w-4 h-4"> Femme
                    </label>
                  </div>
                </div>
              </div>

              <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                <p v-if="error" class="text-[10px] font-bold text-rose-500 uppercase mr-auto tracking-tight"><i class="fas fa-exclamation-circle"></i> {{ error }}</p>
                
                <button type="button" @click="router.back()" class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">
                  Retour
                </button>
                <button type="submit" :disabled="saving" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-600/20 flex items-center gap-2 active:scale-95 transition-all">
                  <i v-if="saving" class="fas fa-spinner animate-spin"></i>
                  <span>{{ saving ? 'Enregistrement...' : 'Sauvegarder les changements' }}</span>
                </button>
              </div>

            </div>
          </div>

        </div>
      </form>
    </main>
  </div>
</template>