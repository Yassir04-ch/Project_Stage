<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'

const justifications = ref([])
const loading = ref(false)
const errorMsg = ref("")
const successMsg = ref("")

const form = ref({
  absence_id: '',
  justified_by: '',
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
    errorMsg.value = "Veuillez remplir tous les champs obligatoires."
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
    
    // Reset Form smoothly
    form.value = {
      absence_id: '',
      justified_by: '',
      type: '',
      reason: '',
      status: 'pending',
      proof_file: null,
    }
    
    // Clear the file input HTML view
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
  <div class="min-h-screen bg-slate-50/60 font-sans antialiased p-4 sm:p-6 md:p-8">
    <div class="max-w-6xl mx-auto space-y-6">
      
      <div class="flex flex-col gap-1 border-b border-slate-200 pb-4">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Gestion des Justifications</h1>
        <p class="text-slate-400 text-xs">Soumettre et suivre les pièces justificatives des incidents de présence.</p>
      </div>

      <div v-if="successMsg" class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-semibold animate-fadeIn flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-lg bg-emerald-500 text-white text-[10px]">✓</span>
        <p>{{ successMsg }}</p>
      </div>
      <div v-if="errorMsg" class="p-3.5 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-semibold animate-fadeIn flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-lg bg-rose-500 text-white text-[10px]">⚠️</span>
        <p>{{ errorMsg }}</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="bg-white rounded-2xl border border-slate-200/70 shadow-[0_8px_30px_rgba(0,0,0,0.02)] p-5 sm:p-6 relative overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-[3px] bg-indigo-600"></div>
          
          <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4">Nouvelle Justification</h2>
          
          <form @submit.prevent="createJustification" class="space-y-4">
            
            <div class="space-y-1">
              <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">ID de l'absence *</label>
              <input 
                v-model="form.absence_id" 
                type="text"
                required
                placeholder="Ex: 42" 
                class="w-full border border-slate-200 px-3 py-2 text-xs rounded-xl outline-none transition-all bg-slate-50/40 focus:border-indigo-500 focus:bg-white text-slate-700 font-medium" 
              />
            </div>

            <div class="space-y-1">
              <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">ID de l'employé (Optionnel)</label>
              <input 
                v-model="form.justified_by" 
                type="text"
                placeholder="Ex: 10" 
                class="w-full border border-slate-200 px-3 py-2 text-xs rounded-xl outline-none transition-all bg-slate-50/40 focus:border-indigo-500 focus:bg-white text-slate-700 font-medium" 
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Type *</label>
                <select v-model="form.type" required class="w-full border border-slate-200 px-2.5 py-2 text-xs rounded-xl outline-none transition-all bg-slate-50/40 focus:border-indigo-500 focus:bg-white text-slate-700 font-medium cursor-pointer">
                  <option value="" disabled selected>Choisir...</option>
                  <option value="medical">🩺 Médical</option>
                  <option value="family">🏠 Familial</option>
                  <option value="mission">💼 Mission</option>
                  <option value="other">📌 Autre</option>
                </select>
              </div>

              <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Statut initial</label>
                <select v-model="form.status" class="w-full border border-slate-200 px-2.5 py-2 text-xs rounded-xl outline-none transition-all bg-slate-50/40 focus:border-indigo-500 focus:bg-white text-slate-700 font-medium cursor-pointer">
                  <option value="pending">⏳ En attente</option>
                  <option value="approved">✅ Approuvé</option>
                  <option value="rejected">🛑 Rejeté</option>
                </select>
              </div>
            </div>

            <div class="space-y-1">
              <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Motif explicatif</label>
              <textarea
                v-model="form.reason"
                rows="3"
                placeholder="Détails concernant le justificatif fourni..."
                class="w-full border border-slate-200 px-3 py-2 text-xs rounded-xl outline-none transition-all bg-slate-50/40 focus:border-indigo-500 focus:bg-white text-slate-700 resize-none"
              ></textarea>
            </div>

            <div class="space-y-1">
              <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Document justificatif (Proof)</label>
              <div class="border-2 border-dashed border-slate-200 rounded-xl p-3 bg-slate-50/30 text-center hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer relative">
                <input 
                  id="proof_file_input"
                  type="file" 
                  @change="handleFile" 
                  class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                />
                <div class="space-y-1 pointer-events-none">
                  <span class="text-base block">📁</span>
                  <p class="text-[11px] font-medium text-slate-600">
                    {{ form.proof_file ? form.proof_file.name : 'Sélectionner le document' }}
                  </p>
                  <p class="text-[9px] text-slate-400" v-if="!form.proof_file">PDF, JPG, PNG max 5MB</p>
                </div>
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all duration-150 shadow-xs active:scale-[0.99] uppercase tracking-wider flex justify-center items-center gap-2 mt-2"
            >
              <span v-if="loading" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              {{ loading ? 'Transmission...' : 'Enregistrer le document' }}
            </button>

          </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/70 shadow-[0_8px_30px_rgba(0,0,0,0.02)] overflow-hidden">
          <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Documents en cours de traitement</h3>
            <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md font-semibold font-mono">{{ justifications.length }} entrée(s)</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-slate-50/70 border-b border-slate-200/60">
                  <th class="p-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider w-16">ID</th>
                  <th class="p-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Catégorie</th>
                  <th class="p-3.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Raison / Motif</th>
                  <th class="p-3.5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider w-28">Statut</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-100">
                <tr v-for="j in justifications" :key="j.id" class="hover:bg-slate-50/50 transition-colors">
                  <td class="p-3.5 text-xs font-mono font-bold text-slate-600">#{{ j.id }}</td>
                  
                  <td class="p-3.5 text-xs text-slate-700 font-medium capitalize">
                    <span class="inline-flex items-center gap-1.5">
                      <span v-if="j.type === 'medical'">🩺</span>
                      <span v-else-if="j.type === 'family'">🏠</span>
                      <span v-else-if="j.type === 'mission'">💼</span>
                      <span v-else>📌</span>
                      {{ j.type }}
                    </span>
                  </td>

                  <td class="p-3.5 text-xs text-slate-500 max-w-xs truncate" :title="j.reason">
                    {{ j.reason || 'Aucune description fournie' }}
                  </td>

                  <td class="p-3.5 text-right whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide"
                      :class="{
                        'bg-amber-50 text-amber-700 border border-amber-100': j.status === 'pending',
                        'bg-emerald-50 text-emerald-700 border border-emerald-100': j.status === 'approved',
                        'bg-rose-50 text-rose-700 border border-rose-100': j.status === 'rejected',
                      }"
                    >
                      <span class="w-1.5 h-1.5 rounded-full mr-1.5 animate-pulse"
                        :class="{
                          'bg-amber-500': j.status === 'pending',
                          'bg-emerald-500': j.status === 'approved',
                          'bg-rose-500': j.status === 'rejected',
                        }"
                      ></span>
                      {{ j.status === 'pending' ? 'En attente' : j.status === 'approved' ? 'Approuvé' : 'Rejeté' }}
                    </span>
                  </td>
                </tr>

                <tr v-if="justifications.length === 0">
                  <td colspan="4" class="p-10 text-center text-slate-400 text-xs">
                    <span class="text-2xl block mb-2">📂</span>
                    Aucun justificatif enregistré dans la base de données.
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
  from { opacity: 0; transform: translateY(2px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>