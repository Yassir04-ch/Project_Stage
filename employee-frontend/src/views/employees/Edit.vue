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

const availableSkills = ref([])
const selectedSkills = ref([]) 

const isModalOpen = ref(false)
const newSkillName = ref('')
const creatingSkill = ref(false)
const modalError = ref('')

const authInitials = computed(() => {
  if (!authUser.value) return ''
  return `${authUser.value.firstname?.[0] ?? ''}${authUser.value.lastname?.[0] ?? ''}`.toUpperCase()
})

const isSelected = (skillId) => {
  return selectedSkills.value.some(s => s.skill_id === skillId)
}

const toggleSkill = (skillId) => {
  const index = selectedSkills.value.findIndex(s => s.skill_id === skillId)
  if (index > -1) {
    selectedSkills.value.splice(index, 1)
  } else {
    selectedSkills.value.push({ skill_id: skillId, level: 'beginner' })
  }
}

const getLevel = (skillId) => {
  const skill = selectedSkills.value.find(s => s.skill_id === skillId)
  return skill ? skill.level : 'beginner'
}

const setLevel = (skillId, level) => {
  const skill = selectedSkills.value.find(s => s.skill_id === skillId)
  if (skill) {
    skill.level = level
  }
}

const fetchAvailableSkills = async () => {
  try {
    const response = await api.get('/skills') 
    availableSkills.value = response.data.skills || response.data
  } catch (err) {
    console.error("Erreur lors du chargement des compétences globales.")
  }
}

const handleAddSkillPool = async () => {
  if (!newSkillName.value.trim()) return
  creatingSkill.value = true
  modalError.value = ''
  try {
    const response = await api.post('/skills', { name: newSkillName.value })
    const createdSkill = response.data.skill || response.data
    
    availableSkills.value.push(createdSkill)
    selectedSkills.value.push({ skill_id: createdSkill.id, level: 'beginner' })
    
    newSkillName.value = ''
    isModalOpen.value = false
  } catch (err) {
    modalError.value = err.response?.data?.message || "Erreur de création."
  } finally {
    creatingSkill.value = false
  }
}

const fetchUserData = async () => {
  loading.value = true
  try {
    const id = route.params.id
    const response = await api.get(`/getUser/${id}`)
    const u = response.data.user
    
    form.value = { ...u, photo: null } 
    if (u.photo) {
      photoPreview.value = `http://127.0.0.1:8000/storage/${u.photo}`
    }

    if (u.skills) {
      selectedSkills.value = u.skills.map(s => ({
        skill_id: s.id,
        level: s.pivot?.level || 'beginner'
      }))
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
    
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && key !== 'skills') {
        formData.append(key, form.value[key])
      }
    })

    selectedSkills.value.forEach((skill, index) => {
      formData.append(`skills[${index}][id]`, skill.skill_id) 
      formData.append(`skills[${index}][level]`, skill.level)
    })

    formData.append('_method', 'PUT')

    await api.post(`/updateUser/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    success.value = true
    setTimeout(() => router.push(`/users`), 1500) 
  } catch (err) {
    error.value = err.response?.data?.message || "Erreur lors de la mise à jour."
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchAvailableSkills()
  fetchUserData()
  getAuthUser()
})
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased py-12 px-4 sm:px-6 lg:px-8 w-full overflow-y-auto">
    <div class="max-w-5xl mx-auto space-y-8">

      <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-sm">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Modifier Collaborateur</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Mise à jour des informations de l'employé #{{ route.params.id }}</p>
        </div>
        
        <button type="button" @click="router.back()" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-2 bg-white border border-slate-200 px-4 py-2.5 rounded-xl shadow-sm active:scale-95 hover:bg-slate-50 transition-all">
          <i class="fas fa-arrow-left"></i> Annuler & Retour
        </button>
      </div>

      <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-slate-200/60 shadow-sm">
        <div class="w-10 h-10 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
      </div>

      <div v-else-if="success" class="bg-emerald-50 border border-emerald-100 p-6 rounded-2xl flex flex-col items-center justify-center text-emerald-800 animate-bounce">
        <i class="fas fa-check-circle text-3xl mb-2"></i>
        <p class="font-bold">Modifications enregistrées avec succès !</p>
      </div>

      <form v-else-if="form" @submit.prevent="submitUpdate" class="space-y-8">
        
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

              <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100/80 space-y-5 mt-6">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                  <div class="flex items-center gap-2">
                    <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-bold">04</span>
                    <div>
                      <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Cartographie des Compétences</h3>
                      <p class="text-xs text-slate-400 mt-0.5">Sélectionnez les compétences clés et qualifiez le niveau d'expertise globale.</p>
                    </div>
                  </div>
                  <button type="button" @click="isModalOpen = true" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100/80 px-3 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0 shadow-sm">
                    <i class="fas fa-plus"></i> Nouvelle compétence
                  </button>
                </div>

                <div v-if="availableSkills.length === 0" class="text-sm text-slate-400 py-8 text-center bg-white rounded-xl border border-slate-200/60 shadow-inner">
                  🔄 Aucune compétence disponible pour le moment...
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 pt-2">
                  <div v-for="skill in availableSkills" :key="skill.id" class="bg-white border rounded-xl p-3 flex flex-col justify-between gap-3 transition-all duration-200 shadow-sm" :class="isSelected(skill.id) ? 'border-indigo-200 bg-indigo-50/20 ring-1 ring-indigo-100' : 'border-slate-200/70 hover:border-slate-300'">
                    <button type="button" @click="toggleSkill(skill.id)" class="flex items-center justify-between text-left w-full group">
                      <span class="text-xs font-bold transition-colors" :class="isSelected(skill.id) ? 'text-indigo-900' : 'text-slate-700 group-hover:text-indigo-600'">
                        {{ skill.name }}
                      </span>
                      <span class="w-4.5 h-4.5 rounded-full border flex items-center justify-center text-[10px] transition-all" :class="isSelected(skill.id) ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-300 group-hover:border-indigo-400 text-transparent'">
                        ✓
                      </span>
                    </button>

                    <div v-if="isSelected(skill.id)" class="relative mt-1">
                      <select :value="getLevel(skill.id)" @change="setLevel(skill.id, $event.target.value)" class="w-full text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 outline-none focus:border-indigo-500 bg-white text-slate-700 appearance-none cursor-pointer font-medium shadow-sm">
                        <option value="beginner">🟢 Débutant</option>
                        <option value="intermediate">🟡 Intermédiaire</option>
                        <option value="advanced">🔴 Avancé</option>
                      </select>
                      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="selectedSkills.length > 0" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold mt-2">
                  <span>✨</span> {{ selectedSkills.length }} compétence(s) affectée(s) avec succès
                </div>
              </div>

              <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                <p v-if="error" class="text-[10px] font-bold text-rose-500 uppercase mr-auto tracking-tight"><i class="fas fa-exclamation-circle"></i> {{ error }}</p>
                
                <button type="button" @click="router.back()" class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                  Retour
                </button>
                <button type="submit" :disabled="saving" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-600/20 flex items-center gap-2 active:scale-95 transition-all">
                  <i v-if="saving" class="fas fa-spinner animate-spin"></i>
                  <span>{{ saving ? 'Enregistrement...' : 'Sauvegarder' }}</span>
                </button>
              </div>

            </div>
          </div>

        </div>
      </form>
    </div>

    <div v-if="isModalOpen" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 animate-fadeIn">
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xl w-full max-w-md p-6 overflow-hidden relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-5">
          <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Créer une nouvelle compétence</h3>
          <button @click="isModalOpen = false" class="text-slate-400 hover:text-slate-600 text-sm"><i class="fas fa-times"></i></button>
        </div>

        <form @submit.prevent="handleAddSkillPool" class="space-y-4">
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-1">Nom de la compétence</label>
            <input v-model="newSkillName" type="text" placeholder="Ex: Vue.js, Docker..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold outline-none focus:border-indigo-500" required>
          </div>

          <p v-if="modalError" class="text-[10px] font-bold text-rose-500 uppercase tracking-tight"><i class="fas fa-exclamation-circle"></i> {{ modalError }}</p>

          <div class="pt-4 flex justify-end gap-2.5">
            <button type="button" @click="isModalOpen = false" class="px-4 py-2.5 text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-slate-600">
              Annuler
            </button>
            <button type="submit" :disabled="creatingSkill" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all flex items-center gap-1.5">
              <i v-if="creatingSkill" class="fas fa-spinner animate-spin"></i>
              <span>{{ creatingSkill ? 'Création...' : 'Ajouter' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>