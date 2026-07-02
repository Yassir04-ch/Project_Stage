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
const mobileMenuOpen = ref(false)

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

  <div class="min-h-screen bg-slate-50 text-slate-700 font-sans antialiased relative selection:bg-indigo-100 selection:text-indigo-900">
    
    <!-- Background Decoration -->
    <div class="absolute top-0 inset-x-0 h-64 bg-gradient-to-b from-indigo-100/40 to-transparent pointer-events-none -z-10"></div>

    <main class="p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto z-10 relative">
      
      <!-- Top Navigation Action -->
      <div class="mb-8">
        <button 
          @click="$router.back()" 
          class="inline-flex items-center gap-2 px-3 py-2 -ml-3 text-sm font-semibold text-slate-500 hover:text-indigo-600 hover:bg-white hover:shadow-sm rounded-lg transition-all group"
        >
          <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
          <span>Retour aux projets</span>
        </button>
      </div>

      <div class="space-y-6">
        
        <!-- Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl shrink-0">
              <i class="fas fa-briefcase"></i>
            </div>
            <div>
              <h1 class="font-extrabold text-xl sm:text-2xl text-slate-900 tracking-tight">
                {{ project.name || 'Allocation des Ressources' }}
              </h1>
              <div class="flex items-center gap-3 mt-1.5">
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500">
                  <i class="far fa-calendar text-slate-400"></i>
                  {{ formatDate(project.start_date) }}
                </span>
                <i class="fas fa-arrow-right text-[10px] text-slate-300"></i>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500">
                  <i class="fas fa-flag-checkered text-slate-400"></i>
                  {{ formatDate(project.end_date) }}
                </span>
              </div>
            </div>
          </div>

          <button
            @click="showForm = !showForm"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold transition-all active:scale-95 shrink-0 border"
            :class="showForm 
              ? 'bg-white hover:bg-slate-50 text-slate-700 border-slate-300' 
              : 'bg-indigo-600 hover:bg-indigo-700 text-white border-indigo-600 shadow-md shadow-indigo-600/20'"
          >
            <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'"></i>
            <span>{{ showForm ? 'Fermer' : 'Assigner un Collaborateur' }}</span>
          </button>
        </div>

        <!-- Alerts -->
        <Transition 
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="opacity-0 -translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-2"
        >
          <div
            v-if="flashMsg"
            class="p-4 rounded-xl border flex items-center gap-3 text-sm font-semibold shadow-sm"
            :class="flashType === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200'"
          >
            <i class="fas" :class="flashType === 'success' ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-rose-500'"></i>
            <p>{{ flashMsg }}</p>
          </div>
        </Transition>

        <!-- Form Panel -->
        <Transition
          enter-active-class="transition duration-300 ease-out origin-top"
          enter-from-class="opacity-0 scale-y-95 -translate-y-2"
          enter-to-class="opacity-100 scale-y-100 translate-y-0"
          leave-active-class="transition duration-200 ease-in origin-top"
          leave-from-class="opacity-100 scale-y-100 translate-y-0"
          leave-to-class="opacity-0 scale-y-95 -translate-y-2"
        >
          <div v-if="showForm" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="pb-3 border-b border-slate-100">
              <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 bg-indigo-600 rounded-full"></span>
                Paramètres d'assignation
              </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <!-- Select Employee -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Employé ciblé</label>
                <div class="relative">
                  <select v-model="form.employee_id" 
                    class="border border-slate-200 bg-slate-50 rounded-lg px-3 py-2.5 text-sm font-medium w-full outline-none transition-all focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 appearance-none cursor-pointer"
                    :class="formErrors.employee_id ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-50' : ''">
                    <option value="" disabled>Sélectionner un profil...</option>
                    <optgroup label="🟢 Disponibles" v-if="freeEmployees.length"></optgroup>
                    <option v-for="e in freeEmployees" :key="e.id" :value="e.id">
                      {{ fullName(e) }}
                    </option>
                    <optgroup label="🟡 Déjà assignés" v-if="busyEmployees.length"></optgroup>
                    <option v-for="e in busyEmployees" :key="e.id" :value="e.id">
                      {{ fullName(e) }}
                    </option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <i class="fas fa-chevron-down text-[10px]"></i>
                  </div>
                </div>
                <p v-if="formErrors.employee_id" class="text-rose-500 text-xs font-medium mt-1 flex items-center gap-1">
                  <i class="fas fa-exclamation-triangle text-[10px]"></i> {{ formErrors.employee_id[0] }}
                </p>
              </div>

              <!-- Role -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Rôle au projet</label>
                <input
                  v-model="form.role_in_project"
                  type="text"
                  placeholder="Ex: Lead Developer"
                  class="border border-slate-200 bg-slate-50 rounded-lg px-3 py-2.5 text-sm font-medium w-full outline-none transition-all focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50"
                />
              </div>

              <!-- Start Date -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Date de Début</label>
                <input type="date" v-model="form.start_date" class="border border-slate-200 bg-slate-50 rounded-lg px-3 py-2.5 text-sm font-medium w-full outline-none transition-all focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 cursor-pointer" />
              </div>

              <!-- End Date -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Date de Fin</label>
                <input type="date" v-model="form.end_date" class="border border-slate-200 bg-slate-50 rounded-lg px-3 py-2.5 text-sm font-medium w-full outline-none transition-all focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 cursor-pointer" />
              </div>
            </div>

            <!-- Warning Alert -->
            <Transition
              enter-active-class="transition duration-300 ease-out"
              enter-from-class="opacity-0 -translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-1"
            >
              <div v-if="selectedBusyEmployee" class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg p-4 text-sm flex items-start sm:items-center gap-3 font-medium">
                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 sm:mt-0"></i>
                <p>Attention : <strong>{{ fullName(selectedBusyEmployee) }}</strong> collabore déjà sur un autre projet à ces dates.</p>
              </div>
            </Transition>

            <div class="flex justify-end pt-4 border-t border-slate-100">
              <button
                @click="storeAssignment"
                :disabled="submitting"
                class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-sm disabled:opacity-50 transition-all active:scale-95 flex items-center justify-center gap-2"
              >
                <i class="fas" :class="submitting ? 'fa-circle-notch animate-spin' : 'fa-check'"></i>
                <span>{{ submitting ? 'Enregistrement...' : 'Confirmer l\'Allocation' }}</span>
              </button>
            </div>
          </div>
        </Transition>

        <!-- Assignments Section -->
        <div class="space-y-4 pt-2">
          <div class="flex items-center justify-between pb-2 border-b border-slate-200">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
              Équipe du projet
              <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md text-xs font-bold border border-slate-200">{{ assignments.length }}</span>
            </h2>
          </div>

          <!-- Loading Skeleton -->
          <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="n in 3" :key="n" class="bg-white border border-slate-200 p-5 rounded-xl animate-pulse">
              <div class="flex gap-4">
                <div class="w-10 h-10 bg-slate-100 rounded-full shrink-0"></div>
                <div class="space-y-2 flex-1 pt-1">
                  <div class="h-4 bg-slate-100 rounded w-2/3"></div>
                  <div class="h-3 bg-slate-50 rounded w-1/3"></div>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t border-slate-50 h-3 bg-slate-50 rounded w-full"></div>
            </div>
          </div>

          <!-- Professional Grid Cards -->
          <div v-else-if="assignments.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="a in assignments"
              :key="a.id"
              class="bg-white border border-slate-200 hover:border-indigo-300 rounded-xl p-5 transition-all duration-200 hover:shadow-md group flex flex-col h-full relative"
            >
              <!-- Top: Info & Delete -->
              <div class="flex justify-between items-start mb-4">
                <router-link :to="`/getUser/${a.employee?.id || a.employee?._id}`" class="flex items-center gap-3 overflow-hidden group/link">
                  <!-- Avatar -->
                  <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs uppercase shrink-0 bg-gradient-to-br shadow-inner overflow-hidden text-white"
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
                  
                  <!-- Name & Role Badge -->
                  <div class="overflow-hidden">
                    <h4 class="font-bold text-slate-800 text-sm truncate group-hover/link:text-indigo-600 transition-colors" :title="fullName(a.employee)">
                      {{ fullName(a.employee) }}
                    </h4>
                    <span class="inline-block mt-0.5 px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded text-[10px] font-bold tracking-wide uppercase truncate max-w-full">
                      {{ a.role_in_project || 'Collaborateur' }}
                    </span>
                  </div>
                </router-link>

                <!-- Delete Action -->
                <button
                  @click="deleteAssignment(a.id)"
                  :disabled="deletingId === a.id"
                  class="text-slate-300 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition-colors active:scale-95 disabled:opacity-50"
                  title="Retirer l'employé"
                >
                  <i class="fas" :class="deletingId === a.id ? 'fa-spinner animate-spin text-rose-600' : 'fa-trash-alt text-sm'"></i>
                </button>
              </div>

              <!-- Bottom: Dates Timeline -->
              <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
                <div class="flex items-center gap-1.5" title="Date de début">
                  <i class="far fa-calendar text-slate-400"></i>
                  <span>{{ formatDate(a.start_date) }}</span>
                </div>
                <div class="h-px flex-1 bg-slate-100 mx-3"></div>
                <div class="flex items-center gap-1.5" title="Date de fin">
                  <i class="fas fa-flag-checkered text-slate-400"></i>
                  <span>{{ formatDate(a.end_date) }}</span>
                </div>
              </div>
            </div>
          </div>

           <div v-else class="bg-white border border-slate-200 border-dashed rounded-xl p-12 text-center">
            <div class="w-14 h-14 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
              <i class="fas fa-users-slash text-xl"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800 mb-1">Aucun collaborateur assigné</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">L'équipe de ce projet est actuellement vide. Utilisez le bouton ci-dessus pour ajouter des membres.</p>
          </div>
        </div>

      </div>
    </main>
  </div>
</template>