<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const router = useRouter();

const user = ref(null);
const loading = ref(false);

// State for Dark/Light Mode toggle
const isDark = ref(localStorage.getItem("theme") !== "light");

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
};

const photoUrl = computed(() => {
  if (!user.value?.photo) return null;
  return `http://127.0.0.1:8000/storage/${user.value.photo}`;
});

const formatDate = (date) => {
  if (!date) return "—";
  return new Date(date).toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
};

const getSkillIcon = (name) => {
  const tech = name.toLowerCase();
  if (tech.includes('php') || tech.includes('laravel')) return 'fab fa-php text-indigo-500 dark:text-indigo-400';
  if (tech.includes('javascript') || tech.includes('js')) return 'fab fa-js text-amber-500 dark:text-amber-400';
  if (tech.includes('vue')) return 'fab fa-vuejs text-emerald-500 dark:text-emerald-400';
  if (tech.includes('mysql') || tech.includes('sql')) return 'fas fa-database text-blue-500 dark:text-blue-400';
  if (tech.includes('aws')) return 'fab fa-aws text-orange-500 dark:text-orange-400';
  if (tech.includes('docker')) return 'fab fa-docker text-cyan-500 dark:text-cyan-400';
  return 'fas fa-code text-indigo-500 dark:text-indigo-400';
};

const getProfile = async () => {
  loading.value = true;
  try {
    const res = await api.get("/profile", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    user.value = res.data.data;
  } catch (err) {
    console.log(err.response?.data);
  } finally {
    loading.value = false;
  }
};

const handleLogout = async () => {
  try {
    await api.post(
      "/logout",
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      }
    );
  } catch (error) {
    console.log(error);
  }
  localStorage.removeItem("token");
  router.push("/");
};

onMounted(() => {
  getProfile();
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div :class="isDark ? 'bg-slate-950 text-slate-400' : 'bg-slate-50 text-slate-600'" class="min-h-screen font-sans antialiased w-full flex flex-col selection:bg-indigo-500/30 selection:text-white transition-colors duration-300">

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
          <button @click="router.push('/notifications')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-bell text-sm opacity-60"></i> <span class="hidden md:inline">Notifications</span>
          </button>
          <button @click="router.push('/absences')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fa-regular fa-calendar-minus text-sm opacity-60"></i> <span class="hidden md:inline">Absences</span>
          </button>
          <button @click="router.push('/assignments')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-folder text-sm opacity-60"></i> <span class="hidden md:inline">Projets</span>
          </button>
          <button class="px-4 py-2.5 rounded-lg text-xs font-black bg-indigo-600 text-white shadow-[0_4px_12px_rgba(79,70,229,0.25)] flex items-center gap-2">
            <i class="fas fa-user text-sm"></i> <span class="hidden md:inline">Mon Profile</span>
          </button>
        </div>

        <button @click="toggleTheme" :class="isDark ? 'bg-slate-900 text-amber-400 border-slate-800' : 'bg-white text-indigo-600 border-slate-200 shadow-sm'" class="w-10 h-10 rounded-xl border flex items-center justify-center transition-all active:scale-95 text-sm">
          <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'"></i>
        </button>
      </div>
    </nav>

    <main class="flex-1 w-full flex flex-col justify-start relative overflow-hidden">
      
      <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-indigo-600/[0.06] blur-[160px] rounded-full pointer-events-none"></div>
      <div class="absolute bottom-10 right-10 w-[500px] h-[500px] bg-purple-600/[0.03] blur-[150px] rounded-full pointer-events-none"></div>

      <div v-if="loading" :class="isDark ? 'bg-slate-950' : 'bg-slate-50'" class="flex flex-col items-center justify-center min-h-[calc(100vh-80px)] flex-1">
        <div class="w-9 h-9 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-[10px] font-bold text-slate-500 mt-4 uppercase tracking-widest">Initialisation du Dashboard...</p>
      </div>

      <div v-else-if="user" class="grid grid-cols-1 lg:grid-cols-12 flex-1 w-full min-h-[calc(100vh-80px)]">
        
        <div :class="isDark ? 'bg-slate-950 border-slate-900 text-slate-300' : 'bg-white border-slate-200/80 text-slate-600'" class="lg:col-span-4 border-r p-8 flex flex-col justify-between relative overflow-hidden transition-colors duration-300">
          <div class="absolute top-0 right-0 w-48 h-48 bg-indigo-500/[0.02] blur-3xl rounded-full pointer-events-none"></div>
          
          <div class="space-y-8 relative">
            <div :class="isDark ? 'border-slate-900/80' : 'border-slate-100'" class="flex flex-col items-center text-center pb-8 border-b">
              <div class="relative group mb-5">
                <div class="absolute -inset-1 bg-gradient-to-tr from-indigo-500 via-purple-600 to-pink-500 rounded-2xl blur opacity-30 group-hover:opacity-60 transition duration-500"></div>
                <img v-if="photoUrl" :src="photoUrl" alt="Avatar Frame" :class="isDark ? 'border-slate-900 bg-slate-950' : 'border-slate-100 bg-white'" class="w-28 h-28 rounded-2xl object-cover relative border-2 shadow-2xl" />
                <div v-else class="w-28 h-28 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-4xl relative border-2 border-transparent uppercase tracking-wider">
                  {{ user.firstname?.[0] }}{{ user.lastname?.[0] }}
                </div>
              </div>

              <h3 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-2xl font-black tracking-wide uppercase">{{ user.firstname }} {{ user.lastname }}</h3>
              <p class="text-[10px] font-black text-indigo-500 dark:text-indigo-400 tracking-widest uppercase mt-2 px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-lg inline-block">
                {{ user.role?.name || 'Developer' }}
              </p>
            </div>

            <div class="grid grid-cols-3 gap-3 py-2 text-center">
              <div :class="isDark ? 'bg-slate-900/30 border-slate-900/60' : 'bg-slate-50 border-slate-200/60'" class="p-3 rounded-2xl border">
                <div :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-black">236</div>
                <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-1">Commits</div>
              </div>
              <div :class="isDark ? 'bg-slate-900/30 border-slate-900/60' : 'bg-slate-50 border-slate-200/60'" class="p-3 rounded-2xl border">
                <div :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-black">120</div>
                <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-1">Tickets</div>
              </div>
              <div :class="isDark ? 'bg-slate-900/30 border-slate-900/60' : 'bg-slate-50 border-slate-200/60'" class="p-3 rounded-2xl border">
                <div class="text-base font-black text-indigo-500 dark:text-indigo-400 font-mono">1.3K</div>
                <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-1">Hours</div>
              </div>
            </div>

            <div class="space-y-4 pt-2">
              <div :class="isDark ? 'bg-slate-900/20 border-slate-900/40 hover:border-slate-800' : 'bg-slate-50/60 border-slate-200/80 hover:border-slate-300'" class="flex items-center gap-4 px-4 py-3 rounded-2xl border transition-colors">
                <i class="fas fa-passport text-indigo-500 dark:text-indigo-400/80 text-sm w-5"></i>
                <div class="min-w-0 flex-1">
                  <span class="block text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-0.5">Identifiant Unique (CIN)</span>
                  <span :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-xs font-bold font-mono uppercase tracking-wider">{{ user.cin || "—" }}</span>
                </div>
              </div>

              <div :class="isDark ? 'bg-slate-900/20 border-slate-900/40 hover:border-slate-800' : 'bg-slate-50/60 border-slate-200/80 hover:border-slate-300'" class="flex items-center gap-4 px-4 py-3 rounded-2xl border transition-colors">
                <i class="fas fa-phone text-emerald-500 dark:text-emerald-400/80 text-sm w-5"></i>
                <div class="min-w-0 flex-1">
                  <span class="block text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-0.5">Ligne Secrétariat</span>
                  <span :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-xs font-bold font-mono tracking-wide">{{ user.telephone || "—" }}</span>
                </div>
              </div>

              <div :class="isDark ? 'bg-slate-900/20 border-slate-900/40 hover:border-slate-800' : 'bg-slate-50/60 border-slate-200/80 hover:border-slate-300'" class="flex items-center gap-4 px-4 py-3 rounded-2xl border transition-colors">
                <i class="fas fa-at text-purple-500 dark:text-purple-400/80 text-sm w-5"></i>
                <div class="min-w-0 flex-1">
                  <span class="block text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-0.5">Adresse Passerelle</span>
                  <span :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="text-xs font-bold truncate font-mono block">{{ user.email }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="pt-8 mt-auto">
            <button @click="handleLogout" class="w-full bg-rose-500/10 border border-rose-500/20 hover:bg-rose-600 text-rose-500 dark:text-rose-400 hover:text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 active:scale-95">
              <i class="fas fa-power-off"></i> Fermer Session Terminal
            </button>
          </div>
        </div>

        <div :class="isDark ? 'bg-slate-950/20' : 'bg-slate-100/40'" class="lg:col-span-8 p-8 md:p-12 space-y-10 flex flex-col justify-start transition-colors duration-300">
          
          <div class="space-y-6">
            <div :class="isDark ? 'border-slate-900' : 'border-slate-200'" class="flex items-center gap-2 pb-3 border-b">
              <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
              <h4 :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-[10px] font-black uppercase tracking-widest">Données Générales Collaborateur</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div :class="isDark ? 'bg-slate-900/30 border-slate-900' : 'bg-white border-slate-200/80 shadow-sm'" class="p-5 rounded-2xl flex items-center gap-4 backdrop-blur-sm border">
                <div class="w-11 h-11 bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 rounded-xl shrink-0"><i class="fas fa-wallet text-sm"></i></div>
                <div>
                  <span class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1.5">Émoluments de Base</span>
                  <span :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-base font-black font-mono tracking-tight">
                    {{ user.salaire ? `${Number(user.salaire).toLocaleString('fr-FR')} MAD` : '—' }}
                  </span>
                </div>
              </div>

              <div :class="isDark ? 'bg-slate-900/30 border-slate-900' : 'bg-white border-slate-200/80 shadow-sm'" class="p-5 rounded-2xl flex items-center gap-4 backdrop-blur-sm border">
                <div class="w-11 h-11 bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-500 rounded-xl shrink-0"><i class="fas fa-file-signature text-sm"></i></div>
                <div>
                  <span class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1.5">Régime Statutaire</span>
                  <span :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-sm font-black uppercase tracking-wider">{{ user.type_contrat || "—" }}</span>
                </div>
              </div>

              <div :class="isDark ? 'bg-slate-900/30 border-slate-900' : 'bg-white border-slate-200/80 shadow-sm'" class="p-5 rounded-2xl flex items-center gap-4 backdrop-blur-sm border">
                <div :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-400' : 'bg-slate-100 text-slate-500 border-slate-200'" class="w-11 h-11 border flex items-center justify-center rounded-xl shrink-0"><i class="fas fa-cake-candles text-sm"></i></div>
                <div>
                  <span class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1.5">Date Naissance</span>
                  <span :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="text-sm font-bold">{{ formatDate(user.date_naissance) }}</span>
                </div>
              </div>

              <div :class="isDark ? 'bg-slate-900/30 border-slate-900' : 'bg-white border-slate-200/80 shadow-sm'" class="p-5 rounded-2xl flex items-center gap-4 backdrop-blur-sm border">
                <div :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-400' : 'bg-slate-100 text-slate-500 border-slate-200'" class="w-11 h-11 border flex items-center justify-center rounded-xl shrink-0"><i class="fas fa-timeline text-sm"></i></div>
                <div>
                  <span class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1.5">Date Enregistrement</span>
                  <span :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="text-sm font-bold">{{ formatDate(user.date_embauche) }}</span>
                </div>
              </div>

              <div :class="isDark ? 'bg-slate-900/30 border-slate-900' : 'bg-white border-slate-200/80 shadow-sm'" class="p-5 rounded-2xl flex items-center gap-4 md:col-span-2 backdrop-blur-sm border">
                <div :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-400' : 'bg-slate-100 text-slate-500 border-slate-200'" class="w-11 h-11 border flex items-center justify-center rounded-xl shrink-0"><i class="fas fa-map-pin text-sm"></i></div>
                <div class="min-w-0 flex-1">
                  <span class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1.5">Domicile Administratif</span>
                  <span :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="text-sm font-bold truncate block">{{ user.adresse || "—" }}</span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="user.skills && user.skills.length" class="space-y-6 flex-1">
            <div :class="isDark ? 'border-slate-900' : 'border-slate-200'" class="flex items-center justify-between pb-3 border-b">
              <div class="flex items-center gap-2">
                <i class="fas fa-circle-nodes text-indigo-500 text-sm"></i>
                <h4 :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-[10px] font-black uppercase tracking-widest">Matrice des Compétences Renseignées</h4>
              </div>
              <span class="text-[9px] font-mono text-slate-400 dark:text-slate-600 tracking-wider">SKILLS_ENGINE_V2</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
              <div v-for="skill in user.skills" :key="skill.id" :class="isDark ? 'bg-slate-900/40 border-slate-900 hover:border-slate-800/80' : 'bg-white border-slate-200 hover:border-slate-300 shadow-sm'" class="border rounded-2xl p-4 flex flex-col justify-between transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/[0.01] rounded-bl-full pointer-events-none group-hover:bg-indigo-500/[0.02] transition-colors"></div>
                
                <div class="flex items-center gap-3 mb-4">
                  <div :class="isDark ? 'bg-slate-950 border-slate-800/80' : 'bg-slate-50 border-slate-200'" class="w-11 h-11 rounded-xl border flex items-center justify-center text-lg shadow-inner group-hover:scale-105 transition-transform">
                    <i :class="getSkillIcon(skill.name)"></i>
                  </div>
                  <div class="min-w-0 flex-1">
                    <h5 :class="isDark ? 'text-white group-hover:text-indigo-400' : 'text-slate-800 group-hover:text-indigo-600'" class="text-xs font-black truncate uppercase tracking-wide transition-colors">
                      {{ skill.name }}
                    </h5>
                    <p class="text-[8px] font-mono text-slate-400 dark:text-slate-500 mt-0.5 tracking-widest">VERIFIED_TECH</p>
                  </div>
                </div>

                <div :class="isDark ? 'border-slate-950' : 'border-slate-100'" class="pt-2.5 border-t flex items-center justify-between">
                  <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Niveau d'Expertise</span>
                  <span v-if="skill.pivot?.level" class="text-indigo-600 dark:text-indigo-400 font-black bg-indigo-500/10 px-2 py-0.5 rounded-md text-[9px] uppercase tracking-widest border border-indigo-500/20">
                    LVL {{ skill.pivot.level }}
                  </span>
                  <span v-else class="text-slate-400 dark:text-slate-600 font-bold text-[9px] font-mono">N/A</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div v-else :class="isDark ? 'bg-slate-950' : 'bg-slate-50'" class="text-center py-24 flex-1 flex flex-col items-center justify-center w-full min-h-[calc(100vh-80px)]">
        <div :class="isDark ? 'bg-slate-900 border-slate-800/60' : 'bg-white border-slate-200'" class="w-14 h-14 rounded-2xl border flex items-center justify-center text-rose-500 mb-4 text-xl shadow-sm">
          <i class="fas fa-database"></i>
        </div>
        <h3 :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-base font-black uppercase tracking-wider">Index non répertorié</h3>
        <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs mx-auto mt-2 font-medium leading-relaxed">Les structures d'identification n'ont retourné aucun enregistrement valide sur ce compte.</p>
      </div>

    </main>
  </div>
</template>