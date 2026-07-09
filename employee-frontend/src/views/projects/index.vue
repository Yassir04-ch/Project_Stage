<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const router   = useRouter();
const projects  = ref([]);
const loading   = ref(false);
const user      = ref(null);
const unreadCount = ref(0);
const isDark    = ref(localStorage.getItem("theme") !== "light");
const search    = ref("");
const filterStatus = ref("all");

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
};

const filteredProjects = computed(() => {
  return projects.value.filter((p) => {
    const matchSearch = p.name.toLowerCase().includes(search.value.toLowerCase())
      || (p.description || "").toLowerCase().includes(search.value.toLowerCase());
    const matchStatus = filterStatus.value === "all" || p.status === filterStatus.value;
    return matchSearch && matchStatus;
  });
});

const stats = computed(() => ({
  total:     projects.value.length,
  active:    projects.value.filter((p) => p.status === "active").length,
  planning:  projects.value.filter((p) => p.status === "planning").length,
  completed: projects.value.filter((p) => p.status === "completed").length,
}));

const loadProjects = async () => {
  loading.value = true;
  try {
    const res = await api.get("/projects", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    projects.value = res.data.data || res.data.projects || res.data || [];
  } catch (err) {
    console.error(err.response?.data);
  } finally {
    loading.value = false;
  }
};

const loadProfile = async () => {
  try {
    const res = await api.get("/profile", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    user.value = res.data.data;
  } catch (err) {
    console.error(err);
  }
};

const loadUnreadCount = async () => {
  try {
    const res = await api.get("/notifications");
    const notifications = res.data.notifications || [];
    unreadCount.value = notifications.filter((n) => !n.is_read).length;
  } catch {}
};

const listenToNotifications = () => {
  if (!user.value?.id || !window.Echo) return;
  window.Echo.private(`notifications.${user.value.id}`).listen(".notification.new", () => {
    unreadCount.value++;
  });
};

const stopListening = () => {
  if (user.value?.id && window.Echo) {
    window.Echo.leave(`notifications.${user.value.id}`);
  }
};

const formatDate = (d) => {
  if (!d) return "—";
  return new Date(d).toLocaleDateString("fr-FR", {
    day: "2-digit", month: "short", year: "numeric",
  });
};

const formatBudget = (v) => {
  if (!v) return "—";
  return `${Number(v).toLocaleString("fr-FR")} MAD`;
};

const getStatusConfig = (status) => {
  const map = {
    planning:  { label: "Planification", dot: "bg-amber-500",   badge: "bg-amber-500/10 text-amber-500 border-amber-500/20",     icon: "fa-hourglass-half" },
    active:    { label: "En cours",      dot: "bg-emerald-500", badge: "bg-emerald-500/10 text-emerald-500 border-emerald-500/20", icon: "fa-circle-play" },
    completed: { label: "Terminé",       dot: "bg-indigo-500",  badge: "bg-indigo-500/10 text-indigo-500 border-indigo-500/20",   icon: "fa-circle-check" },
    cancelled: { label: "Annulé",        dot: "bg-rose-500",    badge: "bg-rose-500/10 text-rose-500 border-rose-500/20",         icon: "fa-circle-xmark" },
  };
  return map[status] || { label: status, dot: "bg-slate-500", badge: "bg-slate-500/10 text-slate-400 border-slate-500/20", icon: "fa-circle" };
};

const filters = [
  { key: "all",       label: "Tous" },
  { key: "active",    label: "En cours" },
  { key: "planning",  label: "Planification" },
  { key: "completed", label: "Terminés" },
  { key: "cancelled", label: "Annulés" },
];

onMounted(async () => {
  await Promise.all([loadProjects(), loadProfile(), loadUnreadCount()]);
  listenToNotifications();
});

onUnmounted(() => stopListening());
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div :class="isDark ? 'bg-slate-950 text-slate-400' : 'bg-slate-50 text-slate-600'"
    class="min-h-screen font-sans antialiased w-full flex flex-col transition-colors duration-300">

    <!-- Navbar -->
    <nav :class="isDark ? 'bg-slate-950/40 border-slate-900/80 text-slate-200' : 'bg-white/60 border-slate-200 text-slate-800'"
      class="backdrop-blur-xl px-8 py-4 flex justify-between items-center shadow-2xl border-b shrink-0 sticky top-0 z-50 w-full transition-colors duration-300">

      <div class="flex items-center gap-3.5 cursor-pointer" @click="router.push('/')">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white text-xl font-black shadow-[0_4px_12px_rgba(99,102,241,0.3)] italic">D</div>
        <div>
          <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-black tracking-wider uppercase italic leading-none">DataXpress</h1>
          <p class="text-[10px] text-indigo-500 font-bold tracking-widest uppercase mt-1">Workspace Hub</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <div :class="isDark ? 'bg-slate-900/40 border-slate-800/60' : 'bg-slate-100 border-slate-200'" class="flex items-center gap-1 p-1.5 rounded-xl border">

          <button @click="router.push('/notifications')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="relative px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-bell text-sm opacity-60"></i><span class="hidden md:inline">Notifications</span>
            <span v-if="unreadCount > 0"
              class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
              {{ unreadCount > 9 ? "9+" : unreadCount }}
            </span>
          </button>

          <button @click="router.push('/myabsences')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fa-regular fa-calendar-minus text-sm opacity-60"></i><span class="hidden md:inline">Mes Absences</span>
          </button>

          <button @click="router.push('/Myassignement')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-folder text-sm opacity-60"></i><span class="hidden md:inline">Assignement</span>
          </button>

          <button class="px-4 py-2.5 rounded-lg text-xs font-black bg-indigo-600 text-white shadow-[0_4px_12px_rgba(79,70,229,0.25)] flex items-center gap-2">
            <i class="fas fa-diagram-project text-sm"></i><span class="hidden md:inline">Projects</span>
          </button>

          <button v-if="user?.role?.name === 'Administrateur'"
            @click="router.push('/users')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-gauge text-sm opacity-60"></i><span class="hidden md:inline">Dashboard</span>
          </button>

          <button @click="router.push('/profile')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-user text-sm opacity-60"></i><span class="hidden md:inline">Mon Profil</span>
          </button>

        </div>
        <button @click="toggleTheme"
          :class="isDark ? 'bg-slate-900 text-amber-400 border-slate-800' : 'bg-white text-indigo-600 border-slate-200 shadow-sm'"
          class="w-10 h-10 rounded-xl border flex items-center justify-center transition-all active:scale-95 text-sm">
          <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'"></i>
        </button>
      </div>
    </nav>

    <main class="flex-1 max-w-7xl w-full mx-auto px-8 md:px-12 py-10 space-y-8 relative">

      <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-indigo-600/[0.04] blur-[160px] rounded-full pointer-events-none"></div>

      <!-- Header -->
      <div :class="isDark ? 'border-slate-900' : 'border-slate-200'" class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b">
        <div>
          <h2 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-2xl font-black tracking-tight uppercase">Projets</h2>
          <p class="text-xs text-slate-400 font-medium mt-1">Liste de tous les projets de l'entreprise.</p>
        </div>

        <!-- Search -->
        <div :class="isDark ? 'bg-slate-900/40 border-slate-800' : 'bg-white border-slate-200 shadow-sm'"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl border w-full md:w-72">
          <i class="fas fa-search text-slate-400 text-xs"></i>
          <input v-model="search" type="text" placeholder="Rechercher un projet..."
            :class="isDark ? 'bg-transparent text-slate-200 placeholder:text-slate-600' : 'bg-transparent text-slate-800 placeholder:text-slate-400'"
            class="flex-1 text-xs font-medium outline-none" />
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div v-for="item in [
          { label: 'Total',        value: stats.total,     icon: 'fa-layer-group',  color: 'text-indigo-500',  bg: 'bg-indigo-500/10 border-indigo-500/20' },
          { label: 'En cours',     value: stats.active,    icon: 'fa-circle-play',  color: 'text-emerald-500', bg: 'bg-emerald-500/10 border-emerald-500/20' },
          { label: 'Planification',value: stats.planning,  icon: 'fa-hourglass-half', color: 'text-amber-500', bg: 'bg-amber-500/10 border-amber-500/20' },
          { label: 'Terminés',     value: stats.completed, icon: 'fa-circle-check', color: 'text-indigo-400',  bg: 'bg-indigo-400/10 border-indigo-400/20' },
        ]" :key="item.label"
          :class="isDark ? 'bg-slate-900/30 border-slate-900' : 'bg-white border-slate-200/80 shadow-sm'"
          class="p-4 rounded-2xl border flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl border flex items-center justify-center shrink-0" :class="[item.bg, item.color]">
            <i :class="`fas ${item.icon} text-sm`"></i>
          </div>
          <div>
            <span :class="isDark ? 'text-white' : 'text-slate-900'" class="block text-xl font-black leading-none">{{ item.value }}</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ item.label }}</span>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex items-center gap-2 flex-wrap">
        <button v-for="f in filters" :key="f.key" @click="filterStatus = f.key"
          :class="filterStatus === f.key
            ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-600/20'
            : isDark
              ? 'bg-slate-900/30 text-slate-400 border-slate-900 hover:border-slate-700'
              : 'bg-white text-slate-500 border-slate-200 hover:border-slate-300 shadow-sm'"
          class="px-4 py-2 rounded-xl text-xs font-bold border transition-all">
          {{ f.label }}
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-24">
        <div class="w-9 h-9 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-[10px] font-bold text-slate-500 mt-4 uppercase tracking-widest">Chargement des projets...</p>
      </div>

      <!-- Empty -->
      <div v-else-if="filteredProjects.length === 0"
        :class="isDark ? 'bg-slate-900/20 border-slate-900/40' : 'bg-slate-50 border-slate-200'"
        class="p-16 rounded-2xl border text-center">
        <i class="fas fa-folder-open text-3xl text-slate-300 dark:text-slate-700 mb-3 block"></i>
        <p class="text-sm font-bold text-slate-400">Aucun projet trouvé.</p>
      </div>

      <!-- Cards grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="p in filteredProjects" :key="p.id"
          :class="isDark ? 'bg-slate-900/30 border-slate-900 hover:border-slate-700' : 'bg-white border-slate-200/80 shadow-sm hover:border-slate-300 hover:shadow-md'"
          class="p-5 rounded-2xl border transition-all duration-200 flex flex-col gap-4 cursor-pointer group">
          
          <!-- Top -->
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                :class="getStatusConfig(p.status).badge.replace('text-', 'text-').replace('border-', 'border-')">
                <i :class="`fas ${getStatusConfig(p.status).icon} text-sm`"
                  :style="p.status === 'active' ? 'color: rgb(16 185 129)' : p.status === 'planning' ? 'color: rgb(245 158 11)' : p.status === 'completed' ? 'color: rgb(99 102 241)' : 'color: rgb(244 63 94)'">
                </i>
              </div>
              <div class="min-w-0">
                <h3 :class="isDark ? 'text-white group-hover:text-indigo-400' : 'text-slate-900 group-hover:text-indigo-600'"
                  class="text-sm font-black truncate uppercase tracking-wide transition-colors">{{ p.name }}</h3>
                <div class="flex items-center gap-1.5 mt-0.5">
                  <div class="w-1.5 h-1.5 rounded-full" :class="getStatusConfig(p.status).dot"></div>
                  <span class="text-[10px] font-bold text-slate-400">{{ getStatusConfig(p.status).label }}</span>
                </div>
              </div>
            </div>
            <span class="text-[9px] font-black px-2.5 py-1 rounded-full border uppercase tracking-wide shrink-0" :class="getStatusConfig(p.status).badge">
              {{ getStatusConfig(p.status).label }}
            </span>
          </div>

          <!-- Description -->
          <p v-if="p.description" class="text-xs text-slate-400 leading-relaxed line-clamp-2">
            {{ p.description }}
          </p>

          <!-- Details -->
          <div :class="isDark ? 'border-slate-900' : 'border-slate-100'" class="pt-3 border-t grid grid-cols-2 gap-3">
            <div>
              <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Budget</span>
              <span :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-xs font-bold">{{ formatBudget(p.budget) }}</span>
            </div>
            <div>
              <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Équipe</span>
              <span :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-xs font-bold">
                {{ p.assignments?.length ?? 0 }} membre(s)
              </span>
            </div>
            <div>
              <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Début</span>
              <span :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-xs font-bold">{{ formatDate(p.start_date) }}</span>
            </div>
            <div>
              <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Fin</span>
              <span :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-xs font-bold">{{ formatDate(p.end_date) }}</span>
            </div>
          </div>

        </div>
      </div>

    </main>
  </div>
</template>
