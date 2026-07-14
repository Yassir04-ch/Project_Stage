<script setup>
import { onMounted, onUnmounted, ref, computed } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const router = useRouter();

const users = ref([]);
const projects = ref([]);
const services = ref([]);
const loading = ref(false);

const currentUser = ref(null);
const unreadCount = ref(0);

const fetchData = async () => {
  loading.value = true;
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem("token")}` };
    
    const [resUsers, resProjects, resServices, resNotifs] = await Promise.all([
      api.get("/users", { headers }),
      api.get("/projects", { headers }),
      api.get("/services", { headers }),
      api.get("/notifications", { headers }).catch(() => ({ data: { notifications: [] } })) 
    ]);

    users.value = resUsers.data.users || [];
    currentUser.value = resUsers.data.admin || null;
    projects.value = resProjects.data.projects || [];
    services.value = resServices.data.services || [];
    unreadCount.value = (resNotifs.data.notifications || []).filter(n => !n.is_read).length;
    
  } catch (err) {
    console.error("Error loading dashboard:", err);
  } finally {
    loading.value = false;
  }
};

const listenToNotifications = () => {
  const userId = currentUser.value?.id; 
  if (!userId || !window.Echo) return;

  window.Echo.private(`notifications.${userId}`)
    .listen('.notification.new', () => {
      unreadCount.value++;
    });
};

const stopListening = () => {
  const userId = currentUser.value?.id;
  if (userId && window.Echo) {
    window.Echo.leave(`notifications.${userId}`);
  }
};

const canAccessSkills = computed(() =>
  ["Administrateur"].includes(currentUser.value?.role?.name)
);

const stats = computed(() => ({
  totalUsers: users.value.length,
  totalProjects: projects.value.length,
  totalServices: services.value.length,
  totalRevenue: projects.value.reduce((acc, p) => acc + (Number(p.budget) || 0), 0)
}));

const roleStats = computed(() => {
  const admins = users.value.filter(u => u.role?.name === 'Administrateur').length;
  const managers = users.value.filter(u => u.role?.name === 'Manager').length;
  const rh = users.value.filter(u => u.role?.name === 'Ressources Humaines').length;
  const employes = users.value.filter(u => u.role?.name === 'Employé').length;
  return { admins, managers, rh, employes };
});

onMounted(() => {
  fetchData().then(() => listenToNotifications());
});

onUnmounted(() => {
  stopListening();
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 flex antialiased font-sans">

    <aside class="bg-slate-950 text-slate-200 w-72 min-h-screen p-6 flex flex-col justify-between shadow-2xl shrink-0 border-r border-slate-800/40 z-20">
      
      <div class="flex flex-col">
        <div class="flex items-center gap-3.5 mb-10 pb-4 border-b border-slate-900">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 text-blue-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-indigo-500/20">
              D
          </div> 
          <div>
            <h1 class="text-lg font-black tracking-tight text-white uppercase italic">DATAXPRESS</h1>
            <p class="text-xs text-indigo-400 font-bold tracking-wider uppercase">Workspace Admin</p>
          </div>
        </div>

        <div class="space-y-2">
          <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Workspace</p>
          <nav class="space-y-1.5">
            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 group transition-all text-sm text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">🏠</span>
              <span>Dashboard</span>
            </button>

            <button v-if="canAccessSkills" @click="router.push('/projects')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">📁</span>
              <span>Projects</span>
            </button>

            <button v-if="canAccessSkills" @click="router.push('/offres')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fas fa-briefcase text-base group-hover:scale-110 transition-transform"></i>
              <span>Offres d'emploi</span>
            </button>

            <button @click="router.push('/users')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">👥</span>
              <span>Utilisateurs</span>
            </button>

            <button @click="router.push('/absences')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fa-regular fa-calendar-minus text-base w-5 group-hover:scale-110 transition-transform"></i>
              <span>Absences</span>
            </button>

            <button @click="router.push('/skills')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fa-solid fa-brain text-base w-5 group-hover:scale-110 transition-transform"></i>
              <span>Compétences</span>
            </button>
            
             <button v-if="canAccessSkills" @click="router.push('/services')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fas fa-building text-base w-5 group-hover:scale-110 transition-transform"></i>
              <span>Services</span>
            </button>
          </nav>
        </div>
      </div>

      <div class="mt-auto pt-4 border-t border-slate-900">
        <router-link
          to="/profile"
          class="bg-slate-900/80 border border-slate-800/40 rounded-xl p-3.5 flex items-center gap-3 shadow-inner hover:bg-slate-900 hover:border-slate-700/60 transition-all group cursor-pointer w-full"
        >
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center font-black text-white text-sm uppercase tracking-wide border border-indigo-400/20 shrink-0">
            <span v-if="currentUser">{{ currentUser.firstname?.[0] }}{{ currentUser.lastname?.[0] }}</span>
            <span v-else class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
          </div>

          <div class="flex-1 min-w-0">
            <div v-if="currentUser">
              <h2 class="text-sm font-bold text-white tracking-tight truncate uppercase group-hover:text-indigo-400 transition-colors">
                {{ currentUser.firstname }} {{ currentUser.lastname }}
              </h2>
              <p class="text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-0.5 truncate">
                {{ currentUser.role?.name ?? 'Admin' }}
              </p>
            </div>
            
            <div v-else class="space-y-1">
              <div class="h-3 w-24 bg-slate-800 rounded animate-pulse"></div>
              <div class="h-2 w-16 bg-slate-800 rounded animate-pulse"></div>
            </div>
          </div>

          <span class="text-slate-600 group-hover:text-slate-400 transition-colors text-xs shrink-0 pl-1">➔</span>
        </router-link>
      </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto max-h-screen relative">
      
      <!-- Loading Overlay Global -->
      <div v-if="loading" class="absolute inset-0 bg-slate-50/80 backdrop-blur-sm flex items-center justify-center z-10">
         <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex justify-between items-center mb-8 shadow-sm">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dashboard Global</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Vue d'ensemble de votre activité et de vos équipes.</p>
        </div>

        <div class="flex items-center gap-3">
          <button @click="router.push('/notifications')" class="relative w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-all shadow-sm hover:scale-105 active:scale-95">
              🔔
              <span v-if="unreadCount > 0"
              class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border border-white">
              {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
          </button>   
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Effectif -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-default">
          <div class="flex justify-between items-start">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-indigo-500 transition-colors">Effectif Global</span>
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-all"><i class="fas fa-users text-xs"></i></div>
          </div>
          <h2 class="text-3xl font-black mt-3 text-slate-900 tracking-tight">{{ stats.totalUsers }}</h2>
          <p class="text-[10px] font-bold text-slate-500 mt-1">Employés enregistrés</p>
        </div>

        <!-- Projets -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-default">
          <div class="flex justify-between items-start">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-emerald-500 transition-colors">Projets Actifs</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all"><i class="fas fa-folder-open text-xs"></i></div>
          </div>
          <h2 class="text-3xl font-black mt-3 text-slate-900 tracking-tight">{{ stats.totalProjects }}</h2>
          <p class="text-[10px] font-bold text-slate-500 mt-1">Projets en cours</p>
        </div>

        <!-- Services -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-default">
          <div class="flex justify-between items-start">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-amber-500 transition-colors">Services</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all"><i class="fas fa-briefcase text-xs"></i></div>
          </div>
          <h2 class="text-3xl font-black mt-3 text-slate-900 tracking-tight">{{ stats.totalServices }}</h2>
          <p class="text-[10px] font-bold text-slate-500 mt-1">Offres disponibles</p>
        </div>

        <!-- Chiffre d'affaire -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-default">
          <div class="flex justify-between items-start">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-rose-500 transition-colors">Budget Total</span>
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition-all"><i class="fas fa-wallet text-xs"></i></div>
          </div>
          <h2 class="text-2xl font-black mt-3 text-slate-900 tracking-tight truncate">
            {{ stats.totalRevenue.toLocaleString('fr-FR') }} <span class="text-sm font-bold text-slate-400">DH</span>
          </h2>
          <p class="text-[10px] font-bold text-slate-500 mt-1">Valeur générée</p>
        </div>

      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm flex flex-col">
          <div class="flex justify-between items-center mb-5">
            <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wide">Derniers Projets</h3>
            <button @click="router.push('/projects')" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase">Voir Tout ➔</button>
          </div>
          
          <div class="space-y-3 flex-1">
            <template v-if="projects.length">
              <div v-for="project in projects.slice(0, 4)" :key="project.id" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors group">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                    #
                  </div>
                  <div>
                    <p class="text-xs font-bold text-slate-800">{{ project.nom || project.name || 'Projet sans nom' }}</p>
                    <p class="text-[10px] font-medium text-slate-400">{{ project.status || 'En cours' }}</p>
                  </div>
                </div>
                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-md">
                  {{ project.budget ? Number(project.budget).toLocaleString('fr-FR') + ' DH' : '-' }}
                </span>
              </div>
            </template>
            <div v-else class="h-full flex flex-col items-center justify-center text-center p-6 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
              <i class="fas fa-folder-open text-2xl text-slate-300 mb-2"></i>
              <p class="text-xs font-bold text-slate-400">Aucun projet trouvé</p>
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm">
          <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wide mb-5">Répartition des Équipes</h3>
          
          <div class="space-y-4">
            
            <div>
              <div class="flex justify-between text-xs font-bold mb-1.5">
                <span class="text-slate-600">Administrateurs</span>
                <span class="text-slate-900">{{ roleStats.admins }}</span>
              </div>
              <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="bg-indigo-500 h-2 rounded-full" :style="`width: ${stats.totalUsers ? (roleStats.admins / stats.totalUsers) * 100 : 0}%`"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs font-bold mb-1.5">
                <span class="text-slate-600">Managers</span>
                <span class="text-slate-900">{{ roleStats.managers }}</span>
              </div>
              <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="bg-amber-500 h-2 rounded-full" :style="`width: ${stats.totalUsers ? (roleStats.managers / stats.totalUsers) * 100 : 0}%`"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs font-bold mb-1.5">
                <span class="text-slate-600">Ressources Humaines</span>
                <span class="text-slate-900">{{ roleStats.rh }}</span>
              </div>
              <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="bg-emerald-500 h-2 rounded-full" :style="`width: ${stats.totalUsers ? (roleStats.rh / stats.totalUsers) * 100 : 0}%`"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs font-bold mb-1.5">
                <span class="text-slate-600">Employés</span>
                <span class="text-slate-900">{{ roleStats.employes }}</span>
              </div>
              <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" :style="`width: ${stats.totalUsers ? (roleStats.employes / stats.totalUsers) * 100 : 0}%`"></div>
              </div>
            </div>

          </div>
        </div>

      </div>

    </main>
  </div>
</template>