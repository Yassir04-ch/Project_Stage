<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()
const justifications = ref([])
const loading = ref(false)
const errorMsg = ref("")
const successMsg = ref("")

// Récupération des données depuis l'URL de façon fluide
const employeeName = ref(route.query.employee_name || route.params.employee_name || 'Collaborateur')

const form = ref({
  absence_id: route.query.absence_id || route.params.absence_id || '',
  justified_by: route.query.employee_id || route.params.employee_id || '',
  type: '',
  reason: '',
  status: 'pending',
  proof_file: null,
})

const getJustifications = async () => {
  try {
    const response = await api.get('/justifications', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    justifications.value = response.data.justifications || response.data
  } catch (error) {
    console.error(error.response?.data)
    errorMsg.value = "Impossible de récupérer la liste des justifications."
  }
}

const handleFile = (e) => {
  form.value.proof_file = e.target.files[0]
}

const createJustification = async () => {
  if (!form.value.absence_id || !form.value.type) {
    errorMsg.value = "Veuillez remplir tous les champs obligatoires (Type de justificatif)."
    return
  }

  loading.value = true
  errorMsg.value = ""
  successMsg.value = ""

  try {
    const formData = new FormData()
    Object.keys(form.value).forEach((key) => {
      if (form.value[key] !== null && form.value[key] !== '') {
        formData.append(key, form.value[key])
      }
    })

    await api.post('/justifications', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data',
      },
    })

    successMsg.value = "La justification a été soumise avec succès."
    
    // Reset Form smoothly en gardant le même contexte
    form.value = {
      absence_id: route.query.absence_id || route.params.absence_id || '',
      justified_by: route.query.employee_id || route.params.employee_id || '',
      type: '',
      reason: '',
      status: 'pending',
      proof_file: null,
    }
    
    const fileInput = document.getElementById('proof_file_input')
    if (fileInput) fileInput.value = ''

    await getJustifications()
  } catch (error) {
    console.error(error.response?.data)
    errorMsg.value = error.response?.data?.message || "Une erreur est survenue lors de la création."
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  getJustifications()
})
</script>

<template>
  <div class="h-screen w-screen bg-slate-50/50 font-sans antialiased p-6 flex flex-col justify-between overflow-hidden">
    <div class="w-full max-w-7xl mx-auto flex flex-col h-full space-y-6 animate-fadeIn">
      
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/60 pb-4 shrink-0">
        <div>
          <h1 class="text-2xl font-black tracking-tight text-slate-900">
            Gestion des Justifications
          </h1>
          <p class="text-slate-400 text-xs font-medium mt-0.5">
            Soumettre et suivre les pièces justificatives des incidents de présence.
          </p>
        </div>
        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs w-fit">
          <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
          <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider font-mono">Espace RH / Admin</span>
        </div>
      </div>

      <div v-if="successMsg || errorMsg" class="shrink-0 animate-fadeIn">
        <div v-if="successMsg" class="p-3 rounded-xl bg-emerald-50 border border-emerald-100/80 text-emerald-900 text-xs font-semibold flex items-center gap-3 shadow-2xs">
          <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-lg bg-emerald-500 text-white shadow-xs text-[10px]">✓</span>
          <p>{{ successMsg }}</p>
        </div>
        
        <div v-if="errorMsg" class="p-3 rounded-xl bg-rose-50 border border-rose-100/80 text-rose-900 text-xs font-semibold flex items-center gap-3 shadow-2xs">
          <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-lg bg-rose-500 text-white shadow-xs text-[10px]">⚠️</span>
          <p>{{ errorMsg }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch flex-1 overflow-hidden min-h-0 pb-2">
        
        <div class="bg-white rounded-2xl border border-slate-200/70 shadow-[0_10px_30px_rgba(0,0,0,0.02)] p-5 relative overflow-hidden flex flex-col justify-between h-full">
          <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-indigo-500 to-purple-600"></div>
          
          <div class="flex items-center gap-2 mb-3 shrink-0">
            <span class="text-base">📝</span>
            <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Nouvelle Justification</h2>
          </div>

          <div class="mb-4 p-3 bg-slate-50 border border-slate-200/60 rounded-xl flex items-center justify-between shrink-0">
            <span class="text-slate-400 text-[11px] font-extrabold uppercase tracking-wide">Nom Collaborateur:</span>
            <span class="text-xs font-bold text-slate-800 bg-white border border-slate-200/80 px-3 py-1 rounded-lg shadow-2xs">
              👤 {{ employeeName }}
            </span>
          </div>
          
          <form @submit.prevent="createJustification" class="space-y-4 flex-1 flex flex-col justify-between min-h-0">
            
            <div class="space-y-4 overflow-y-auto pr-1">
              <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Type de justificatif <span class="text-rose-500">*</span></label>
                <select v-model="form.type" required class="w-full border border-slate-200 px-3 py-2 text-xs rounded-xl outline-none transition-all bg-slate-50/40 focus:border-indigo-500 focus:bg-white text-slate-700 font-semibold cursor-pointer shadow-2xs">
                  <option value="" disabled selected>Choisir le type...</option>
                  <option value="medical">🩺 Médical</option>
                  <option value="family">🏠 Familial</option>
                  <option value="mission">💼 Mission</option>
                  <option value="other">📌 Autre</option>
                </select>
              </div>

              <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Motif explicatif</label>
                <textarea
                  v-model="form.reason"
                  rows="3"
                  placeholder="Détails concernant le justificatif fourni..."
                  class="w-full border border-slate-200 px-3 py-2 text-xs rounded-xl outline-none transition-all bg-slate-50/40 focus:border-indigo-500 focus:bg-white text-slate-700 resize-none leading-relaxed shadow-2xs"
                ></textarea>
              </div>

              <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Document justificatif (Proof)</label>
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-3 text-center bg-slate-50/30 hover:bg-indigo-50/10 hover:border-indigo-400/60 transition-all cursor-pointer relative group shadow-2xs">
                  <input 
                    id="proof_file_input"
                    type="file" 
                    @change="handleFile" 
                    class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10"
                  />
                  <div class="space-y-1 pointer-events-none transition-transform group-hover:scale-[1.01]">
                    <span class="text-xl block drop-shadow-xs">📁</span>
                    <p class="text-[11px] font-bold text-slate-700 truncate max-w-[180px] mx-auto px-1">
                      {{ form.proof_file ? form.proof_file.name : 'Sélectionner le fichier' }}
                    </p>
                    <p class="text-[9px] text-slate-400 font-medium" v-if="!form.proof_file">PDF, JPG, PNG (Max 5MB)</p>
                    <span v-else class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 border border-indigo-100 text-indigo-700 uppercase tracking-wider animate-fadeIn">Fichier chargé</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="pt-3 mt-auto shrink-0">
              <button
                type="submit"
                :disabled="loading"
                class="w-full bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all duration-200 shadow-md active:scale-[0.99] uppercase tracking-wider flex justify-center items-center gap-2.5 disabled:opacity-40 disabled:cursor-not-allowed"
              >
                <span v-if="loading" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span>{{ loading ? 'Transmission...' : 'Enregistrer le document' }}</span>
              </button>
            </div>

          </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/70 shadow-[0_10px_30px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col h-full">
          <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40 shrink-0">
            <div class="flex items-center gap-2">
              <span class="text-sm">📂</span>
              <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Documents traités</h3>
            </div>
            <span class="text-[10px] bg-slate-200/70 text-slate-600 px-2.5 py-0.5 rounded-md font-bold font-mono border border-slate-300/30">
              {{ justifications.length }} entrée(s)
            </span>
          </div>

          <div class="overflow-auto flex-1 min-h-0">
            <table class="w-full border-collapse">
              <thead class="sticky top-0 bg-white z-10 shadow-xs">
                <tr class="bg-slate-50/80 border-b border-slate-200/40">
                  <th class="p-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider w-20">ID</th>
                  <th class="p-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">Catégorie</th>
                  <th class="p-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">Raison / Motif</th>
                  <th class="p-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-wider w-32">Statut</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-100/80">
                <tr v-for="j in justifications" :key="j.id" class="hover:bg-slate-50/40 transition-all duration-150 group">
                  <td class="p-4 text-xs font-mono font-bold text-slate-600">#{{ j.id }}</td>
                  
                  <td class="p-4 text-xs text-slate-800 font-bold capitalize">
                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/60 group-hover:bg-white group-hover:border-slate-300/80 transition-all shadow-2xs">
                      <span v-if="j.type === 'medical'">🩺</span>
                      <span v-else-if="j.type === 'family'">🏠</span>
                      <span v-else-if="j.type === 'mission'">💼</span>
                      <span v-else>📌</span>
                      <span>{{ j.type }}</span>
                    </span>
                  </td>

                  <td class="p-4 text-xs text-slate-500 max-w-xs truncate font-medium" :title="j.reason">
                    {{ j.reason || 'Aucune description fournie' }}
                  </td>

                  <td class="p-4 text-right whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border"
                      :class="{
                        'bg-amber-50 text-amber-700 border-amber-200/60 shadow-[0_2px_10px_rgba(245,158,11,0.04)]': j.status === 'pending',
                        'bg-emerald-50 text-emerald-700 border-emerald-200/60 shadow-[0_2px_10px_rgba(16,185,129,0.04)]': j.status === 'approved',
                        'bg-rose-50 text-rose-700 border-rose-200/60 shadow-[0_2px_10px_rgba(239,68,68,0.04)]': j.status === 'rejected',
                      }"
                    >
                      <span class="w-1.5 h-1.5 rounded-full mr-2"
                        :class="{
                          'bg-amber-500 animate-pulse': j.status === 'pending',
                          'bg-emerald-500': j.status === 'approved',
                          'bg-rose-500': j.status === 'rejected',
                        }"
                      ></span>
                      {{ j.status === 'pending' ? 'En attente' : j.status === 'approved' ? 'Approuvé' : 'Rejeté' }}
                    </span>
                  </td>
                </tr>

                <tr v-if="justifications.length === 0">
                  <td colspan="4" class="p-12 text-center text-slate-400 text-xs">
                    <div class="flex flex-col items-center justify-center gap-3">
                      <span class="text-3xl filter grayscale opacity-75">📥</span>
                      <div>
                        <p class="font-bold text-slate-700 text-sm">Registre vide</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Aucun justificatif n'a été enregistré pour le moment.</p>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>