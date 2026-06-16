<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const router = useRouter();

const user = ref(null);
const loading = ref(false);

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

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased w-full flex flex-col">

    <nav class="bg-slate-950 text-slate-200 px-8 py-4 flex justify-between items-center shadow-xl border-b border-slate-900 shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white text-lg font-black shadow-md shadow-indigo-500/10">
          D
        </div>
        <div>
          <h1 class="text-sm font-black tracking-tight text-white uppercase italic leading-none">DataExpress</h1>
          <p class="text-[10px] text-indigo-400 font-bold tracking-wider uppercase mt-0.5">Employee Space</p>
        </div>
      </div>

      <div class="flex gap-2">
        <button @click="router.push('/dashboard')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-slate-100 hover:bg-slate-900 transition-all">
          <i class="fas fa-home mr-1.5 opacity-70"></i> Dashboard
        </button>

        <button @click="router.push('/absences')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-slate-100 hover:bg-slate-900 transition-all">
          <i class="fa-regular fa-calendar-minus mr-1.5 opacity-70"></i> Absences
        </button>

        <button @click="router.push('/assignments')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-slate-100 hover:bg-slate-900 transition-all">
          <i class="fas fa-folder mr-1.5 opacity-70"></i> Projects
        </button>

        <button class="px-4 py-2 rounded-xl text-xs font-extrabold bg-indigo-600 text-white shadow-md shadow-indigo-600/10 transition-all">
          <i class="fas fa-user mr-1.5"></i> Profile
        </button>
      </div>
    </nav>

    <main class="flex-1 w-full max-w-5xl mx-auto p-6 md:p-10 flex flex-col justify-start">

      <div v-if="loading" class="flex flex-col items-center justify-center py-32 bg-white rounded-2xl border border-slate-200/60 shadow-sm flex-1">
        <div class="w-10 h-10 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-xs font-semibold text-slate-400 mt-4 uppercase tracking-widest">Chargement de votre compte...</p>
      </div>

      <div v-else-if="user" class="space-y-8 w-full">
        
        <div class="bg-white rounded-2xl border border-slate-200/60 overflow-hidden shadow-sm flex flex-col relative">
          
          <div class="h-44 bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 relative overflow-hidden shrink-0">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(99,102,241,0.08),transparent)]"></div>
          </div>

          <div class="px-8 pb-6 relative flex flex-col sm:flex-row sm:items-end justify-between gap-6 -mt-16 border-b border-slate-100 bg-white">
            <div class="flex flex-col sm:flex-row sm:items-end gap-5">
              
              <div class="shrink-0 relative z-10 mx-auto sm:mx-0">
                <img v-if="photoUrl" :src="photoUrl" alt="Profile" class="w-28 h-28 rounded-2xl border-4 border-white object-cover shadow-md bg-white" />
                <div v-else class="w-28 h-28 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-black text-3xl border-4 border-white shadow-md uppercase tracking-wider">
                  {{ user.firstname?.[0] }}{{ user.lastname?.[0] }}
                </div>
              </div>

              <div class="text-center sm:text-left min-w-0">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase truncate">
                  {{ user.firstname }} {{ user.lastname }}
                </h2>
                <p class="text-xs font-medium text-slate-400 mt-1 flex items-center justify-center sm:justify-start gap-2">
                  <i class="fas fa-envelope text-indigo-500/80"></i> <span class="font-mono text-slate-600">{{ user.email }}</span>
                </p>
                
                <div class="flex flex-wrap gap-2 mt-4 justify-center sm:justify-start">
                  <span class="text-[10px] font-extrabold px-3 py-1 rounded-full border bg-indigo-50 text-indigo-700 border-indigo-200/60 uppercase tracking-wider shadow-sm">
                    <i class="fas fa-shield-alt text-indigo-400 mr-1"></i> {{ user.role?.name || 'Employee' }}
                  </span>
                  <span v-if="user.type_contrat" class="text-[10px] font-extrabold px-3 py-1 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200/60 uppercase tracking-wider shadow-sm">
                    <i class="fas fa-file-signature text-emerald-400 mr-1"></i> {{ user.type_contrat }}
                  </span>
                </div>
              </div>
            </div>

            <div class="shrink-0 flex justify-center pb-1">
              <button @click="handleLogout" class="bg-rose-50 border border-rose-100 hover:bg-rose-600 text-rose-600 hover:text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 active:scale-95">
                <i class="fas fa-power-off text-[11px]"></i> Déconnexion
              </button>
            </div>
          </div>

          <div class="p-8 bg-slate-50/40 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            
            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-id-card text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Numéro CIN</p>
                <p class="text-xs font-bold text-slate-900 font-mono uppercase">{{ user.cin || "—" }}</p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-phone text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Téléphone</p>
                <p class="text-xs font-bold text-slate-700 font-mono">{{ user.telephone || "—" }}</p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-500 shrink-0"><i class="fas fa-wallet text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-emerald-600/80 uppercase tracking-widest leading-none mb-1">Salaire Mensuel</p>
                <p class="text-sm font-black text-emerald-700 font-mono">
                  {{ user.salaire ? `${Number(user.salaire).toLocaleString('fr-FR')} MAD` : '—' }}
                </p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-venus-mars text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Genre</p>
                <p class="text-xs font-bold text-slate-700 uppercase tracking-wide">{{ user.genre || "—" }}</p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-birthday-cake text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Date de Naissance</p>
                <p class="text-xs font-bold text-slate-700">{{ formatDate(user.date_naissance) }}</p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-calendar-check text-xs"></i></div>
              <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Date d'embauche</p>
                <p class="text-xs font-bold text-slate-700">{{ formatDate(user.date_embauche) }}</p>
              </div>
            </div>

            <div class="bg-white border border-slate-200/60 p-5 rounded-2xl shadow-sm flex items-start gap-4 md:col-span-2 lg:col-span-3">
              <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0"><i class="fas fa-map-marker-alt text-xs"></i></div>
              <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Adresse Habitation</p>
                <p class="text-xs font-bold text-slate-700 leading-relaxed">{{ user.adresse || "—" }}</p>
              </div>
            </div>

          </div>

          <div v-if="user.skills && user.skills.length" class="p-8 border-t border-slate-100 bg-white">
            <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
              <i class="fas fa-graduation-cap text-indigo-500"></i> Compétences et expertises renseignées
            </h3>
            
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in user.skills" :key="skill.id" class="inline-flex items-center text-xs font-bold px-3 py-1.5 bg-slate-50 text-slate-700 rounded-xl border border-slate-200/60 shadow-sm transition-colors hover:bg-slate-100">
                <i class="fas fa-check-circle text-[10px] text-indigo-500 mr-2"></i>
                <span>{{ skill.name }}</span>
              </span>
            </div>
          </div>

        </div>
      </div>

      <div v-else class="text-center py-16 bg-white border border-slate-200/60 rounded-2xl shadow-sm flex flex-col items-center justify-center flex-1">
        <div class="w-12 h-12 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-500 mb-3 text-sm animate-pulse">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="text-sm font-bold text-slate-700">Impossible de charger le profil</h3>
        <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1 font-medium">Veuillez rafraîchir l'interface ou vous reconnecter sur la plateforme.</p>
      </div>

    </main>
  </div>
</template>