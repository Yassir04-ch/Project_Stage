<script setup>
import { ref, onMounted, watch } from "vue";
import { getEmploiyee } from "@/services/emploiyeeService";
import { useRouter } from "vue-router";

const router = useRouter();
const employees = ref([]);
const roleFilter = ref("");
const loading = ref(false);
const unreadCount = ref(0); // تأكد تربطها مع الـ API ديالك
const user = ref({ role: { name: 'Administrateur' } }); // مثال، بدل هادي بـ الـ user ديالك الحقيقي
const isDark = ref(localStorage.getItem("theme") !== "light");
const pagination = ref({ data: [], links: [] });

const getPhotoUrl = (photo) => {
  if (!photo) return null;
  return `http://127.0.0.1:8000/storage/${photo}`;
};

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
  if (isDark.value) {
    document.documentElement.classList.add("dark");
  } else {
    document.documentElement.classList.remove("dark");
  }
};

const fetchEmployees = async (page = 1) => {
  loading.value = true;
  try {
    const data = await getEmploiyee(page, roleFilter.value);
    employees.value = data.data || [];
    pagination.value = { links: data.links || [] };
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

watch(roleFilter, () => fetchEmployees(1));
onMounted(() => {
  fetchEmployees();
  if (isDark.value) document.documentElement.classList.add("dark");
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div :class="isDark ? 'bg-slate-950 text-slate-400' : 'bg-slate-50 text-slate-600'" class="min-h-screen font-sans antialiased transition-colors duration-300">
    
    <!-- NAVBAR -->
   <nav :class="isDark ? 'bg-slate-950/40 border-slate-900/80 text-slate-200' : 'bg-white/60 border-slate-200 text-slate-800'" class="backdrop-blur-xl px-8 py-4 flex justify-between items-center shadow-2xl border-b shrink-0 sticky top-0 z-50 w-full transition-colors duration-300">
    <div class="flex items-center gap-3.5">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white text-xl font-black shadow-[0_4px_12px_rgba(99,102,241,0.3)] italic">
        D
      </div>
      <div>
        <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-black tracking-wider uppercase italic leading-none">DataXpress</h1>
        <p class="text-[10px] text-indigo-500 dark:text-indigo-400 font-bold tracking-widest uppercase mt-1">Workspace Hub</p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <div :class="isDark ? 'bg-slate-900/40 border-slate-800/60' : 'bg-slate-100 border-slate-200'" class="flex items-center gap-1 p-1.5 rounded-xl border">

        <button @click="router.push('/notifications')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="relative px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fas fa-bell text-sm opacity-60"></i>
          <span class="hidden md:inline">Notifications</span>
          <span v-if="unreadCount > 0"
            class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border border-white dark:border-slate-950">
            {{ unreadCount > 9 ? '9+' : unreadCount }}
          </span>
        </button>

        <button @click="router.push('/myabsences')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fa-regular fa-calendar-minus text-sm opacity-60"></i>
          <span class="hidden md:inline">Mes Absences</span>
        </button>

        <button @click="router.push('/Myassignement')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fas fa-folder text-sm opacity-60"></i>
          <span class="hidden md:inline">Assignement</span>
        </button>

        <button @click="router.push('/allprojects')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fas fa-diagram-project text-sm opacity-60"></i>
          <span class="hidden md:inline">Projects</span>
        </button>

        <button
          v-if="user?.role?.name === 'Administrateur'"
          @click="router.push('/users')"
          :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
          class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fas fa-gauge text-sm opacity-60"></i>
          <span class="hidden md:inline">Dashboard</span>
        </button>
        <button @click="router.push('/emploiyee')" class="px-4 py-2.5 rounded-lg text-xs font-black bg-indigo-600 text-white shadow-[0_4px_12px_rgba(79,70,229,0.25)] flex items-center gap-2">
          <i class="fas fa-user text-sm"></i>
          <span class="hidden md:inline">Emploiyée</span>
        </button>

        <button @click="router.push('/profile')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fas fa-user text-sm"></i>
          <span class="hidden md:inline">Mon Profile</span>
        </button>

      </div>

      <button @click="toggleTheme" :class="isDark ? 'bg-slate-900 text-amber-400 border-slate-800' : 'bg-white text-indigo-600 border-slate-200 shadow-sm'" class="w-10 h-10 rounded-xl border flex items-center justify-center transition-all active:scale-95 text-sm">
        <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'"></i>
      </button>
    </div>
  </nav>


    <!-- CONTENT -->
    <main class="p-8 max-w-7xl mx-auto">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
      <div>
        <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-3xl font-black tracking-wide uppercase italic">Employés</h1>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Gestion du personnel</p>
      </div>

      <select v-model="roleFilter" :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200'" class="px-4 py-2 rounded-xl border text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-sky-500 outline-none transition-all">
        <option value="">Tous les rôles</option>
        <option value="Administrateur">Administrateur</option>
        <option value="Manager">Manager</option>
        <option value="Ressources Humaines">Ressources Humaines</option>
        <option value="Employé">Employé</option>
      </select>
    </div>

      <!-- GRID -->
      <div v-if="!loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="emp in employees" :key="emp.id" 
            class="group relative p-6 rounded-3xl border border-slate-200/60 dark:border-slate-800/60 bg-white/50 dark:bg-slate-900/20 backdrop-blur-sm transition-all hover:scale-[1.02] hover:shadow-2xl hover:shadow-indigo-500/10">
            
            <!-- Avatar Frame -->
            <div class="relative mb-5 mx-auto w-28">
            <div class="absolute -inset-1 bg-gradient-to-tr from-indigo-500 via-purple-600 to-pink-500 rounded-2xl blur opacity-20 group-hover:opacity-50 transition duration-500"></div>
            
            <img v-if="emp.photo" :src="getPhotoUrl(emp.photo)" 
                class="w-28 h-28 rounded-2xl object-cover relative border-[3px] border-white dark:border-slate-800 shadow-xl" />
            
            <div v-else class="w-28 h-28 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-4xl relative border-[3px] border-white dark:border-slate-800 shadow-xl uppercase">
                {{ emp.firstname?.[0] }}{{ emp.lastname?.[0] }}
            </div>
            </div>

            <!-- Info Section -->
            <div class="text-center space-y-1">
            <h3 class="font-black uppercase text-sm tracking-wide text-slate-800 dark:text-white">
                {{ emp.firstname }} {{ emp.lastname }}
            </h3>
            
            <!-- Role Badge -->
            <div class="flex justify-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                {{ emp.role?.name || 'Employé' }}
                </span>
            </div>

            <p class="text-[10px] font-medium text-slate-400 pt-2">{{ emp.email }}</p>
            </div>

            <!-- Action Button -->
            <button @click="router.push(`/getUser/${emp.id}`)" 
                    class="mt-6 w-full py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-bold uppercase tracking-widest hover:bg-indigo-600 dark:hover:bg-indigo-400 transition-all duration-300">
            Voir Profil
            </button>
        </div>
    </div>

      <!-- PAGINATION -->
      <div v-if="pagination.links.length" class="flex gap-2 justify-center mt-12 pb-10">
        <button v-for="(link, index) in pagination.links" :key="index"
                :disabled="!link.url"
                @click="link.url && fetchEmployees(new URL(link.url).searchParams.get('page'))"
                class="px-4 py-2 border rounded-xl text-xs font-bold transition-all"
                :class="link.active ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-900 dark:border-slate-800'">
          <span v-html="link.label"></span>
        </button>
      </div>
    </main>
  </div>
</template>