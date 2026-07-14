<script setup>
import { onMounted, onUnmounted, ref, computed } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const router = useRouter();

const users         = ref([]);
const loading       = ref(false);
const statusLoading = ref({});
const currentUser   = ref(null);
const unreadCount   = ref(0);

const getUsers = async () => {
  loading.value = true;
  try {
    const response = await api.get("/users", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    users.value       = response.data.users || [];
    currentUser.value = response.data.admin || null;
  } catch (error) {
    console.log(error.response?.data);
  } finally {
    loading.value = false;
  }
};

const toggleStatus = async (user) => {
  const isCurrentlyActive = user.status === 'active';
  const endpoint = isCurrentlyActive
    ? `/users/${user.id}/desactiver`
    : `/users/${user.id}/activer`;

  statusLoading.value[user.id] = true;
  try {
    const response = await api.put(endpoint, {}, {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    if (response.data.success) {
      user.status = isCurrentlyActive ? 'banni' : 'active';
    }
  } catch (error) {
    console.log("Erreur lors du changement de statut", error.response?.data);
  } finally {
    statusLoading.value[user.id] = false;
  }
};

const loadUnreadCount = async () => {
  try {
    const res = await api.get("/notifications", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    unreadCount.value = (res.data.notifications || []).filter(n => !n.is_read).length;
  } catch {}
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

const countAdmins = computed(() => 
  users.value.filter(u => u.role?.name === 'Administrateur').length
);

const countManagers = computed(() => 
  users.value.filter(u => u.role?.name === 'Manager').length
);

const countRH = computed(() => 
  users.value.filter(u => u.role?.name === 'Ressources Humaines').length
);

const countEmployees = computed(() => 
  users.value.filter(u => u.role?.name === 'Employé').length
);


onMounted(async () => {
  await getUsers();
  await loadUnreadCount();
  listenToNotifications();
});

onUnmounted(() => {
  stopListening();
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 flex antialiased font-sans">

    <aside class="bg-slate-950 text-slate-200 w-72 min-h-screen p-6 flex flex-col justify-between shadow-2xl shrink-0 border-r border-slate-800/40">
      
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
            <button @click="router.push('/statystic')"  class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">🏠</span>
              <span>Dashboard</span>
            </button>

            <button v-if="canAccessSkills" @click="router.push('/projects')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">📁</span>
              <span>Projects</span>
            </button>

            <button @click="router.push('/offres')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fas fa-briefcase text-base group-hover:scale-110 transition-transform"></i>
              <span>Offres d'emploi</span>
            </button>

            <button @click="router.push('/users')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 group transition-all text-sm text-left">
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
              <i class="fas fa-building text-base w-5"></i><span>Services</span>
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

    <main class="flex-1 p-8 overflow-y-auto max-h-screen">

      <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex justify-between items-center mb-8 shadow-sm">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Panneau d'Administration</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Ravi de vous revoir 👋 Suivi global de vos équipes.</p>
        </div>

        <div class="flex items-center gap-3">
          <button
            @click="router.push('/register')"
            class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider px-4 py-3 rounded-xl shadow-md shadow-indigo-600/15 transition-all flex items-center gap-2 active:scale-95"
          >
            <span>+</span>
            <span>Ajouter Employee</span>
          </button>

          <button @click="router.push('/notifications')" class="relative w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-all shadow-sm">
              🔔
              <span v-if="unreadCount > 0"
              class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border border-white dark:border-slate-950">
              {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
          </button>   
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Administrateurs</span>
          <h2 class="text-3xl font-black mt-2 text-slate-900 tracking-tight">{{ countAdmins }}</h2>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Managers</span>
          <h2 class="text-3xl font-black mt-2 text-slate-900 tracking-tight">{{ countManagers }}</h2>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ressources Humaines</span>
          <h2 class="text-3xl font-black mt-2 text-slate-900 tracking-tight">{{ countRH }}</h2>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Employés</span>
          <h2 class="text-3xl font-black mt-2 text-slate-900 tracking-tight">{{ countEmployees }}</h2>
        </div>

      </div>

      <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-white">
          <h2 class="text-base font-bold text-slate-900 tracking-tight">Liste des Employé</h2>
          <span class="text-xs bg-slate-100 text-slate-600 font-bold px-2.5 py-1 rounded-md">{{ users.length }} Enregistrés</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-slate-50/70 border-b border-slate-100">
                <th class="text-left p-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Collaborateur</th>
                <th class="text-left p-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Adresse Email</th>
                <th class="text-left p-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Salaire</th>
                <th class="text-left p-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">CIN</th>
                <th class="text-left p-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Fonction</th>
                <th class="text-left p-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Statut</th>
                <th class="text-center p-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              <template v-if="loading">
                <tr v-for="n in 3" :key="n" class="animate-pulse">
                  <td class="p-4" v-for="i in 7" :key="i">
                    <div class="h-4 bg-slate-100 rounded w-full my-1"></div>
                  </td>
                </tr>
              </template>

              <template v-else-if="users.length">
                <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/50 transition-all">
                  
                  <td class="p-4">
                    <router-link 
                      :to="`/getUser/${user.id || user._id}`" 
                      class="flex items-center gap-3 group cursor-pointer"
                    >
                      <div class="w-9 h-9 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center uppercase shrink-0 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-all shadow-sm">
                        {{ user.firstname?.[0] }}{{ user.lastname?.[0] }}
                      </div>

                      <div class="flex flex-col min-w-0">
                        <span class="text-xs font-bold text-slate-800 uppercase truncate group-hover:text-indigo-600 transition-colors">
                          {{ user.firstname }} {{ user.lastname }}
                        </span>
                        <span class="text-[10px] font-mono text-slate-400">View Profile ➔</span>
                      </div>
                    </router-link>
                  </td>

                  <td class="p-4 text-sm text-slate-500 font-medium">{{ user.email }}</td>
                  
                  <td class="p-4 text-sm font-bold text-slate-700">
                    {{ user.salaire ? `${Number(user.salaire).toLocaleString('fr-FR')} DH` : '—' }}
                  </td>
                  
                  <td class="p-4 text-sm font-mono font-medium text-slate-600 tracking-wide">{{ user.cin ?? '—' }}</td>
                  
                  <td class="p-4">
                    <span class="inline-flex text-[10px] font-bold px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-md uppercase tracking-wider shadow-sm">
                      {{ user.role?.name ?? 'Membre' }}
                    </span>
                  </td>

                  <td class="p-4">
                    <span v-if="user.status === 'active'" class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-md uppercase tracking-wider">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                      Actif
                    </span>
                    <span v-else class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 bg-rose-50 border border-rose-100 text-rose-700 rounded-md uppercase tracking-wider">
                      <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                      Banni
                    </span>
                  </td>

                  <td class="p-4 text-center">
                    <button 
                      @click="toggleStatus(user)"
                      :disabled="statusLoading[user.id]"
                      class="px-3 py-1.5 rounded-xl text-[11px] font-bold tracking-wide transition-all border active:scale-95 disabled:opacity-50 w-28 text-center inline-flex items-center justify-center gap-1"
                      :class="user.status === 'active' 
                        ? 'bg-rose-50 hover:bg-rose-100 border-rose-200 text-rose-600 shadow-sm shadow-rose-500/5' 
                        : 'bg-emerald-50 hover:bg-emerald-100 border-emerald-200 text-emerald-600 shadow-sm shadow-emerald-500/5'"
                    >
                      <i v-if="statusLoading[user.id]" class="fas fa-spinner animate-spin"></i>
                      <span v-else>
                        <i :class="user.status === 'active' ? 'fas fa-user-slash text-[10px]' : 'fas fa-user-check text-[10px]'"></i>
                      </span>
                      <span>{{ user.status === 'active' ? 'Désactiver' : 'Activer' }}</span>
                    </button>
                  </td>

                </tr>
              </template>

              <tr v-else>
                <td colspan="7" class="p-12 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/20">
                  Aucun utilisateur trouvé f l-base de données
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</template>