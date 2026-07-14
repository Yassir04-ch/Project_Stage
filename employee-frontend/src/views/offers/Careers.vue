<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import jobOfferService from "@/services/jobOfferService";

const router = useRouter();
const offers = ref([]);
const loading = ref(true);
const search = ref("");
const contractFilter = ref("all");

const fetchOffers = async () => {
  loading.value = true;
  try {
    const res = await jobOfferService.getOpenOffers();
    offers.value = res.data.data || [];
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchOffers);

const filteredOffers = computed(() =>
  offers.value.filter((o) => {
    const matchSearch =
      o.title.toLowerCase().includes(search.value.toLowerCase()) ||
      (o.location || "").toLowerCase().includes(search.value.toLowerCase());
    const matchContract = contractFilter.value === "all" || o.contract_type === contractFilter.value;
    return matchSearch && matchContract;
  })
);

const formatSalary = (min, max) => {
  if (!min && !max) return null;
  if (min && max) return `${Number(min).toLocaleString("fr-FR")} - ${Number(max).toLocaleString("fr-FR")} MAD`;
  return `${Number(min || max).toLocaleString("fr-FR")} MAD`;
};

const formatDeadline = (d) => {
  if (!d) return null;
  return new Date(d).toLocaleDateString("fr-FR", { day: "2-digit", month: "short", year: "numeric" });
};
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <div class="min-h-screen bg-slate-50 text-slate-800 font-sans antialiased">

    <nav class="max-w-7xl mx-auto px-6 lg:px-16 py-5 flex justify-between items-center sticky top-0 bg-slate-50/80 backdrop-blur-md z-50 border-b border-slate-200/40">
      <div class="flex items-center gap-3 group cursor-pointer" @click="router.push('/')">
        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black text-sm tracking-wider shadow-xs transition-transform group-hover:scale-105">
          DX
        </div>
        <h1 class="text-lg font-black tracking-tight text-slate-900 uppercase">
          Data<span class="text-blue-600">Xpress</span>
        </h1>
      </div>

      <div class="hidden md:flex items-center gap-8 text-[14px] font-bold text-slate-500">
        <a href="/" class="hover:text-blue-600 transition-colors">Accueil</a>
        <a href="/contacts" class="hover:text-blue-600 transition-colors">Contact & Audit</a>
        <span class="text-slate-900">Carrières</span>
        <button
          @click="router.push('/login')"
          class="bg-white text-slate-700 border border-slate-200/80 px-4.5 py-2 rounded-xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all font-bold text-xs uppercase tracking-wider shadow-2xs">
          Connexion Employé
        </button>
      </div>
    </nav>

    <header class="relative bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-900 text-white pt-16 pb-24 px-6 lg:px-16 overflow-hidden">
      <div class="max-w-7xl mx-auto relative z-10 space-y-4 max-w-2xl">
        <h2 class="text-4xl md:text-5xl font-black tracking-tight leading-tight">Rejoignez notre équipe</h2>
        <p class="text-slate-300 text-base md:text-lg leading-relaxed opacity-90">
          Découvrez nos offres d'emploi et construisez votre carrière avec DataXpress.
        </p>
      </div>
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-blue-500/10 via-transparent to-transparent pointer-events-none"></div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 -mt-14 pb-20 relative z-20 space-y-8">

      <div class="bg-white rounded-3xl p-4 sm:p-5 shadow-lg border border-slate-100 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
          <input
            v-model="search"
            type="text"
            placeholder="Rechercher un poste, une ville..."
            class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500"
          />
        </div>
        <select
          v-model="contractFilter"
          class="px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500"
        >
          <option value="all">Tous les contrats</option>
          <option value="CDI">CDI</option>
          <option value="CDD">CDD</option>
          <option value="Stage">Stage</option>
          <option value="Freelance">Freelance</option>
        </select>
      </div>

      <div v-if="loading" class="text-center py-20 text-slate-400 font-semibold">
        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Chargement des offres...
      </div>

      <div v-else-if="filteredOffers.length === 0" class="text-center py-20 bg-white rounded-3xl border border-slate-100 shadow-sm">
        <i class="fa-solid fa-inbox text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-500 font-semibold">Aucune offre disponible pour le moment.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div
          v-for="offer in filteredOffers"
          :key="offer.id"
          @click="router.push(`/carrieres/${offer.id}`)"
          class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer group"
        >
          <div class="flex items-start justify-between gap-3 mb-3">
            <h3 class="text-lg font-black text-slate-900 group-hover:text-blue-600 transition-colors">{{ offer.title }}</h3>
            <span class="shrink-0 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-600 border border-blue-500/20">
              {{ offer.contract_type }}
            </span>
          </div>

          <p class="text-slate-500 text-sm leading-relaxed line-clamp-2 mb-4">{{ offer.description }}</p>

          <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-400">
            <span v-if="offer.location" class="flex items-center gap-1.5">
              <i class="fa-solid fa-location-dot"></i> {{ offer.location }}
            </span>
            <span v-if="formatSalary(offer.salary_min, offer.salary_max)" class="flex items-center gap-1.5">
              <i class="fa-solid fa-sack-dollar"></i> {{ formatSalary(offer.salary_min, offer.salary_max) }}
            </span>
            <span v-if="offer.deadline" class="flex items-center gap-1.5">
              <i class="fa-regular fa-calendar"></i> avant le {{ formatDeadline(offer.deadline) }}
            </span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>