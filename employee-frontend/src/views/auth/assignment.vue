<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/api/axios"; 
import assignmentService from "@/services/assignmentService"; 

const router = useRouter();

const loading = ref(false);
const unreadCount = ref(0);

const assignmentsData = ref({
  total: 0,
  current: [],
  upcoming: [],
  past: [],
  employee: null
});

// Modal State
const isModalOpen = ref(false);
const selectedAssignment = ref(null);
const projectEmployees = ref([]);
const loadingEmployees = ref(false);

// Fonction bach t-jbd les employes li m-assigniyin l dak l-projet ( mn service/api )
const fetchProjectTeam = async (projectId) => {
  loadingEmployees.value = true;
  try {
    // T9der t-bdel '/projects/' + projectId + '/employees' b endpoint dialek f backend
    const response = await api.get(`/projects/${projectId}/employees`);
    projectEmployees.value = response.data.data || response.data;
  } catch (err) {
    console.error("Erreur f jalb a3da' al mashro3:", err);
    projectEmployees.value = [];
  } finally {
    loadingEmployees.value = false;
  }
};

const openDetailsModal = async (assignment) => {
  selectedAssignment.value = assignment;
  isModalOpen.value = true;
  
  // N-3ayto l-fonction bch t-jbd l-a3da'
  if (assignment.project_id) {
    await fetchProjectTeam(assignment.project_id);
  }
};

const closeDetailsModal = () => {
  isModalOpen.value = false;
  selectedAssignment.value = null;
  projectEmployees.value = [];
};

const isDark = ref(localStorage.getItem("theme") !== "light");

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
};

const formatDate = (dateString) => {
  if (!dateString) return "—";
  return new Date(dateString).toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

const getAssignments = async () => {
  loading.value = true;
  try {
    const res = await assignmentService.getMyAssignments();
    console.log("Backend Response:", res.data);
    
    const responseData = res.data.data || res.data;
    const employeeData = responseData.employee || null;
    const totalCount = responseData.total || 0;
    const rawAssignments = responseData.assignments || [];

    const now = new Date();
    const currentList = [];
    const upcomingList = [];
    const pastList = [];

    rawAssignments.forEach(assignment => {
      const startDate = assignment.start_date ? new Date(assignment.start_date) : null;
      const endDate = assignment.end_date ? new Date(assignment.end_date) : null;

      if (startDate && startDate > now) {
        upcomingList.push(assignment);
      } else if (endDate && endDate < now) {
        pastList.push(assignment);
      } else {
        currentList.push(assignment);
      }
    });

    assignmentsData.value = {
      total: totalCount > 0 ? totalCount : rawAssignments.length,
      current: currentList,
      upcoming: upcomingList,
      past: pastList,
      employee: employeeData
    };

  } catch (err) {
    console.error("Erreur API:", err.response?.data || err);
  } finally {
    loading.value = false;
  }
};

const loadUnreadCount = async () => {
  try {
    const res = await api.get("/notifications", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
    });
    const notifications = res.data.notifications || [];
    unreadCount.value = notifications.filter(n => !n.is_read).length;
  } catch (err) {
    console.log(err);
  }
};

onMounted(async () => {
  await getAssignments();
  await loadUnreadCount();
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div :class="isDark ? 'bg-slate-950 text-slate-400' : 'bg-slate-50 text-slate-600'" class="min-h-screen font-sans antialiased w-full flex flex-col selection:bg-indigo-500/30 selection:text-white transition-colors duration-300">

    <nav :class="isDark ? 'bg-slate-950/40 border-slate-900/80 text-slate-200' : 'bg-white/60 border-slate-200 text-slate-800'" class="backdrop-blur-xl px-8 py-4 flex justify-between items-center shadow-2xl border-b shrink-0 sticky top-0 z-50 w-full transition-colors duration-300">
      <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500 via-indigo-500 to-purple-500 flex items-center justify-center text-white text-xl font-black shadow-[0_4px_12px_rgba(56,189,248,0.3)] italic">
          D
        </div>
        <div>
          <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-black tracking-wider uppercase italic leading-none">DataXpress</h1>
          <p class="text-[10px] text-sky-500 dark:text-sky-400 font-bold tracking-widest uppercase mt-1">Workspace Hub</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <div :class="isDark ? 'bg-slate-900/40 border-slate-800/60' : 'bg-slate-100 border-slate-200'" class="flex items-center gap-1 p-1.5 rounded-xl border">
          <button @click="router.push('/notifications')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="relative px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-bell text-sm opacity-60"></i> <span class="hidden md:inline">Notifications</span>
            <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border border-white dark:border-slate-950">
              {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
          </button>
          
          <button @click="router.push('/absences')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fa-regular fa-calendar-minus text-sm opacity-60"></i> <span class="hidden md:inline">Absences</span>
          </button>
          
          <button class="px-4 py-2.5 rounded-lg text-xs font-black bg-sky-600 text-white shadow-[0_4px_12px_rgba(2,132,199,0.25)] flex items-center gap-2">
            <i class="fas fa-folder text-sm"></i> <span class="hidden md:inline">Assignement</span>
          </button>

         <button @click="router.push('/projects')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fa-regular fa-calendar-minus text-sm opacity-60"></i> <span class="hidden md:inline">Projects</span>
          </button>
          
          <button @click="router.push('/profile')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-user text-sm opacity-60"></i> <span class="hidden md:inline">Mon Profile</span>
          </button>
        </div>

        <button @click="toggleTheme" :class="isDark ? 'bg-slate-900 text-amber-400 border-slate-800' : 'bg-white text-sky-600 border-slate-200 shadow-sm'" class="w-10 h-10 rounded-xl border flex items-center justify-center transition-all active:scale-95 text-sm">
          <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'"></i>
        </button>
      </div>
    </nav>

    <main class="flex-1 w-full flex flex-col justify-start relative overflow-hidden p-8 md:p-12">
      <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-sky-600/[0.06] blur-[160px] rounded-full pointer-events-none"></div>
      <div class="absolute bottom-10 right-10 w-[500px] h-[500px] bg-indigo-600/[0.03] blur-[150px] rounded-full pointer-events-none"></div>

      <div v-if="loading" class="flex flex-col items-center justify-center min-h-[60vh] relative z-10">
        <div class="w-9 h-9 border-2 border-sky-600/20 border-t-sky-600 rounded-full animate-spin"></div>
        <p class="text-[10px] font-bold text-slate-500 mt-4 uppercase tracking-widest">Chargement des affectations...</p>
      </div>

      <div v-else class="max-w-7xl mx-auto w-full relative z-10 space-y-12">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b pb-6" :class="isDark ? 'border-slate-800/60' : 'border-slate-200'">
          <div>
            <h2 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-3xl font-black tracking-wide uppercase italic">Mes Affectations</h2>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-2">
              Vous êtes impliqué dans <span class="text-sky-500">{{ assignmentsData.total }}</span> projet(s) au total
            </p>
          </div>
        </div>

        <section v-if="assignmentsData.current?.length > 0" class="space-y-6">
          <div class="flex items-center gap-3">
            <div class="w-2.5 h-2.5 rounded-full bg-sky-500 animate-pulse shadow-[0_0_10px_rgba(14,165,233,0.5)]"></div>
            <h3 :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-xs font-black uppercase tracking-widest">Projets En Cours</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="assignment in assignmentsData.current" :key="assignment.id" @click="openDetailsModal(assignment)" :class="isDark ? 'bg-slate-900/40 border-sky-900/30 hover:border-sky-500/50' : 'bg-white border-slate-200 hover:border-sky-400 shadow-sm'" class="p-6 rounded-2xl border transition-all duration-300 group backdrop-blur-sm relative overflow-hidden flex flex-col h-full cursor-pointer">
              <div class="absolute -top-10 -right-10 w-32 h-32 bg-sky-500/10 blur-3xl rounded-full group-hover:bg-sky-500/20 transition-all"></div>
              
              <div class="flex justify-between items-start mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-lg shadow-lg">
                  <i class="fas fa-layer-group"></i>
                </div>
                <span class="text-[9px] font-black bg-sky-500/10 text-sky-500 px-2.5 py-1 rounded-md uppercase tracking-widest border border-sky-500/20">
                  Active
                </span>
              </div>
              
              <div class="relative z-10 flex-1 flex flex-col">
                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1">Détails du Projet</span>
                <h4 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-bold uppercase tracking-wide mb-1">{{ assignment.project?.name }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mb-3 font-medium">{{ assignment.project?.description || 'Aucune description disponible.' }}</p>
                
                <div v-if="assignment.project?.end_date || assignment.project?.deadline" class="flex items-center gap-1.5 mt-auto text-[9px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800/50 w-fit px-2 py-1 rounded border dark:border-slate-700/50">
                  <i class="fas fa-flag-checkered text-slate-500"></i>
                  <span>Fin Prévue: {{ formatDate(assignment.project?.end_date || assignment.project?.deadline) }}</span>
                </div>
              </div>

              <div class="pt-4 border-t mt-5 relative z-10" :class="isDark ? 'border-slate-800/80' : 'border-slate-100'">
                <span class="text-[8px] font-bold text-sky-500 dark:text-sky-400 uppercase tracking-widest mb-2 block">Mon Affectation</span>
                <div class="flex justify-between items-center mb-3">
                  <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Rôle</span>
                  <span :class="isDark ? 'text-slate-200 bg-slate-800' : 'text-slate-800 bg-slate-100'" class="text-xs font-black px-2 py-1 rounded">{{ assignment.role_in_project || 'Développeur' }}</span>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-mono text-slate-500 dark:text-slate-400">
                  <i class="fas fa-user-clock"></i>
                  <span>{{ formatDate(assignment.start_date) }}</span>
                  <i class="fas fa-arrow-right text-[8px]"></i>
                  <span :class="{'text-sky-500': !assignment.end_date}">{{ assignment.end_date ? formatDate(assignment.end_date) : 'En cours' }}</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section v-if="assignmentsData.upcoming?.length > 0" class="space-y-6">
          <div class="flex items-center gap-3">
            <div class="w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
            <h3 :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-xs font-black uppercase tracking-widest">Affectations À Venir</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="assignment in assignmentsData.upcoming" :key="assignment.id" @click="openDetailsModal(assignment)" :class="isDark ? 'bg-slate-900/20 border-slate-800/60 hover:border-indigo-500/30' : 'bg-white border-slate-200 hover:border-indigo-300 shadow-sm'" class="p-6 rounded-2xl border transition-all duration-300 flex flex-col h-full cursor-pointer">
              
              <div class="flex justify-between items-start mb-4">
                <div :class="isDark ? 'bg-slate-800 text-slate-400' : 'bg-slate-100 text-slate-500 border border-slate-200'" class="w-12 h-12 rounded-xl flex items-center justify-center text-lg">
                  <i class="fas fa-hourglass-start"></i>
                </div>
                <span class="text-[9px] font-black bg-indigo-500/10 text-indigo-500 px-2.5 py-1 rounded-md uppercase tracking-widest border border-indigo-500/20">
                  Planifié
                </span>
              </div>
              
              <div class="flex-1 mb-4">
                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Détails du Projet</span>
                <h4 :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="text-base font-bold uppercase tracking-wide mb-1">{{ assignment.project?.name }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 font-medium">{{ assignment.project?.description || 'Aucune description disponible.' }}</p>
              </div>

              <div class="pt-4 border-t mt-auto" :class="isDark ? 'border-slate-800/50' : 'border-slate-200'">
                <span class="text-[8px] font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-widest mb-2 block">Mon Affectation Prévue</span>
                <div class="flex justify-between items-center mb-2">
                  <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Rôle Prévu</span>
                  <span :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold">{{ assignment.role_in_project || 'Non défini' }}</span>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-mono text-slate-500 dark:text-slate-400">
                  <i class="fas fa-calendar-plus"></i>
                  <span>Début: {{ formatDate(assignment.start_date) }}</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section v-if="assignmentsData.past?.length > 0" class="space-y-6">
          <div class="flex items-center gap-3">
            <div class="w-2.5 h-2.5 rounded-full bg-slate-600 dark:bg-slate-500"></div>
            <h3 :class="isDark ? 'text-slate-400' : 'text-slate-500'" class="text-xs font-black uppercase tracking-widest">Projets Terminés</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="assignment in assignmentsData.past" :key="assignment.id" @click="openDetailsModal(assignment)" :class="isDark ? 'bg-slate-950 border-slate-900' : 'bg-white border-slate-200 shadow-sm'" class="p-4 rounded-xl border flex flex-col justify-between opacity-90 hover:opacity-100 transition-opacity gap-4 cursor-pointer">
              
              <div class="flex items-center gap-4">
                <div :class="isDark ? 'bg-slate-900 text-slate-600' : 'bg-slate-100 text-slate-400 border border-slate-200'" class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0">
                  <i class="fas fa-check-double"></i>
                </div>
                <div>
                  <h4 :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-black uppercase tracking-wider">{{ assignment.project?.name }}</h4>
                  <span class="text-[9px] font-bold text-slate-400 bg-slate-200 dark:bg-slate-800/50 px-2 py-0.5 rounded mt-1 inline-block border dark:border-slate-800">Terminé</span>
                </div>
              </div>
              
              <div class="pt-3 border-t flex justify-between items-center" :class="isDark ? 'border-slate-800/50' : 'border-slate-100'">
                <div class="flex flex-col">
                  <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Mon Rôle</span>
                  <span :class="isDark ? 'text-slate-500' : 'text-slate-700'" class="text-[10px] font-bold">
                     {{ assignment.role_in_project || 'Développeur' }}
                  </span>
                </div>
                <div class="text-right">
                  <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Période</span>
                  <p class="text-[9px] font-mono text-slate-500">{{ formatDate(assignment.start_date) }} - {{ formatDate(assignment.end_date) }}</p>
                </div>
              </div>

            </div>
          </div>
        </section>

        <div v-if="assignmentsData.total === 0" :class="isDark ? 'bg-slate-900/30 border-slate-800/60' : 'bg-slate-50 border-slate-200'" class="text-center py-24 flex-1 flex flex-col items-center justify-center rounded-3xl border backdrop-blur-sm">
          <div :class="isDark ? 'bg-slate-950 border-slate-800' : 'bg-white border-slate-200'" class="w-16 h-16 rounded-2xl border flex items-center justify-center text-indigo-500 mb-5 text-2xl shadow-sm">
            <i class="fas fa-folder-open"></i>
          </div>
          <h3 :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-base font-black uppercase tracking-wider">Aucune Affectation</h3>
          <p class="text-xs text-slate-400 dark:text-slate-500 max-w-sm mx-auto mt-2 font-medium leading-relaxed">Vous n'êtes actuellement assigné à aucun projet. Veuillez contacter votre chef de projet si cela est une erreur.</p>
        </div>

      </div>
    </main>
  </div>

  <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="closeDetailsModal"></div>

    <div :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-300' : 'bg-white border-slate-200 text-slate-700 shadow-2xl'" class="relative w-full max-w-2xl rounded-2xl border p-6 max-h-[85vh] overflow-y-auto z-10">
      
      <button @click="closeDetailsModal" class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
        <i class="fas fa-times"></i>
      </button>

      <div class="border-b pb-4 mb-5" :class="isDark ? 'border-slate-800' : 'border-slate-100'">
        <span class="text-[9px] font-black uppercase tracking-widest text-sky-500">Détails du Projet & Équipe</span>
        <h3 class="text-xl font-black mt-1 uppercase" :class="isDark ? 'text-white' : 'text-slate-900'">
          {{ selectedAssignment?.project?.name }}
        </h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div :class="isDark ? 'bg-slate-800/50' : 'bg-slate-50'" class="p-4 rounded-xl">
          <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block mb-1">Description</span>
          <p class="text-xs font-medium leading-relaxed">
            {{ selectedAssignment?.project?.description || 'Aucune description disponible pour ce projet.' }}
          </p>
        </div>

        <div :class="isDark ? 'bg-slate-800/50' : 'bg-slate-50'" class="p-4 rounded-xl flex flex-col justify-between gap-3">
          <div>
            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Mon Rôle</span>
            <span class="text-xs font-black mt-1 inline-block px-2 py-0.5 rounded" :class="isDark ? 'bg-slate-950 text-slate-200' : 'bg-slate-200 text-slate-800'">
              {{ selectedAssignment?.role_in_project || 'Développeur' }}
            </span>
          </div>
          <div>
            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block">Durée</span>
            <span class="text-[10px] font-mono font-bold">
              {{ formatDate(selectedAssignment?.start_date) }} → {{ selectedAssignment?.end_date ? formatDate(selectedAssignment?.end_date) : 'En cours' }}
            </span>
          </div>
        </div>
      </div>

      <div>
        <span class="text-xs font-black uppercase tracking-widest block mb-4 border-b pb-2" :class="isDark ? 'border-slate-800 text-slate-200' : 'border-slate-100 text-slate-800'">
          <i class="fas fa-users mr-2 text-sky-500"></i> Membres de l'équipe (Employés)
        </span>
        
        <div v-if="loadingEmployees" class="flex justify-center py-6">
           <div class="w-6 h-6 border-2 border-sky-600/20 border-t-sky-600 rounded-full animate-spin"></div>
        </div>

        <div v-else-if="projectEmployees && projectEmployees.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div v-for="emp in projectEmployees" :key="emp.id" :class="isDark ? 'bg-slate-800/30 border-slate-700/40' : 'bg-slate-50 border-slate-100'" class="p-3 rounded-xl border flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-sky-500/10 text-sky-500 flex items-center justify-center font-black text-sm shrink-0">
                <router-link 
                    :to="`/getUser/${emp.id || emp._id}`" 
                    class="flex items-center gap-3 group cursor-pointer">
                <i class="fas fa-user"></i>
                </router-link>
            </div>
            <div>
              <h5 class="text-xs font-bold uppercase" :class="isDark ? 'text-slate-200' : 'text-slate-800'">{{ emp.firstname }} {{ emp.lastname }}</h5>
              <span class="text-[9px] font-medium text-slate-500 dark:text-slate-400 block">{{ emp.email }}</span>
              <span v-if="emp.role_in_project || emp.pivot?.role_in_project" class="text-[8px] font-black bg-sky-500/10 text-sky-500 px-1.5 py-0.5 rounded mt-1 inline-block uppercase">
                {{ emp.role_in_project || emp.pivot?.role_in_project }}
              </span>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-6 text-[10px] font-bold text-slate-400 dark:text-slate-500" :class="isDark ? 'bg-slate-800/20' : 'bg-slate-50'">
          <i class="fas fa-info-circle block mb-2 text-base"></i> Aucun membre trouvé dans l'équipe pour ce projet.
        </div>
      </div>

    </div>
  </div>
</template>