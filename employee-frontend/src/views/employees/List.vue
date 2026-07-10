<script setup>
import { ref, onMounted, watch } from "vue";
import { getEmploiyee } from "@/services/emploiyeeService";
import { useRouter } from "vue-router";
import api from "@/api/axios";

const router = useRouter();
const employees   = ref([]);
const roleFilter  = ref("");
const search      = ref("");
const loading     = ref(false);
const unreadCount = ref(0);
const user        = ref(null);
const isDark      = ref(localStorage.getItem("theme") !== "light");
const pagination  = ref({ data: [], links: [] });

const getPhotoUrl = (photo) => {
  if (!photo) return null;
  return `http://127.0.0.1:8000/storage/${photo}`;
};

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
  document.documentElement.classList.toggle("dark", isDark.value);
};

const fetchEmployees = async (page = 1) => {
  loading.value = true;
  try {
    const data = await getEmploiyee(page, roleFilter.value, search.value);
    employees.value  = data.data || [];
    pagination.value = { links: data.links || [] };
  } catch (error) {
    console.error(error);
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
  } catch {}
};

const loadUnreadCount = async () => {
  try {
    const res = await api.get("/notifications");
    unreadCount.value = (res.data.notifications || []).filter((n) => !n.is_read).length;
  } catch {}
};

let searchTimeout = null;
watch(search, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => fetchEmployees(1), 400); // debounce 400ms
});

watch(roleFilter, () => fetchEmployees(1));

onMounted(() => {
  fetchEmployees();
  loadProfile();
  loadUnreadCount();
  document.documentElement.classList.toggle("dark", isDark.value);
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div :class="isDark ? 'bg-slate-950 text-slate-400' : 'bg-slate-50 text-slate-600'"
    class="min-h-screen font-sans antialiased transition-colors duration-300">

    <!-- NAVBAR -->
    <nav :class="isDark ? 'bg-slate-950/40 border-slate-900/80 text-slate-200' : 'bg-white/60 border-slate-200 text-slate-800'"
      class="backdrop-blur-xl px-8 py-4 flex justify-between items-center shadow-2xl border-b shrink-0 sticky top-0 z-50 w-full transition-colors duration-300">

      <div class="flex items-center gap-3.5 cursor-pointer" @click="router.push('/')">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-indigo-500/20">
           D
        </div>
        <div>
            <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-black tracking-wider uppercase italic leading-none">DataXpress</h1>
            <p class="text-[10px] text-indigo-500 font-bold tracking-widest uppercase mt-1">Workspace Hub</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <div :class="isDark ? 'bg-slate-900/40 border-slate-800/60' : 'bg-slate-100 border-slate-200'"
          class="flex items-center gap-1 p-1.5 rounded-xl border">

          <button @click="router.push('/notifications')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="relative px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-bell text-sm opacity-60"></i>
            <span class="hidden md:inline">Notifications</span>
            <span v-if="unreadCount > 0"
              class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border border-white dark:border-slate-950">
              {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
          </button>

          <button @click="router.push('/myabsences')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fa-regular fa-calendar-minus text-sm opacity-60"></i>
            <span class="hidden md:inline">Mes Absences</span>
          </button>

          <button @click="router.push('/Myassignement')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-folder text-sm opacity-60"></i>
            <span class="hidden md:inline">Assignement</span>
          </button>

          <button @click="router.push('/allprojects')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-diagram-project text-sm opacity-60"></i>
            <span class="hidden md:inline">Projects</span>
          </button>

          <button v-if="user?.role?.name === 'Administrateur'"
            @click="router.push('/users')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-gauge text-sm opacity-60"></i>
            <span class="hidden md:inline">Dashboard</span>
          </button>

          <button @click="router.push('/emploiyee')"
            class="px-4 py-2.5 rounded-lg text-xs font-black bg-indigo-600 text-white shadow-[0_4px_12px_rgba(79,70,229,0.25)] flex items-center gap-2">
            <i class="fas fa-users text-sm"></i>
            <span class="hidden md:inline">Employés</span>
          </button>

          <button @click="router.push('/profile')"
            :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
            class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-user text-sm opacity-60"></i>
            <span class="hidden md:inline">Mon Profil</span>
          </button>

        </div>

        <button @click="toggleTheme"
          :class="isDark ? 'bg-slate-900 text-amber-400 border-slate-800' : 'bg-white text-indigo-600 border-slate-200 shadow-sm'"
          class="w-10 h-10 rounded-xl border flex items-center justify-center transition-all active:scale-95 text-sm">
          <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'"></i>
        </button>
      </div>
    </nav>

    <!-- CONTENT -->
    <main class="p-8 max-w-7xl mx-auto space-y-8">

      <!-- Header + Search + Filter -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 :class="isDark ? 'text-white' : 'text-slate-900'"
            class="text-3xl font-black tracking-wide uppercase italic">Employés</h1>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Gestion du personnel</p>
        </div>

        <!-- Search + Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">

          <!-- ✅ Search input -->
          <div :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200 shadow-sm'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl border w-full sm:w-64">
            <i class="fas fa-search text-slate-400 text-xs"></i>
            <input
              v-model="search"
              type="text"
              placeholder="Rechercher par nom, email..."
              :class="isDark ? 'bg-transparent text-slate-200 placeholder:text-slate-600' : 'bg-transparent text-slate-800 placeholder:text-slate-400'"
              class="flex-1 text-xs font-medium outline-none"
            />
            <button v-if="search" @click="search = ''"
              :class="isDark ? 'text-slate-500 hover:text-slate-300' : 'text-slate-400 hover:text-slate-600'"
              class="transition-colors">
              <i class="fas fa-xmark text-xs"></i>
            </button>
          </div>

          <!-- ✅ Role filter -->
          <select v-model="roleFilter"
            :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-700'"
            class="px-4 py-2.5 rounded-xl border text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 outline-none transition-all cursor-pointer">
            <option value="">Tous les rôles</option>
            <option value="Administrateur">Administrateur</option>
            <option value="Manager">Manager</option>
            <option value="Ressources Humaines">Ressources Humaines</option>
            <option value="Employé">Employé</option>
          </select>

        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-24">
        <div class="w-9 h-9 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-[10px] font-bold text-slate-500 mt-4 uppercase tracking-widest">Chargement...</p>
      </div>

      <!-- Empty -->
      <div v-else-if="employees.length === 0"
        :class="isDark ? 'bg-slate-900/20 border-slate-900' : 'bg-white border-slate-200 shadow-sm'"
        class="p-16 rounded-2xl border text-center">
        <i class="fas fa-users-slash text-3xl text-slate-300 dark:text-slate-700 mb-3 block"></i>
        <p class="text-sm font-bold text-slate-400">Aucun employé trouvé.</p>
        <p v-if="search || roleFilter" class="text-xs text-slate-400 mt-1">
          Essayez de modifier vos critères de recherche.
        </p>
      </div>

      <!-- GRID -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="emp in employees" :key="emp.id"
          class="group relative p-6 rounded-3xl border border-slate-200/60 dark:border-slate-800/60 bg-white/50 dark:bg-slate-900/20 backdrop-blur-sm transition-all hover:scale-[1.02] hover:shadow-2xl hover:shadow-indigo-500/10">

          <!-- Avatar -->
          <div class="relative mb-5 mx-auto w-28">
            <div class="absolute -inset-1 bg-gradient-to-tr from-indigo-500 via-purple-600 to-pink-500 rounded-2xl blur opacity-20 group-hover:opacity-50 transition duration-500"></div>
            <img v-if="emp.photo" :src="getPhotoUrl(emp.photo)"
              class="w-28 h-28 rounded-2xl object-cover relative border-[3px] border-white dark:border-slate-800 shadow-xl" />
            <div v-else
              class="w-28 h-28 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-4xl relative border-[3px] border-white dark:border-slate-800 shadow-xl uppercase">
              {{ emp.firstname?.[0] }}{{ emp.lastname?.[0] }}
            </div>
          </div>

          <!-- Info -->
          <div class="text-center space-y-1">
            <h3 class="font-black uppercase text-sm tracking-wide text-slate-800 dark:text-white">
              {{ emp.firstname }} {{ emp.lastname }}
            </h3>
            <div class="flex justify-center">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                {{ emp.role?.name || 'Employé' }}
              </span>
            </div>
            <p class="text-[10px] font-medium text-slate-400 pt-2 truncate px-2">{{ emp.email }}</p>
          </div>

          <!-- Action -->
          <button @click="router.push(`/getUser/${emp.id}`)"
            class="mt-6 w-full py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-bold uppercase tracking-widest hover:bg-indigo-600 dark:hover:bg-indigo-400 transition-all duration-300">
            Voir Profil
          </button>
        </div>
      </div>

      <!-- PAGINATION -->
      <div v-if="pagination.links.length > 3" class="flex gap-2 justify-center pb-10">
        <button v-for="(link, index) in pagination.links" :key="index"
          :disabled="!link.url"
          @click="link.url && fetchEmployees(new URL(link.url).searchParams.get('page'))"
          class="px-4 py-2 border rounded-xl text-xs font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed"
          :class="link.active
            ? 'bg-indigo-600 text-white border-indigo-600'
            : isDark
              ? 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'
              : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300'">
          <span v-html="link.label"></span>
        </button>
      </div>

    </main>
  </div>
</template>