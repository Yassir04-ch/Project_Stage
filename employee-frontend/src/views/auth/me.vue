<script setup>
import { ref, onMounted } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const router = useRouter();

const user = ref(null);
const loading = ref(false);

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

// Formater les dates n9yin
const formatDate = (dateStr) => {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: '2-digit', month: 'short', year: 'numeric'
  })
}

// Handler dial simple logout (A modifier l-action dialek ila kanti kat-clear l-localstorage)
const handleLogout = () => {
  localStorage.removeItem("token");
  router.push("/login");
};

onMounted(() => {
  getProfile();
});
</script>

<template>
  <div class="min-h-screen bg-[#f8fafc] flex flex-col antialiased font-sans">

    <nav class="sticky top-0 z-50 bg-slate-900 text-white h-20 px-8 flex justify-between items-center shadow-md">
      <div class="flex items-center gap-2 group cursor-pointer" @click="router.push('/dashboard')">
        <div class="w-3 h-3 rounded-full bg-indigo-500 shadow-[0_0_10px_#6366f1]"></div>
        <h1 class="text-lg font-black tracking-tight text-white uppercase italic">My Dashboard</h1>
      </div>

      <div class="flex items-center gap-1 sm:gap-2">
        <button
          @click="router.push('/dashboard')"
          class="text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition-all"
        >
          <i class="fas fa-chart-pie mr-1.5 opacity-70"></i> Vue d'ensemble
        </button>

        <button
          @click="router.push('/absences')"
          class="text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition-all"
        >
          <i class="fas fa-calendar-times mr-1.5 opacity-70"></i> Absences
        </button>

        <button
          @click="router.push('/assignments')"
          class="text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition-all"
        >
          <i class="fas fa-tasks mr-1.5 opacity-70"></i> Projets Assignés
        </button>

        <button
          @click="router.push('/profile')"
          class="text-xs font-black uppercase tracking-wider px-4 py-2.5 rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-600/10 transition-all"
        >
          <i class="fas fa-user-circle mr-1.5"></i> Mon Profile
        </button>
      </div>
    </nav>

    <div class="flex-1 max-w-3xl w-full mx-auto p-6 sm:p-8">

      <div v-if="loading" class="flex flex-col items-center justify-center py-28 bg-white rounded-2xl border border-slate-200/60 shadow-sm">
        <div class="w-10 h-10 border-3 border-indigo-600/10 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-xs font-bold text-slate-400 mt-4 uppercase tracking-widest">Chargement du compte...</p>
      </div>

      <div v-else-if="user" class="bg-white rounded-2xl border border-slate-200/60 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
        
        <div class="h-28 bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 relative"></div>

        <div class="px-6 pb-6 relative flex flex-col sm:flex-row sm:items-end gap-5 -mt-12 border-b border-slate-100 bg-white">
          
          <div class="relative shrink-0">
            <div v-if="user.photo" class="w-24 h-24 rounded-2xl bg-white p-1 shadow-md border border-slate-100">
              <img :src="`/storage/${user.photo}`" class="w-full h-full rounded-xl object-cover" />
            </div>
            <div v-else class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-1 shadow-md shadow-indigo-500/20 flex items-center justify-center text-white font-black text-2xl tracking-wider border-2 border-white uppercase">
              {{ user.firstname?.[0] }}{{ user.lastname?.[0] }}
            </div>
          </div>

          <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold text-slate-900 tracking-tight truncate uppercase">
              {{ user.firstname }} {{ user.lastname }}
            </h2>

            <p class="text-xs font-medium text-slate-400 flex items-center gap-1.5 mt-0.5">
              <i class="far fa-envelope opacity-70"></i> {{ user.email }}
            </p>

            <div class="flex flex-wrap gap-1.5 mt-3">
              <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border bg-indigo-50 text-indigo-700 border-indigo-100 tracking-wider uppercase shadow-sm">
                <i class="fas fa-shield-halved mr-1"></i> {{ user.role?.name ?? 'Collaborateur' }}
              </span>
              <span v-if="user.type_contrat" class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-100 tracking-wider uppercase shadow-sm">
                {{ user.type_contrat }}
              </span>
              <span v-if="user.genre" class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border bg-slate-50 text-slate-600 border-slate-200 tracking-wider uppercase">
                {{ user.genre }}
              </span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-slate-50/20 border-b border-slate-100">
          
          <div class="p-5 flex flex-col justify-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <i class="fas fa-id-card text-slate-400"></i> Code CIN
            </span>
            <p class="text-sm font-semibold text-slate-800 tracking-wide">{{ user.cin ?? '—' }}</p>
          </div>

          <div class="p-5 flex flex-col justify-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <i class="fas fa-phone text-slate-400"></i> Téléphone
            </span>
            <p class="text-sm font-semibold text-slate-800 tracking-wide">{{ user.telephone ?? '—' }}</p>
          </div>

          <div class="p-5 flex flex-col justify-center border-t border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <i class="fas fa-wallet text-slate-400"></i> Rémunération Mensuelle
            </span>
            <p class="text-sm font-bold text-slate-900 tracking-tight">
              {{ user.salaire ? `${Number(user.salaire).toLocaleString('fr-FR')} MAD` : '—' }}
            </p>
          </div>

          <div class="p-5 flex flex-col justify-center border-t border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <i class="fas fa-map-marker-alt text-slate-400"></i> Adresse Résidentielle
            </span>
            <p class="text-sm font-semibold text-slate-700 truncate" :title="user.adresse">{{ user.adresse ?? '—' }}</p>
          </div>

          <div class="p-5 flex flex-col justify-center border-t border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <i class="far fa-calendar text-slate-400"></i> Date de Naissance
            </span>
            <p class="text-sm font-semibold text-slate-800">{{ formatDate(user.date_naissance) }}</p>
          </div>

          <div class="p-5 flex flex-col justify-center border-t border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
              <i class="fas fa-briefcase text-slate-400"></i> Date d'intégration (Embauche)
            </span>
            <p class="text-sm font-semibold text-slate-800">{{ formatDate(user.date_embauche) }}</p>
          </div>

        </div>

        <div v-if="user.skills && user.skills.length" class="p-5 border-b border-slate-100 bg-white">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3.5 block flex items-center gap-1.5">
            <i class="fas fa-star-half-alt text-indigo-500"></i> Compétences Validées
          </span>
          <div class="flex flex-wrap gap-1.5">
            <span v-for="skill in user.skills" :key="skill.id" class="inline-flex items-center text-xs font-semibold px-3 py-1 bg-slate-50 text-slate-700 rounded-xl border border-slate-200/60 shadow-sm">
              {{ skill.name }}
              <span v-if="skill.pivot && skill.pivot.level" class="text-indigo-600 font-extrabold bg-indigo-50 px-1.5 py-0.5 rounded-md text-[9px] ml-2 border border-indigo-100 uppercase tracking-wider">
                {{ skill.pivot.level }}
              </span>
            </span>
          </div>
        </div>

        <div class="p-4 bg-slate-50 flex justify-end items-center">
          <button
            @click="handleLogout"
            class="inline-flex items-center gap-2 bg-white text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 transition active:scale-95 shadow-sm"
          >
            <i class="fas fa-sign-out-alt"></i> Déconnexion
          </button>
        </div>

      </div>

      <div v-else class="text-center py-16 bg-white border border-slate-200/60 rounded-2xl shadow-sm">
        <div class="w-12 h-12 rounded-full bg-rose-50 flex items-center justify-center text-rose-500 mx-auto mb-3">
          <i class="fas fa-user-slash text-base"></i>
        </div>
        <p class="text-sm font-bold text-slate-800 uppercase tracking-wider">Impossible de charger le profil</p>
        <p class="text-xs text-slate-400 mt-1">Veuillez vérifier vos identifiants ou actualiser la session.</p>
      </div>

    </div>
  </div>
</template>