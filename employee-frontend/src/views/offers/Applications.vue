<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import applicationService from "@/services/applicationService";
import jobOfferService from "@/services/jobOfferService";

const route = useRoute();
const router = useRouter();
const offerId = route.params.id;

const applications = ref([]);
const offerTitle = ref("");
const loading = ref(true);
const error = ref("");
const selected = ref(null);

const cvUrl = (path) => `http://localhost:8000/storage/${path}`;

const getData = async () => {
  loading.value = true;
  error.value = "";
  try {
    const [offerRes, appsRes] = await Promise.all([
      jobOfferService.getOffer(offerId),
      applicationService.getApplicationsForOffer(offerId),
    ]);

    offerTitle.value = offerRes.data.data.title;
    applications.value = appsRes.data.data || [];

  } catch (e) {
    console.error(e);
    error.value = "Impossible de charger les candidatures. Réessayez.";
  } finally {
    loading.value = false;
  }
};

const updateStatus = async (app, status) => {
  const previous = app.status;
  app.status = status;
  try {
    await applicationService.updateStatus(app.id, status);
  } catch (e) {
    app.status = previous;
    window.alert("La mise à jour du statut a échoué. Réessayez.");
  }
};

const confirmDelete = async (app) => {
  if (!window.confirm(`Supprimer la candidature de ${app.full_name} ?`)) return;
  try {
    await applicationService.deleteApplication(app.id);
    applications.value = applications.value.filter((a) => a.id !== app.id);
  } catch (e) {
    window.alert("La suppression a échoué. Réessayez.");
  }
};

// Styles les badges de statut
const getStatusBadgeClass = (status) => {
  if (status === 'accepted') return 'bg-emerald-50 text-emerald-700 border-emerald-200 focus:ring-emerald-500/20';
  if (status === 'rejected') return 'bg-rose-50 text-rose-700 border-rose-200 focus:ring-rose-500/20';
  return 'bg-amber-50 text-amber-700 border-amber-200 focus:ring-amber-500/20';
};

const getInitials = (name) => {
  if (!name) return "?";
  return name.split(" ").map(n => n[0]).join("").toUpperCase().substring(0, 2);
};

onMounted(getData);
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased py-10 px-4 sm:px-6 lg:px-8 flex justify-center w-full overflow-y-auto">
    <div class="max-w-6xl w-full flex flex-col gap-6 animate-fade-in">
      
      <!-- 🔙 TOP HEADER (Bouton Back moderne + Infos) -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        
        <div class="flex items-center gap-4">
          <!-- Bouton Back -->
          <button 
            @click="router.push({ name: 'offers.manage' })" 
            class="group flex items-center gap-2 text-xs font-bold bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 px-4 py-2.5 rounded-xl shadow-sm transition-all active:scale-95 shrink-0"
          >
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Retour aux offres</span>
          </button>
          
          <div class="h-8 w-px bg-slate-200 hidden md:block"></div>

          <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              Candidatures
              <span v-if="offerTitle" class="text-indigo-600 font-bold text-lg truncate max-w-xs md:max-w-md">
                — {{ offerTitle }}
              </span>
            </h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Suivi et traitement en temps réel des dossiers reçus.</p>
          </div>
        </div>

        <!-- Badges & Refresh -->
        <div class="flex items-center gap-2.5 self-end md:self-auto">
          <div class="flex items-center gap-2 bg-white border border-slate-200 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-500 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
            Reçues : <span class="text-slate-900 ml-1 font-black">{{ applications.length }}</span>
          </div>
          
          <button @click="getData" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors flex items-center gap-1.5 bg-white hover:bg-slate-50 px-3.5 py-2.5 rounded-xl border border-slate-200 shadow-sm">
            <i class="fas fa-sync-alt" :class="{'animate-spin': loading}"></i> 
            <span>Rafraîchir</span>
          </button>
        </div>
      </div>

      <!-- Message Erreur -->
      <div v-if="error" class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm font-semibold flex items-center gap-2.5">
        <i class="fas fa-exclamation-circle text-rose-500"></i>
        <span>{{ error }}</span>
      </div>

      <!-- 📋 CONTAINER TABLEAU -->
      <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden flex flex-col">
        
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white">
          <h2 class="font-bold text-slate-900 text-sm tracking-tight flex items-center gap-2">
            <i class="fas fa-users text-indigo-500"></i> Liste des candidats postulants
          </h2>
        </div>

        <div class="overflow-x-auto w-full">
          <table class="w-full table-fixed border-collapse">
            <thead>
              <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-5 py-4 text-left w-[28%]">Nom Complet</th>
                <th class="px-5 py-4 text-left w-[22%]">Adresse Email</th>
                <th class="px-5 py-4 text-left w-[18%]">Téléphone</th>
                <th class="px-5 py-4 text-center w-[12%]">Dossier CV</th>
                <th class="px-5 py-4 text-center w-[12%]">Statut</th>
                <th class="px-5 py-4 text-center w-[8%]">Actions</th>
              </tr>
            </thead>
            
            <tbody class="divide-y divide-slate-100">
              <!-- Loading Skeleton -->
              <template v-if="loading">
                <tr v-for="n in 3" :key="n" class="animate-pulse">
                  <td class="px-5 py-4" v-for="i in 6" :key="i">
                    <div class="h-4 bg-slate-100 rounded w-full my-1"></div>
                  </td>
                </tr>
              </template>

              <!-- Data row -->
              <template v-else-if="applications.length">
                <tr v-for="app in applications" :key="app.id" class="hover:bg-slate-50/50 transition-colors group">
                  
                  <!-- Candidat (Avatar + Nom) -->
                  <td class="px-5 py-4 font-bold text-slate-800 text-sm">
                    <div class="flex items-center gap-3 min-w-0">
                      <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xs shrink-0">
                        {{ getInitials(app.full_name) }}
                      </div>
                      <div class="truncate">
                        <!-- Clic pour voir la Cover Letter -->
                        <button 
                          @click="selected = app" 
                          class="font-black text-slate-900 hover:text-indigo-600 transition-colors text-xs uppercase tracking-tight block truncate text-left"
                        >
                          {{ app.full_name }}
                        </button>
                        <span class="text-[9px] text-slate-400 font-bold tracking-widest block uppercase mt-0.5">Postulant</span>
                      </div>
                    </div>
                  </td>

                  <!-- Email -->
                  <td class="px-5 py-4 text-slate-500 text-xs font-medium">
                    <span class="truncate block" :title="app.email">✉️ {{ app.email }}</span>
                  </td>

                  <!-- Téléphone -->
                  <td class="px-5 py-4 text-slate-500 text-xs font-medium">
                    <span class="truncate block">📞 {{ app.phone || "—" }}</span>
                  </td>

                  <!-- CV PDF Link -->
                  <td class="px-5 py-4 text-center">
                    <a 
                      :href="cvUrl(app.cv_path)" 
                      target="_blank" 
                      rel="noopener"
                      class="inline-flex items-center gap-1.5 bg-slate-50 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 text-slate-600 hover:text-rose-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm"
                    >
                      <i class="far fa-file-pdf text-rose-500 text-sm"></i>
                      <span>Voir CV</span>
                    </a>
                  </td>

                  <!-- Select Dropdown Statut -->
                  <td class="px-5 py-4 text-center">
                    <div class="inline-block relative">
                      <select 
                        :value="app.status" 
                        @change="updateStatus(app, $event.target.value)"
                        class="appearance-none border rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-wider cursor-pointer focus:outline-none focus:ring-4 transition-all pr-8 shadow-sm"
                        :class="getStatusBadgeClass(app.status)"
                      >
                        <option value="pending" class="bg-white text-amber-700">En attente</option>
                        <option value="accepted" class="bg-white text-emerald-700">Acceptée</option>
                        <option value="rejected" class="bg-white text-rose-700">Rejetée</option>
                      </select>
                      <span class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 text-[10px]">
                        <i class="fas fa-chevron-down"></i>
                      </span>
                    </div>
                  </td>

                  <!-- Delete -->
                  <td class="px-5 py-4 text-center">
                    <button 
                      @click="confirmDelete(app)" 
                      class="w-8 h-8 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 text-rose-500 rounded-xl flex items-center justify-center transition-all shadow-sm shrink-0 mx-auto active:scale-95"
                      title="Supprimer la candidature"
                    >
                      <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                  </td>

                </tr>
              </template>

              <!-- Empty state -->
              <tr v-else>
                <td colspan="6" class="p-16 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/10">
                  <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-3">
                    <i class="far fa-folder-open text-lg"></i>
                  </div>
                  Aucune candidature reçue pour cette offre pour l'instant.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>

    <!-- 🟦 MODAL DETAILS (COVER LETTER) -->
    <transition name="modal">
      <div v-if="selected" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="selected = null">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full p-6 flex flex-col max-h-[90vh]">
          
          <!-- Header -->
          <div class="flex items-start justify-between pb-4 border-b border-slate-100">
            <div>
              <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-md tracking-wider uppercase">Dossier Candidat</span>
              <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight mt-1.5">{{ selected.full_name }}</h2>
              <div class="flex flex-col gap-0.5 mt-1 text-xs text-slate-400 font-medium">
                <span>✉️ {{ selected.email }}</span>
                <span v-if="selected.phone">📞 {{ selected.phone }}</span>
              </div>
            </div>
            <button @click="selected = null" class="text-slate-400 hover:text-slate-600 p-1 transition-colors">
              <i class="fas fa-times text-base"></i>
            </button>
          </div>

          <!-- Body -->
          <div class="py-5 overflow-y-auto max-h-[50vh] flex-1">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2.5">Lettre de motivation :</h3>
            <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 border border-slate-100 rounded-xl p-4 whitespace-pre-wrap font-medium">
              {{ selected.cover_letter || "Aucune lettre de motivation fournie par le candidat." }}
            </p>
          </div>

          <!-- Footer -->
          <div class="pt-4 border-t border-slate-100 flex justify-end gap-2 shrink-0">
            <a 
              :href="cvUrl(selected.cv_path)" 
              target="_blank" 
              class="text-xs font-bold bg-slate-50 border border-slate-200 text-slate-600 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all flex items-center gap-1.5"
            >
              <i class="far fa-file-pdf text-rose-500"></i>
              <span>Ouvrir le CV</span>
            </a>
            
            <button 
              @click="selected = null" 
              class="text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl shadow-md shadow-indigo-600/15 transition-all active:scale-95"
            >
              Fermer
            </button>
          </div>

        </div>
      </div>
    </transition>

  </div>
</template>

<style scoped>
/* Modal animation fade in */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.25s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>