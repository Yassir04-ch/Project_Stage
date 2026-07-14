<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import jobOfferService from "@/services/jobOfferService";
import applicationService from "@/services/applicationService";

const route = useRoute();
const router = useRouter();

const offer = ref(null);
const loading = ref(true);
const submitting = ref(false);
const success = ref(false);
const errors = ref({});
const cvFile = ref(null);
const isDragging = ref(false);

const form = reactive({
  full_name: "",
  email: "",
  phone: "",
  cover_letter: "",
});

const fetchOffer = async () => {
  loading.value = true;
  try {
    const res = await jobOfferService.getOffer(route.params.id);
    offer.value = res.data.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchOffer);

// Drag & Drop handlers
const onDragOver = (e) => {
  e.preventDefault();
  isDragging.value = true;
};

const onDragLeave = () => {
  isDragging.value = false;
};

const onDrop = (e) => {
  e.preventDefault();
  isDragging.value = false;
  const files = e.dataTransfer.files;
  if (files.length) {
    validateAndSetFile(files[0]);
  }
};

const validateAndSetFile = (file) => {
  const allowedTypes = [
    "application/pdf", 
    "application/msword", 
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
  ];
  
  if (!allowedTypes.includes(file.type)) {
    errors.value.cv = ["Veuillez joindre uniquement un fichier PDF ou Word (.doc, .docx)."];
    cvFile.value = null;
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    errors.value.cv = ["Le fichier est trop volumineux (max 5 Mo)."];
    cvFile.value = null;
    return;
  }

  errors.value.cv = null;
  cvFile.value = file;
};

const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) validateAndSetFile(file);
};

const removeFile = () => {
  cvFile.value = null;
  if (errors.value.cv) errors.value.cv = null;
};

const formatSize = (bytes) => {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Octets", "Ko", "Mo"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i];
};

const formatSalary = (min, max) => {
  if (!min && !max) return null;
  if (min && max) return `${Number(min).toLocaleString("fr-FR")} - ${Number(max).toLocaleString("fr-FR")} MAD`;
  return `${Number(min || max).toLocaleString("fr-FR")} MAD`;
};

const submitApplication = async () => {
  errors.value = {};

  if (!cvFile.value) {
    errors.value.cv = ["Veuillez joindre votre CV (PDF ou Word)."];
    return;
  }

  submitting.value = true;
  try {
    const data = new FormData();
    data.append("full_name", form.full_name);
    data.append("email", form.email);
    if (form.phone) data.append("phone", form.phone);
    if (form.cover_letter) data.append("cover_letter", form.cover_letter);
    data.append("cv", cvFile.value);

    await applicationService.apply(route.params.id, data);
    success.value = true;
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
    } else {
      alert("Une erreur est survenue lors de l'envoi de votre candidature.");
    }
  } finally {
    submitting.value = false;
  }
};
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <div class="min-h-screen bg-slate-50/50 text-slate-800 font-sans antialiased">
    
    <!-- NAVBAR (Indeed Style) -->
    <nav class="sticky top-0 bg-white border-b border-slate-200/80 backdrop-blur-md bg-opacity-95 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3 cursor-pointer group" @click="router.push('/')">
          <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black text-sm tracking-wider shadow-sm transition-transform group-hover:scale-105">
            DX
          </div>
          <span class="text-md font-bold tracking-tight text-slate-950">
            Data<span class="text-blue-600 font-black">Xpress</span>
          </span>
        </div>
        <button
          @click="router.push('/carrieres')"
          class="flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition-colors bg-slate-100 hover:bg-blue-50 px-3.5 py-2 rounded-lg"
        >
          <i class="fa-solid fa-arrow-left"></i> Retour aux emplois
        </button>
      </div>
    </nav>

    <!-- MAIN BODY -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- LOADING STATE -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-32">
        <div class="w-12 h-12 border-4 border-slate-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
        <p class="text-slate-500 font-semibold text-sm">Chargement de l'offre d'emploi...</p>
      </div>

      <!-- NOT FOUND STATE -->
      <div v-else-if="!offer" class="max-w-xl mx-auto text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-sm px-6">
        <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fa-solid fa-circle-exclamation text-2xl"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-950 mb-1">Offre indisponible</h3>
        <p class="text-slate-500 text-sm mb-6">Cette offre n'existe plus ou a été clôturée par le recruteur.</p>
        <button @click="router.push('/carrieres')" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
          Voir d'autres offres
        </button>
      </div>

      <!-- MAIN CONTENT GRID -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: JOB DETAILS (Col: 7/12) -->
        <section class="lg:col-span-7 xl:col-span-8 space-y-6">
          
          <!-- Job Brief Card -->
          <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex items-start justify-between gap-4 flex-wrap">
              <div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                  <i class="fa-solid fa-briefcase text-[10px]"></i> {{ offer.contract_type }}
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-950 mt-3 tracking-tight">{{ offer.title }}</h2>
                <p class="text-slate-500 text-sm font-semibold mt-1">Publié par DataXpress</p>
              </div>
            </div>

            <!-- Meta attributes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-b border-slate-100 py-5 my-6">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500">
                  <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                  <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Lieu de travail</p>
                  <p class="text-sm font-semibold text-slate-800">{{ offer.location || "Non spécifié" }}</p>
                </div>
              </div>

              <div class="flex items-center gap-3" v-if="formatSalary(offer.salary_min, offer.salary_max)">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                  <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div>
                  <p class="text-[10px] uppercase tracking-wider font-bold text-emerald-600/70">Salaire proposé</p>
                  <p class="text-sm font-bold text-slate-800">{{ formatSalary(offer.salary_min, offer.salary_max) }}</p>
                </div>
              </div>
            </div>

            <!-- Job Description -->
            <div>
              <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-3">Description du poste</h3>
              <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ offer.description }}</p>
            </div>
          </div>

          <!-- Requirements Card -->
          <div v-if="offer.requirements" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
              <i class="fa-solid fa-circle-check text-blue-600"></i> Profil recherché / Prérequis
            </h3>
            <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ offer.requirements }}</p>
          </div>
        </section>

        <!-- RIGHT COLUMN: APPLY FORM (Col: 5/12 - STICKY ON PC) -->
        <section class="lg:col-span-5 xl:col-span-4 lg:sticky lg:top-24">
          
          <!-- SUCCESS STATE -->
          <div v-if="success" class="bg-white border border-emerald-200 rounded-2xl p-8 text-center shadow-md animate-fade-in">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
              <i class="fa-solid fa-circle-check text-3xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-950 mb-1">Candidature reçue !</h3>
            <p class="text-slate-500 text-xs leading-relaxed mb-6">Merci d'avoir postulé. Notre équipe RH étudiera votre profil avec la plus grande attention.</p>
            <button @click="router.push('/carrieres')" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
              Découvrir d'autres postes
            </button>
          </div>

          <!-- THE APPLICATION FORM -->
          <div v-else class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-md">
            <div class="mb-5 pb-4 border-b border-slate-100">
              <h3 class="text-md font-extrabold text-slate-950">Candidature simplifiée</h3>
              <p class="text-slate-400 text-[11px] font-semibold mt-1">Postulez en moins de 2 minutes</p>
            </div>

            <form @submit.prevent="submitApplication" class="space-y-4">
              <!-- Name Input -->
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Nom complet <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"><i class="fa-solid fa-user"></i></span>
                  <input
                    v-model="form.full_name"
                    type="text"
                    required
                    placeholder="Sarah Chen"
                    :class="['w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50/50 border text-xs font-semibold outline-none transition-all focus:bg-white focus:ring-4',
                      errors.full_name ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-500/10']"
                  />
                </div>
                <p v-if="errors.full_name" class="text-rose-500 text-[10px] font-bold mt-1"><i class="fa-solid fa-circle-info"></i> {{ errors.full_name[0] }}</p>
              </div>

              <!-- Email Input -->
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Adresse e-mail <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"><i class="fa-solid fa-envelope"></i></span>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    placeholder="sarah.chen@mail.com"
                    :class="['w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50/50 border text-xs font-semibold outline-none transition-all focus:bg-white focus:ring-4',
                      errors.email ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-500/10']"
                  />
                </div>
                <p v-if="errors.email" class="text-rose-500 text-[10px] font-bold mt-1"><i class="fa-solid fa-circle-info"></i> {{ errors.email[0] }}</p>
              </div>

              <!-- Phone Input -->
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Téléphone</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"><i class="fa-solid fa-phone"></i></span>
                  <input
                    v-model="form.phone"
                    type="tel"
                    placeholder="+212 6XXXXXXXX"
                    :class="['w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50/50 border text-xs font-semibold outline-none transition-all focus:bg-white focus:ring-4',
                      errors.phone ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-500/10']"
                  />
                </div>
                <p v-if="errors.phone" class="text-rose-500 text-[10px] font-bold mt-1"><i class="fa-solid fa-circle-info"></i> {{ errors.phone[0] }}</p>
              </div>

              <!-- Cover Letter -->
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Message d'accompagnement (optionnel)</label>
                <textarea
                  v-model="form.cover_letter"
                  rows="3"
                  placeholder="Présentez brièvement vos atouts pour ce poste..."
                  class="w-full px-4 py-2.5 rounded-xl bg-slate-50/50 border border-slate-200 text-xs font-semibold outline-none resize-none focus:bg-white focus:ring-4 focus:border-blue-500 focus:ring-blue-500/10"
                ></textarea>
              </div>

              <!-- DRAG AND DROP CV INPUT -->
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Votre CV <span class="text-rose-500">*</span></label>
                
                <div
                  @dragover="onDragOver"
                  @dragleave="onDragLeave"
                  @drop="onDrop"
                  :class="[
                    'relative rounded-2xl border-2 border-dashed p-4 flex flex-col items-center justify-center text-center transition-all cursor-pointer',
                    isDragging ? 'border-blue-500 bg-blue-50/50' : 'border-slate-200 hover:border-blue-500/50 hover:bg-slate-50/50',
                    cvFile ? 'bg-emerald-50/30 border-emerald-400 hover:bg-emerald-50/30' : '',
                    errors.cv ? 'border-rose-400 bg-rose-50/30' : ''
                  ]"
                >
                  <input
                    id="cv-upload"
                    type="file"
                    accept=".pdf,.doc,.docx"
                    @change="onFileChange"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                  />

                  <!-- If file is NOT uploaded -->
                  <div v-if="!cvFile" class="space-y-1.5 pointer-events-none">
                    <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-1">
                      <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                    </div>
                    <p class="text-xs font-extrabold text-slate-800">Glissez-déposez ou parcourez</p>
                    <p class="text-[10px] text-slate-400 font-semibold">PDF, Word (max 5 Mo)</p>
                  </div>

                  <!-- If file IS uploaded -->
                  <div v-else class="flex items-center justify-between w-full z-20">
                    <div class="flex items-center gap-3 text-left">
                      <div class="w-9 h-9 rounded-lg bg-emerald-500 text-white flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-file-pdf text-sm" v-if="cvFile.type === 'application/pdf'"></i>
                        <i class="fa-solid fa-file-word text-sm" v-else></i>
                      </div>
                      <div class="max-w-[150px] sm:max-w-[180px]">
                        <p class="text-xs font-bold text-slate-800 truncate" :title="cvFile.name">{{ cvFile.name }}</p>
                        <p class="text-[10px] text-emerald-600 font-bold">{{ formatSize(cvFile.size) }}</p>
                      </div>
                    </div>
                    <button
                      type="button"
                      @click.stop="removeFile"
                      class="w-7 h-7 rounded-lg bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-500 flex items-center justify-center border border-slate-200 transition-colors shadow-xs"
                      title="Supprimer le fichier"
                    >
                      <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                  </div>
                </div>
                
                <p v-if="errors.cv" class="text-rose-500 text-[10px] font-bold mt-1"><i class="fa-solid fa-circle-info"></i> {{ errors.cv[0] }}</p>
              </div>

              <!-- Submit Button -->
              <button
                type="submit"
                :disabled="submitting"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md shadow-blue-500/10 hover:shadow-lg hover:shadow-blue-500/20 active:scale-[0.99] transition-all duration-200 flex justify-center items-center gap-2"
              >
                <i v-if="!submitting" class="fa-solid fa-paper-plane text-[10px]"></i>
                <i v-else class="fa-solid fa-spinner fa-spin"></i>
                <span>{{ submitting ? "Envoi de la candidature..." : "Postuler maintenant" }}</span>
              </button>
            </form>
          </div>
        </section>

      </div>
    </main>
  </div>
</template>