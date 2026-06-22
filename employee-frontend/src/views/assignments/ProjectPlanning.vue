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
const mobileMenuOpen = ref(false) // Dynamic state for responsive nav drawer

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
  'from-indigo-500 to-cyan-500 text-white shadow-indigo-500/10',
  'from-emerald-500 to-teal-500 text-white shadow-emerald-500/10',
  'from-pink-500 to-rose-500 text-white shadow-pink-500/10',
  'from-amber-500 to-orange-500 text-white shadow-amber-500/10',
  'from-sky-500 to-blue-500 text-white shadow-sky-500/10'
]

const ganttColor = (id) => {
  return colors[id % colors.length]
}

onMounted(fetchData)
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-[#f8fafc] text-slate-600 font-sans antialiased flex flex-col md:flex-row relative">
    
    <div class="absolute top-0 right-0 w-[800px] h-[350px] bg-gradient-to-b from-indigo-50/30 via-slate-50/5 to-transparent rounded-full blur-3xl -z-10 pointer-events-none"></div>

    <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-400 shrink-0 min-h-screen border-r border-slate-800 shadow-xl relative z-30">
      <div class="p-6 border-b border-slate-800 flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-indigo-500/20">
            D
          </div>
          <div>
            <h1 class="text-lg font-black tracking-tight text-white uppercase italic">DATAXPRESS</h1>
            <p class="text-xs text-indigo-400 font-bold tracking-wider uppercase">Workspace Admin</p>
          </div>
      </div>

      <nav class="space-y-1.5 p-4 flex-1">
        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 font-semibold transition-all text-sm group text-left">
          <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">🏠</span>
          <span>Dashboard</span>
        </button>

        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 font-semibold transition-all text-sm group text-left">
          <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">📁</span>
          <span>Projects</span>
        </button>

        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 group transition-all text-sm text-left">
          <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">👥</span>
          <span>Utilisateurs</span>
        </button>

        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 font-semibold transition-all text-sm group text-left">
          <i class="fa-regular fa-calendar-minus text-base w-5 group-hover:scale-110 transition-transform"></i>
          <span>Absences</span>
        </button>
      </nav>

      <div class="p-4 border-t border-slate-800 bg-slate-950/40 flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-white shadow-inner">
          YC
        </div>
        <div class="overflow-hidden flex-1">
          <h4 class="text-xs font-bold text-slate-200 truncate leading-none">Administrateur HR</h4>
          <p class="text-[10px] text-slate-500 truncate mt-1">session@youcode.ma</p>
        </div>
      </div>
    </aside>

    <header class="md:hidden bg-slate-900 text-slate-400 border-b border-slate-800 flex items-center justify-between px-5 py-4 shrink-0 relative z-30 shadow-md">
      <div class="flex items-center gap-3">
        <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-xs font-black">HR</div>
        <h2 class="text-white font-bold text-xs tracking-tight">Enterprise Suite</h2>
      </div>
      <button @click="mobileMenuOpen = !mobileMenuOpen" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-800 text-slate-200 transition-colors">
        <i class="fas" :class="mobileMenuOpen ? 'fa-xmark text-sm' : 'fa-bars text-sm'"></i>
      </button>

      <div v-if="mobileMenuOpen" class="absolute top-[100%] left-0 w-full bg-slate-900 border-b border-slate-800 p-4 space-y-1 shadow-xl animate-[slideDown_0.15s_ease-out]">
        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 text-white text-xs font-bold">🏠 Dashboard</button>
        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white text-xs font-bold">👥 Utilisateurs</button>
        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 text-white text-xs font-bold">📋 Absences</button>
      </div>
    </header>

    <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto z-10">
      <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_2px_12px_rgba(15,23,42,0.02)] p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100/60 text-indigo-600 flex items-center justify-center text-lg shrink-0 shadow-sm">
              <i class="fas fa-cubes"></i>
            </div>
            <div>
              <h1 class="font-bold text-lg sm:text-xl text-slate-900 tracking-tight leading-tight">
                {{ project.name || 'Project Team Allocation' }}
              </h1>
              <p class="text-xs text-slate-400 mt-1 font-semibold flex items-center gap-2">
                <span class="inline-flex items-center gap-1 bg-slate-50 border border-slate-200/60 px-2 py-0.5 rounded-md text-slate-500">
                  <i class="far fa-calendar-alt text-[10px] text-slate-400"></i>
                  {{ formatDate(project.start_date) }}
                </span>
                <i class="fas fa-arrow-right text-[10px] text-slate-300"></i>
                <span class="inline-flex items-center gap-1 bg-slate-50 border border-slate-200/60 px-2 py-0.5 rounded-md text-slate-500">
                  {{ formatDate(project.end_date) }}
                </span>
              </p>
            </div>
          </div>

          <button
            @click="showForm = !showForm"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all active:scale-[0.98] shrink-0 border shadow-sm"
            :class="showForm 
              ? 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' 
              : 'bg-indigo-600 hover:bg-indigo-700 text-white border-indigo-600 shadow-indigo-600/10'"
          >
            <i class="fas" :class="showForm ? 'fa-xmark' : 'fa-user-plus'"></i>
            <span>{{ showForm ? 'Fermer Panel' : 'Assigner Membre' }}</span>
          </button>
        </div>

        <Transition name="fade">
          <div
            v-if="flashMsg"
            class="p-4 rounded-xl border flex items-center gap-3 text-xs font-bold shadow-sm backdrop-blur-md animate-[slideDown_0.2s_ease-out]"
            :class="flashType === 'success' ? 'bg-emerald-50/90 text-emerald-800 border-emerald-200/60' : 'bg-rose-50/90 text-rose-800 border-rose-200/60'"
          >
            <i class="fas" :class="flashType === 'success' ? 'fa-circle-check text-emerald-600' : 'fa-circle-exclamation text-rose-600'"></i>
            <p>{{ flashMsg }}</p>
          </div>
        </Transition>

        <div v-if="showForm" class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-[0_4px_20px_rgba(15,23,42,0.03)] space-y-5 animate-[slideDown_0.2s_ease-out]">
          <div class="pb-2 border-b border-slate-100">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
              <span class="w-1.5 h-3 bg-indigo-600 rounded-full"></span>
              Configuration de l'assignation du personnel
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-bold text-slate-600">Employé ciblée</label>
              <div class="relative">
                <select v-model="form.employee_id" 
                  class="border border-slate-200 bg-white rounded-xl px-3 py-2.5 text-xs font-medium w-full outline-none transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 appearance-none h-10 shadow-sm cursor-pointer"
                  :class="formErrors.employee_id ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-50' : ''">
                  <option value="">Sélectionner un profil...</option>
                  <optgroup label="🟢 Employés Libres" v-if="freeEmployees.length"></optgroup>
                  <option v-for="e in freeEmployees" :key="e.id" :value="e.id">
                    &nbsp;&nbsp;{{ fullName(e) }}
                  </option>
                  <optgroup label="🟡 Occupés (Autre projet)" v-if="busyEmployees.length"></optgroup>
                  <option v-for="e in busyEmployees" :key="e.id" :value="e.id">
                    &nbsp;&nbsp;{{ fullName(e) }}
                  </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                  <i class="fas fa-chevron-down text-[10px]"></i>
                </div>
              </div>
              <p v-if="formErrors.employee_id" class="text-rose-500 text-[11px] font-medium mt-0.5 flex items-center gap-1">
                <span>⚠️</span> {{ formErrors.employee_id[0] }}
              </p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-bold text-slate-600">Rôle au projet</label>
              <input
                v-model="form.role_in_project"
                type="text"
                placeholder="Ex: Lead Developer"
                class="border border-slate-200 bg-white rounded-xl px-3 py-2.5 text-xs font-medium w-full outline-none transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-10 shadow-sm"
              />
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-bold text-slate-600">Date de Début</label>
              <input type="date" v-model="form.start_date" class="border border-slate-200 bg-white rounded-xl px-3 py-2.5 text-xs font-medium w-full outline-none transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-10 shadow-sm cursor-pointer" />
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-bold text-slate-600">Date de Fin</label>
              <input type="date" v-model="form.end_date" class="border border-slate-200 bg-white rounded-xl px-3 py-2.5 text-xs font-medium w-full outline-none transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-10 shadow-sm cursor-pointer" />
            </div>
          </div>

          <div v-if="selectedBusyEmployee" class="bg-amber-50/60 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-xs flex items-center gap-2.5 font-medium animate-[fadeIn_0.2s_ease-out]">
            <i class="fas fa-triangle-exclamation text-amber-500 text-sm"></i>
            <span>Attention: <strong>{{ fullName(selectedBusyEmployee) }}</strong> collabore déjà sur un autre projet actif à ces dates.</span>
          </div>

          <div class="flex justify-end pt-2 border-t border-slate-50">
            <button
              @click="storeAssignment"
              :disabled="submitting"
              class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 disabled:opacity-40 transition-all active:scale-[0.99] flex items-center justify-center gap-2"
            >
              <i class="fas" :class="submitting ? 'fa-circle-notch animate-spin' : 'fa-circle-check'"></i>
              <span>{{ submitting ? 'Enregistrement...' : 'Confirmer Allocation' }}</span>
            </button>
          </div>
        </div>

        <div class="space-y-4">
          <div class="px-1 flex items-center justify-between">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
              <i class="fas fa-user-group text-slate-400"></i> Membres Assignés ({{ assignments.length }})
            </h2>
          </div>

          <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="n in 3" :key="n" class="bg-white border border-slate-200/60 p-5 rounded-2xl animate-pulse flex items-center gap-4 shadow-sm">
              <div class="w-11 h-11 bg-slate-100 rounded-xl shrink-0"></div>
              <div class="space-y-2.5 flex-1">
                <div class="h-3.5 bg-slate-100 rounded-lg w-2/3"></div>
                <div class="h-2.5 bg-slate-50 rounded-md w-1/2"></div>
              </div>
            </div>
          </div>

          <div v-else-if="assignments.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="a in assignments"
              :key="a.id"
              class="bg-white border border-slate-200/80 hover:border-indigo-200 hover:shadow-[0_8px_24px_rgba(79,70,229,0.04)] rounded-2xl p-4 flex items-start justify-between gap-3 transition-all group relative"
            >
              <router-link 
                :to="`/getUser/${a.employee?.id || a.employee?._id}`" 
                class="flex items-center gap-3.5 overflow-hidden flex-1 group/link cursor-pointer"
              >
                <div class="w-11 h-11 rounded-xl flex items-center justify-center font-bold text-xs uppercase tracking-wider shrink-0 bg-gradient-to-br shadow-inner overflow-hidden border border-slate-100 group-hover/link:scale-[1.03] transition-transform"
                     :class="a.employee?.image ? '' : ganttColor(a.employee_id)">
                  <img 
                    v-if="a.employee?.image" 
                    :src="a.employee.image" 
                    alt="Avatar" 
                    class="w-full h-full object-cover"
                  />
                  <span v-else>
                    {{ avatarInitials(fullName(a.employee)) }}
                  </span>
                </div>

                <div class="overflow-hidden flex-1">
                  <h4 class="font-bold text-slate-800 text-sm truncate group-hover/link:text-indigo-600 transition-colors" :title="fullName(a.employee)">
                    {{ fullName(a.employee) }}
                  </h4>
                  <p class="text-xs text-slate-400 font-semibold truncate mt-0.5">
                    {{ a.role_in_project || 'Collaborateur' }}
                  </p>
                  <div class="mt-2.5 text-[10px] font-bold text-slate-500 flex items-center gap-1">
                    <span class="bg-slate-50 border border-slate-200/60 px-1.5 py-0.5 rounded text-slate-600">{{ formatDate(a.start_date) }}</span>
                    <i class="fas fa-arrow-right text-[9px] text-slate-300 px-0.5"></i>
                    <span class="bg-slate-50 border border-slate-200/60 px-1.5 py-0.5 rounded text-slate-500">{{ formatDate(a.end_date) }}</span>
                  </div>
                </div>
              </router-link>

              <button
                @click="deleteAssignment(a.id)"
                :disabled="deletingId === a.id"
                class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-100 rounded-xl transition-all shrink-0 active:scale-90 relative z-20 self-center"
                title="Supprimer l'affectation"
              >
                <i class="fas" :class="deletingId === a.id ? 'fa-spinner animate-spin text-rose-600' : 'fa-trash-can text-xs'"></i>
              </button>
            </div>
          </div>

          <div v-else class="bg-white border border-slate-200/80 rounded-2xl p-12 text-center shadow-sm">
            <div class="w-12 h-12 bg-slate-50 border border-slate-200/60 rounded-xl flex items-center justify-center mx-auto mb-3 text-slate-400">
              <i class="fas fa-user-slash text-sm"></i>
            </div>
            <p class="text-xs font-bold text-slate-800 uppercase tracking-wide">Aucun membre assigné</p>
            <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">Le panel d'allocation est vide. Cliquez sur "Assigner Membre" pour intégrer du personnel.</p>
          </div>
        </div>

      </div>
    </main>
  </div>
</template>

<style scoped>
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>