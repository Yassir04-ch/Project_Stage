<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import assignmentService from '@/services/assignmentService'

const route = useRoute()

const loading = ref(false)
const submitting = ref(false)
const deletingId = ref(null)

const project = ref({})
const assignments = ref([])
const freeEmployees = ref([])
const busyEmployees = ref([])

const showForm = ref(false)

const flashMsg = ref('')
const flashType = ref('success')

const form = ref({
  employee_id: '',
  role_in_project: '',
  start_date: '',
  end_date: '',
})

const formErrors = ref({})

const resetForm = () => {
  form.value = {
    employee_id: '',
    role_in_project: '',
    start_date: '',
    end_date: '',
  }
  formErrors.value = {}
}

const showFlash = (msg, type = 'success') => {
  flashMsg.value = msg
  flashType.value = type
  setTimeout(() => {
    flashMsg.value = ''
  }, 3000)
}

const fetchData = async () => {
  loading.value = true
  try {
    const response = await assignmentService.getAssignments(route.params.id)
    const data = response.data
    project.value = data.project || {}
    assignments.value = data.assignments || []
    freeEmployees.value = data.free_employees || []
    console.log(freeEmployees.value)
    busyEmployees.value = data.busy_employees || []
  } catch (err) {
    console.log(err.response?.data)
    showFlash('Erreur chargement', 'error')
  } finally {
    loading.value = false
  }
}

const storeAssignment = async () => {
  submitting.value = true
  formErrors.value = {}
  try {
    await assignmentService.createAssignment(route.params.id, form.value)
    resetForm()
    showForm.value = false
    await fetchData()
    showFlash('Employé assigné successfully!')
  } catch (e) {
    console.log(e.response?.data)
    if (e.response?.status === 422) {
      formErrors.value = e.response.data.errors || {}
    } else {
      showFlash("Erreur lors de l'assignation", 'error')
    }
  } finally {
    submitting.value = false
  }
}

const deleteAssignment = async (id) => {
  deletingId.value = id
  try {
    await assignmentService.deleteAssignment(id)
    assignments.value = assignments.value.filter((a) => a.id !== id)
    showFlash('Assignation supprimée')
  } catch (e) {
    console.log(e.response?.data)
    showFlash('Erreur suppression', 'error')
  } finally {
    deletingId.value = null
  }
}

const selectedBusyEmployee = computed(() => {
  return busyEmployees.value.find((e) => e.id == form.value.employee_id)
})

const fullName = (employee) => {
  if (!employee) return ''
  return `${employee.firstname || ''} ${employee.lastname || ''}`
}

const avatarInitials = (name) => {
  if (!name) return '?'
  return name
    .split(' ')
    .map((v) => v[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
}

const formatDate = (date) => {
  if (!date) return '--'
  return new Date(date).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })
}

const colors = [
  'from-indigo-500 to-cyan-400 text-white shadow-indigo-500/20',
  'from-emerald-500 to-teal-400 text-white shadow-emerald-500/20',
  'from-pink-500 to-rose-400 text-white shadow-pink-500/20',
  'from-amber-500 to-orange-400 text-white shadow-amber-500/20',
  'from-sky-500 to-blue-400 text-white shadow-sky-500/20'
]

const ganttColor = (id) => {
  return colors[id % colors.length]
}

onMounted(fetchData)
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-[#f8fafc] text-slate-600 font-sans antialiased p-4 sm:p-6 lg:p-8 relative overflow-x-hidden">
    
    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-600/5 rounded-full blur-[140px] pointer-events-none"></div>

    <div class="max-w-6xl mx-auto space-y-6 relative z-10">
      
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0 border border-indigo-100">
            <i class="fas fa-users-gear"></i>
          </div>
          <div>
            <h1 class="font-black text-xl text-slate-800 tracking-tight leading-none">
              {{ project.name || 'Project Team allocation' }}
            </h1>
            <p class="text-xs text-slate-400 mt-1.5 font-medium flex items-center gap-1.5">
              <i class="far fa-calendar text-[11px]"></i>
              {{ formatDate(project.start_date) }} <i class="fas fa-long-arrow-right text-[10px] text-slate-300"></i> {{ formatDate(project.end_date) }}
            </p>
          </div>
        </div>

        <button
          @click="showForm = !showForm"
          class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 transition-all active:scale-95 shrink-0 self-end sm:self-auto"
        >
          <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus-user'"></i>
          <span>{{ showForm ? 'Fermer Panel' : 'Assigner Membre' }}</span>
        </button>
      </div>

      <Transition name="fade">
        <div
          v-if="flashMsg"
          class="p-4 rounded-xl border flex items-center gap-3 text-xs font-semibold shadow-sm backdrop-blur-md"
          :class="flashType === 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'"
        >
          <i class="fas" :class="flashType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
          <p>{{ flashMsg }}</p>
        </div>
      </Transition>

      <div v-if="showForm" class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4 animate-[slideDown_0.2s_ease-out]">
        <div class="border-b border-slate-50 pb-3">
          <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
            <i class="fas fa-id-badge text-slate-300"></i> Configuration de l'assignation
          </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
          
          <div class="space-y-1">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Employé</label>
            <select v-model="form.employee_id" class="border border-slate-200 bg-slate-50/50 rounded-xl p-2.5 text-xs font-medium w-full focus:bg-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
              <option value="">Sélectionner...</option>
              <optgroup label="✨ Employés Libres" v-if="freeEmployees.length">
                <option v-for="e in freeEmployees" :key="e.id" :value="e.id">🟢 {{ fullName(e) }}</option>
              </optgroup>
              <optgroup label="⚠️ Employés Occupés (Autre projet)" v-if="busyEmployees.length">
                <option v-for="e in busyEmployees" :key="e.id" :value="e.id">🟡 {{ fullName(e) }}</option>
              </optgroup>
            </select>
            <p v-if="formErrors.employee_id" class="text-rose-500 text-[11px] font-medium mt-0.5">{{ formErrors.employee_id[0] }}</p>
          </div>

          <div class="space-y-1">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Rôle assigné</label>
            <input
              v-model="form.role_in_project"
              placeholder="Ex: Lead Developer"
              class="border border-slate-200 bg-slate-50/50 rounded-xl p-2.5 text-xs font-medium w-full focus:bg-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
            />
          </div>

          <div class="space-y-1">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Date de Début</label>
            <input type="date" v-model="form.start_date" class="border border-slate-200 bg-slate-50/50 rounded-xl p-2.5 text-xs font-mono w-full focus:bg-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
          </div>

          <div class="space-y-1">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Date de Fin</label>
            <input type="date" v-model="form.end_date" class="border border-slate-200 bg-slate-50/50 rounded-xl p-2.5 text-xs font-mono w-full focus:bg-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
          </div>

        </div>

        <div v-if="selectedBusyEmployee" class="bg-amber-50 border border-amber-200 text-amber-700 rounded-xl px-4 py-2.5 text-xs flex items-center gap-2 font-medium">
          <i class="fas fa-exclamation-triangle"></i>
          <span>Attention: Cet employé collabore déjà sur un autre projet actif.</span>
        </div>

        <div class="flex justify-end pt-2">
          <button
            @click="storeAssignment"
            :disabled="submitting"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-sm disabled:opacity-50 transition-all flex items-center gap-1.5"
          >
            <i class="fas" :class="submitting ? 'fa-spinner animate-spin' : 'fa-save'"></i>
            <span>{{ submitting ? 'Enregistrement...' : 'Confirmer Allocation' }}</span>
          </button>
        </div>
      </div>

      <div class="space-y-3">
        <div class="px-2 flex items-center justify-between">
          <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
            <i class="fas fa-user-group text-slate-300"></i> Membres Assignés ({{ assignments.length }})
          </h2>
        </div>

        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="n in 3" :key="n" class="bg-white border border-slate-100 p-5 rounded-2xl animate-pulse flex items-center gap-4">
            <div class="w-10 h-10 bg-slate-100 rounded-full shrink-0"></div>
            <div class="space-y-2 flex-1">
              <div class="h-3 bg-slate-100 rounded w-1/2"></div>
              <div class="h-2 bg-slate-50 rounded w-1/3"></div>
            </div>
          </div>
        </div>

        <div v-else-if="assignments.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="a in assignments"
            :key="a.id"
            class="bg-white border border-slate-100 hover:border-slate-200 rounded-2xl p-4.5 flex items-start justify-between gap-4 transition-all hover:shadow-sm group relative"
          >
            <div class="flex items-center gap-3.5 overflow-hidden">
              <div
                class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs uppercase tracking-wider shrink-0 bg-gradient-to-br shadow-sm"
                :class="ganttColor(a.employee_id)"
              >
                {{ avatarInitials(fullName(a.employee)) }}
              </div>

              <div class="overflow-hidden">
                <h4 class="font-bold text-slate-800 text-sm truncate group-hover:text-indigo-600 transition-colors" :title="fullName(a.employee)">
                  {{ fullName(a.employee) }}
                </h4>
                <p class="text-xs text-slate-400 font-semibold truncate mt-0.5">
                  {{ a.role_in_project || 'Collaborateur' }}
                </p>
                <div class="mt-2 text-[10px] font-medium text-slate-400 flex items-center gap-1 font-mono">
                  <span class="bg-slate-50 px-1 py-0.2 rounded text-slate-600">{{ formatDate(a.start_date) }}</span>
                  <span class="text-slate-300">→</span>
                  <span class="bg-slate-50 px-1 py-0.2 rounded text-slate-500">{{ formatDate(a.end_date) }}</span>
                </div>
              </div>
            </div>

            <button
              @click="deleteAssignment(a.id)"
              :disabled="deletingId === a.id"
              class="w-7 h-7 flex items-center justify-center text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all shrink-0 active:scale-90"
              title="Supprimer l'affectation"
            >
              <i class="fas" :class="deletingId === a.id ? 'fa-spinner animate-spin' : 'fa-trash-alt text-xs'"></i>
            </button>
          </div>
        </div>

        <div v-else class="bg-white border border-slate-100 rounded-2xl p-12 text-center text-slate-400">
          <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mx-auto mb-3 text-lg border border-slate-100">
            <i class="fas fa-user-slash text-slate-300"></i>
          </div>
          <p class="text-xs font-bold text-slate-700 uppercase tracking-wide">Aucun membre assigné</p>
          <p class="text-[11px] text-slate-400 mt-0.5">Cliquez sur "Assigner Membre" pour ajouter des ressources à ce projet.</p>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>