<script setup>
import { onMounted, onUnmounted, ref, computed } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const absences = ref([]);
const loading = ref(false);
const currentUser = ref(null);

const router = useRouter();

const goToJustifications = (absenceId) => {
  router.push(`/justifications/absence/${absenceId}`);
};

const getData = async () => {
  loading.value = true;
  try {

    const res = await api.get("/absences", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    
    absences.value = res.data.absences || [];

    const userResponse = await api.get("/users", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    currentUser.value = userResponse.data.admin || null;

  } catch (err) {
    console.error(err.response?.data);
  } finally {
    loading.value = false;
  }
};

const deleteAbsence = async (id) => {
  if (!confirm("Are you sure you want to delete this log?")) return;
  
  try {
    await api.delete(`/absences/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    getData(); 
  } catch (err) {
    console.error(err.response?.data);
  }
};

const canAccessSkills = computed(() =>
  ["Administrateur"].includes(currentUser.value?.role?.name)
);

onMounted(() => {
  getData();
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased flex overflow-hidden w-full">

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
            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">🏠</span>
              <span>Dashboard</span>
            </button>

            <button v-if="canAccessSkills" @click="router.push('/projects')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">📁</span>
              <span>Projects</span>
            </button>

            <button @click="router.push('/users')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">👥</span>
              <span>Utilisateurs</span>
            </button>

            <button @click="router.push('/absences')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 group transition-all text-sm text-left">
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
          class="bg-slate-900/80 border border-slate-800/40 rounded-xl p-3.5 flex items-center gap-3 shadow-inner hover:bg-slate-900 hover:border-slate-700/60 transition-all group cursor-pointer w-full text-left block"
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

    <main class="flex-1 w-0 p-8 max-w-full flex flex-col justify-start overflow-y-auto h-screen">

      <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex justify-between items-center mb-8 shadow-sm shrink-0">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Absences Log Center</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Monitor, verify, and manage company attendance flow records.</p>
        </div>
        
        <button 
          @click="getData" 
          :disabled="loading"
          class="text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm disabled:opacity-60"
        >
          <i class="fas fa-sync-alt" :class="{ 'animate-spin': loading }"></i> 
          <span>{{ loading ? 'Updating...' : 'Refresh Logs' }}</span>
        </button>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden flex flex-col">
        
        <div class="p-5 border-b border-slate-100 bg-white">
          <h2 class="font-bold text-slate-900 text-sm tracking-tight flex items-center gap-2">
            <i class="fa-regular fa-calendar-minus text-indigo-500"></i> Registre d'émargement et absences
          </h2>
        </div>

        <div class="overflow-x-auto w-full">
          <table class="w-full table-fixed border-collapse">
            <thead>
              <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-5 py-4 text-left w-[22%]">Employee Info</th>
                <th class="px-5 py-4 text-left w-[14%]">Log Date</th>
                <th class="px-5 py-4 text-left w-[14%]">Status</th>
                <th class="px-5 py-4 text-left w-[12%]">Check In</th>
                <th class="px-5 py-4 text-left w-[12%]">Check Out</th>
                <th class="px-5 py-4 text-center w-[11%]">Justified</th>
                <th class="px-5 py-4 text-right w-[15%]">Actions</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              <template v-if="loading">
                <tr v-for="n in 5" :key="n" class="animate-pulse">
                  <td class="px-5 py-4" v-for="i in 7" :key="i">
                    <div class="h-4 bg-slate-100 rounded w-full my-1"></div>
                  </td>
                </tr>
              </template>

              <template v-else-if="absences.length === 0">
                <tr>
                  <td colspan="7" class="py-16 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/20">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-3">
                      <i class="fas fa-folder-open text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700 normal-case mb-1">No records discovered</h3>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto font-normal normal-case">
                      All clear! There are currently no recorded absence flags logged inside the platform databases.
                    </p>
                  </td>
                </tr>
              </template>

              <template v-else>
                <tr v-for="a in absences" :key="a.id" class="hover:bg-slate-50/50 transition-colors group">
                  
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3 min-w-0">
                      <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs uppercase shadow-sm shrink-0 border border-indigo-100/40">
                        {{ a.user?.firstname?.[0] || 'U' }}{{ a.user?.lastname?.[0] || '' }}
                      </div>
                      <div class="truncate">
                        <span class="font-bold text-slate-900 block text-xs uppercase truncate">
                          {{ a.user?.firstname }} {{ a.user?.lastname }}
                        </span>
                        <span class="text-[10px] font-mono text-slate-400 block mt-0.5">ID: #{{ a.user_id }}</span>
                      </div>
                    </div>
                  </td>

                  <td class="px-5 py-4 font-semibold text-slate-700 text-xs font-mono">
                    {{ a.date }}
                  </td>

                  <td class="px-5 py-4">
                    <span 
                      class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold inline-flex items-center gap-1 shadow-sm uppercase tracking-wider"
                      :class="{
                        'bg-rose-50 text-rose-700 border border-rose-200/40': a.status === 'absent',
                        'bg-amber-50 text-amber-700 border border-amber-200/40': a.status === 'late',
                        'bg-emerald-50 text-emerald-700 border border-emerald-200/40': a.status === 'present'
                      }"
                    >
                      <span class="w-1.5 h-1.5 rounded-full" :class="{ 'bg-rose-500': a.status === 'absent', 'bg-amber-500': a.status === 'late', 'bg-emerald-500': a.status === 'present' }"></span>
                      <span>{{ a.status }}</span>
                    </span>
                  </td>

                  <td class="px-5 py-4 text-slate-500 font-mono text-xs font-medium">
                    {{ a.check_in || '--:--' }}
                  </td>
                  <td class="px-5 py-4 text-slate-500 font-mono text-xs font-medium">
                    {{ a.check_out || '--:--' }}
                  </td>

                  <td class="px-5 py-4 text-center">
                    <span 
                      class="text-[10px] font-black px-2 py-0.5 rounded border uppercase tracking-wider shadow-sm"
                      :class="a.is_justified ? 'text-emerald-700 bg-emerald-50 border-emerald-200/60' : 'text-slate-400 bg-slate-100 border-slate-200/60'"
                    >
                      {{ a.is_justified ? 'Yes' : 'No' }}
                    </span>
                  </td>

                  <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <button
                        @click="goToJustifications(a.id)"
                        class="bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white px-2.5 py-1.5 rounded-xl text-[11px] font-bold border border-indigo-100/80 hover:border-indigo-600 transition-all shadow-sm active:scale-95 shrink-0"
                      >
                        Justify
                      </button>

                      <button
                        @click="deleteAbsence(a.id)"
                        class="opacity-0 group-hover:opacity-100 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white p-1.5 rounded-xl transition-all duration-200 border border-rose-100 shadow-sm active:scale-95 shrink-0"
                        title="Delete Log"
                      >
                        <i class="fas fa-trash-alt text-[11px]"></i>
                      </button>
                    </div>
                  </td>

                </tr>
              </template>
            </tbody>

          </table>
        </div>
      </div>

    </main>

  </div>
</template>