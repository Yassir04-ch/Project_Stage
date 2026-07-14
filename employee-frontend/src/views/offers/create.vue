<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import jobOfferService from "@/services/jobOfferService";

const route = useRoute();
const router = useRouter();

const isEdit = computed(() => !!route.params.id);
const loading = ref(isEdit.value);
const submitting = ref(false);
const submitError = ref("");
const errors = ref({});

const form = reactive({
  title: "",
  description: "",
  requirements: "",
  location: "",
  contract_type: "",
  salary_min: null,
  salary_max: null,
  status: "open",
  deadline: "",
});

const loadOffer = async () => {
  try {
    const res = await jobOfferService.getOffer(route.params.id);
    const offer = res.data.data;
    Object.assign(form, {
      title: offer.title,
      description: offer.description,
      requirements: offer.requirements,
      location: offer.location,
      contract_type: offer.contract_type,
      salary_min: offer.salary_min,
      salary_max: offer.salary_max,
      status: offer.status,
      deadline: offer.deadline ? offer.deadline.substring(0, 10) : "",
    });
  } catch (e) {
    submitError.value = "Impossible de charger cette offre.";
  } finally {
    loading.value = false;
  }
};

const submitForm = async () => {
  submitting.value = true;
  submitError.value = "";
  errors.value = {};
  try {
    if (isEdit.value) {
      await jobOfferService.updateOffer(route.params.id, form);
    } else {
      await jobOfferService.createOffer(form);
    }
    router.push({ name: "offers.manage" });
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors || {};
      submitError.value = "Merci de corriger les champs indiqués en rouge.";
    } else {
      submitError.value = "L'enregistrement a échoué. Veuillez réessayer.";
    }
  } finally {
    submitting.value = false;
  }
};

onMounted(() => {
  if (isEdit.value) loadOffer();
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 text-slate-700 font-sans py-10 px-4 sm:px-6 flex justify-center w-full">
    <div class="max-w-4xl w-full flex flex-col gap-6">
      
      <!-- HEADER -->
      <div class="flex flex-col md:flex-row md:items-start gap-4 mb-2">
        <button 
          @click="router.push({ name: 'offers.manage' })" 
          class="group w-fit flex items-center gap-2 text-xs font-bold bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 px-4 py-2.5 rounded-xl shadow-sm transition-all active:scale-95"
        >
          <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
          <span>Retour aux offres</span>
        </button>
        
        <div class="md:ml-2">
          <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
            <span v-if="isEdit" class="text-indigo-600"><i class="fas fa-pen-to-square"></i></span>
            <span v-else class="text-emerald-500"><i class="fas fa-plus-circle"></i></span>
            {{ isEdit ? "Modifier l'offre d'emploi" : "Nouvelle offre d'emploi" }}
          </h1>
          <p class="text-xs text-slate-500 font-medium mt-1">
            {{ isEdit ? "Mettez à jour les détails de cette offre." : "Remplissez les informations pour publier une opportunité." }}
          </p>
        </div>
      </div>

      <!-- ERROR ALERT -->
      <div v-if="submitError" class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm font-bold flex items-center gap-3 shadow-sm">
        <i class="fas fa-exclamation-triangle text-rose-500 text-lg"></i>
        <span>{{ submitError }}</span>
      </div>

      <!-- FORM CARD -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden relative">
        
        <!-- Loading Overlay -->
        <div v-if="loading" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
          <div class="w-10 h-10 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mb-3"></div>
          <span class="text-sm font-bold text-slate-500 animate-pulse">Chargement des données...</span>
        </div>

        <form @submit.prevent="submitForm" class="p-6 sm:p-8 flex flex-col gap-6" :class="{'opacity-50 pointer-events-none': loading}">
          
          <!-- Titre -->
          <div class="flex flex-col gap-1.5">
            <label for="title" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
              <i class="fas fa-briefcase text-slate-400 w-4"></i> Titre du poste <span class="text-rose-500">*</span>
            </label>
            <input 
              id="title" 
              v-model="form.title" 
              type="text" 
              required 
              placeholder="ex: Développeur Fullstack Vue/Laravel"
              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none placeholder:text-slate-400"
              :class="{'border-rose-400 bg-rose-50 focus:border-rose-500 focus:ring-rose-500/10': errors.title}"
            />
            <span v-if="errors.title" class="text-rose-600 text-xs font-bold mt-1 flex items-center gap-1"><i class="fas fa-info-circle"></i> {{ errors.title[0] }}</span>
          </div>

          <!-- Description -->
          <div class="flex flex-col gap-1.5">
            <label for="description" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
              <i class="fas fa-align-left text-slate-400 w-4"></i> Description *
            </label>
            <textarea 
              id="description" 
              v-model="form.description" 
              rows="5" 
              required
              placeholder="Décrivez les missions et le contexte..."
              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-y placeholder:text-slate-400 min-h-[120px]"
              :class="{'border-rose-400 bg-rose-50': errors.description}"
            ></textarea>
            <span v-if="errors.description" class="text-rose-600 text-xs font-bold mt-1 flex items-center gap-1"><i class="fas fa-info-circle"></i> {{ errors.description[0] }}</span>
          </div>

          <!-- Prérequis -->
          <div class="flex flex-col gap-1.5">
            <label for="requirements" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
              <i class="fas fa-list-check text-slate-400 w-4"></i> Prérequis & Compétences
            </label>
            <textarea 
              id="requirements" 
              v-model="form.requirements" 
              rows="4"
              placeholder="ex: 3 ans d'expérience..."
              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-y placeholder:text-slate-400 min-h-[100px]"
            ></textarea>
          </div>

          <!-- Lieu & Contrat -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-1.5">
              <label for="location" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <i class="fas fa-map-marker-alt text-slate-400 w-4"></i> Lieu de travail
              </label>
              <input 
                id="location" 
                v-model="form.location" 
                type="text" 
                placeholder="ex: Casablanca, Hybride"
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none placeholder:text-slate-400"
              />
            </div>

            <div class="flex flex-col gap-1.5">
              <label for="contract_type" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <i class="fas fa-file-signature text-slate-400 w-4"></i> Type de contrat
              </label>
              <div class="relative">
                <select 
                  id="contract_type" 
                  v-model="form.contract_type"
                  class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none cursor-pointer"
                >
                  <option value="" disabled>Sélectionner</option>
                  <option value="CDI">CDI</option>
                  <option value="CDD">CDD</option>
                  <option value="Stage">Stage / PFE</option>
                  <option value="Freelance">Freelance</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
              </div>
            </div>
          </div>

          <!-- Salaires (Row Highlighté b Tailwind) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 border border-slate-100 p-5 rounded-2xl">
            <div class="flex flex-col gap-1.5">
              <label for="salary_min" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <i class="fas fa-money-bill-wave text-slate-400 w-4"></i> Salaire min (MAD)
              </label>
              <input 
                id="salary_min" 
                v-model.number="form.salary_min" 
                type="number" 
                min="0" 
                placeholder="ex: 8000"
                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none placeholder:text-slate-400"
              />
            </div>

            <div class="flex flex-col gap-1.5">
              <label for="salary_max" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <i class="fas fa-money-bill-wave text-slate-400 w-4"></i> Salaire max (MAD)
              </label>
              <input 
                id="salary_max" 
                v-model.number="form.salary_max" 
                type="number" 
                min="0" 
                placeholder="ex: 12000"
                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none placeholder:text-slate-400"
                :class="{'border-rose-400 bg-rose-50': errors.salary_max}"
              />
              <span v-if="errors.salary_max" class="text-rose-600 text-xs font-bold mt-1">{{ errors.salary_max[0] }}</span>
            </div>
          </div>

          <!-- Statut & Date -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-1.5">
              <label for="status" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <i class="fas fa-toggle-on text-slate-400 w-4"></i> Statut de l'offre
              </label>
              <div class="relative">
                <select 
                  id="status" 
                  v-model="form.status"
                  class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none cursor-pointer"
                  :class="form.status === 'open' ? 'text-emerald-600' : 'text-slate-500'"
                >
                  <option value="open">🟢 Ouverte (Publiée)</option>
                  <option value="closed">⚪ Fermée (Archivée)</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
              </div>
            </div>

            <div class="flex flex-col gap-1.5">
              <label for="deadline" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <i class="fas fa-calendar-alt text-slate-400 w-4"></i> Date limite
              </label>
              <input 
                id="deadline" 
                v-model="form.deadline" 
                type="date" 
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
              />
            </div>
          </div>

          <!-- LIGNE SEPARATEUR -->
          <hr class="border-slate-100 my-2">

          <!-- BOUTONS -->
          <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <button 
              type="button" 
              class="w-full sm:w-auto px-6 py-3 rounded-xl text-sm font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition-colors shadow-sm" 
              @click="$router.back()"
            >
              Annuler
            </button>
            
            <button 
              type="submit" 
              class="w-full sm:w-auto px-8 py-3 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition-all active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              :disabled="submitting"
            >
              <i v-if="submitting" class="fas fa-spinner animate-spin"></i>
              <i v-else class="fas fa-check"></i>
              <span>{{ submitting ? "Enregistrement en cours..." : isEdit ? "Enregistrer les modifications" : "Publier l'offre" }}</span>
            </button>
          </div>

        </form>
      </div>

    </div>
  </div>
</template>