<script setup>
import { reactive, ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router"; // Ztha 7it kanti mkhdm router.push f nav

const router = useRouter();
const loading = ref(false);
const services = ref([]); // Bach n-stockiw les services li jyin m l-API

const form = reactive({
    name: "",
    email: "",
    telephone: "",
    service_id: "", // Zt hadi bach t-stoki id dial service li khtar user
    subject: "",
    message: "",
});

const errors = ref({});

// Fonction bach n-jibo les services m l-API ghir kat-démarra l-page
const fetchServices = async () => {
    try {
        const response = await axios.get("http://127.0.0.1:8000/api/services");
        // centralisation 3la 7sab structure dial l'API dialek (response.data aw response.data.services)
        services.value = response.data.services || response.data;
    } catch (error) {
        console.error("Erreur lors de la récupération des services :", error);
    }
};

onMounted(() => {
    fetchServices();
});

const submitForm = async () => {
    loading.value = true;
    errors.value = {};

    try {
        await axios.post("http://127.0.0.1:8000/api/contact", form);

        alert("Votre message a été envoyé avec succès.");

        form.name = "";
        form.email = "";
        form.telephone = "";
        form.service_id = "";
        form.subject = "";
        form.message = "";

    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            alert("Une erreur est survenue.");
            console.log(error);
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <div class="min-h-screen bg-slate-50 text-slate-800 font-sans antialiased">
    
    <!-- 1. Barre de Navigation -->
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
        <a href="" class="text-slate-900 hover:text-blue-600 transition-colors">Contact & Audit</a>
        
        <button
          @click="router.push('/login')"
          class="bg-white text-slate-700 border border-slate-200/80 px-4.5 py-2 rounded-xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all font-bold text-xs uppercase tracking-wider shadow-2xs">
          Connexion Employé
        </button>
      </div>
    </nav>

    <!-- 2. Hero Section -->
    <header class="relative bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-900 text-white min-h-[380px] pt-16 pb-32 px-6 lg:px-16 overflow-hidden">
      <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
        
        <!-- Texte de présentation (Gauche) -->
        <div class="lg:col-span-7 space-y-4 max-w-xl">
          <h2 class="text-4xl md:text-5xl font-black tracking-tight leading-tight">
            Contactez-nous
          </h2>
          <p class="text-slate-300 text-base md:text-lg font-normal leading-relaxed opacity-90">
            Une question, une suggestion ou besoin d'assistance ? Notre équipe est à votre disposition pour vous accompagner.
          </p>
        </div>

        <!-- Image d'illustration (Droite) -->
        <div class="lg:col-span-5 hidden lg:block relative h-64 w-full rounded-2xl overflow-hidden shadow-2xl opacity-40 mix-blend-luminosity">
          <img 
            src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=800&q=80" 
            alt="DataXpress Team" 
            class="w-full h-full object-cover"
          />
        </div>
      </div>
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-blue-500/10 via-transparent to-transparent pointer-events-none"></div>
    </header>

    <!-- 3. Section principale en superposition (Overlap) -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 -mt-24 pb-16 relative z-20 space-y-12">
      
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Colonne Gauche : Cartes d'informations -->
        <div class="lg:col-span-7 space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Card 1: Email -->
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 relative group">
              <div class="h-40 w-full rounded-2xl overflow-hidden relative mb-4">
                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=500&q=80" alt="Email Support" class="w-full h-full object-cover" />
                <div class="absolute top-3 left-3 w-9 h-9 rounded-xl bg-white/95 backdrop-blur-xs flex items-center justify-center text-blue-600 shadow-xs">
                  <i class="fa-solid fa-envelope text-sm"></i>
                </div>
              </div>
              <div class="px-1">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Email</h3>
                <a href="mailto:mhomce@dataxpress.ma" class="text-slate-800 font-extrabold text-sm hover:text-blue-600 transition-colors block truncate">
                  mhomce@dataxpress.ma
                </a>
              </div>
            </div>

            <!-- Card 2: Téléphone -->
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 relative group">
              <div class="h-40 w-full rounded-2xl overflow-hidden relative mb-4">
                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=500&q=80" alt="Phone Support" class="w-full h-full object-cover" />
                <div class="absolute top-3 left-3 w-9 h-9 rounded-xl bg-white/95 backdrop-blur-xs flex items-center justify-center text-indigo-600 shadow-xs">
                  <i class="fa-solid fa-phone text-sm"></i>
                </div>
              </div>
              <div class="px-1">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Téléphone</h3>
                <a href="tel:+21266240029" class="text-slate-800 font-extrabold text-sm hover:text-indigo-600 transition-colors block">
                  +212 66240029
                </a>
              </div>
            </div>

            <!-- Card 3: Adresse -->
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 relative group">
              <div class="h-40 w-full rounded-2xl overflow-hidden relative mb-4">
                <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=500&q=80" alt="Office Location" class="w-full h-full object-cover" />
                <div class="absolute top-3 left-3 w-9 h-9 rounded-xl bg-white/95 backdrop-blur-xs flex items-center justify-center text-blue-600 shadow-xs">
                  <i class="fa-solid fa-location-dot text-sm"></i>
                </div>
              </div>
              <div class="px-1">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Adresse</h3>
                <p class="text-slate-800 font-extrabold text-sm">El Jadida, Maroc</p>
              </div>
            </div>

            <!-- Card 4: Horaires -->
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 relative group">
              <div class="h-40 w-full rounded-2xl overflow-hidden relative mb-4">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=500&q=80" alt="Working Hours" class="w-full h-full object-cover" />
                <div class="absolute top-3 left-3 w-9 h-9 rounded-xl bg-white/95 backdrop-blur-xs flex items-center justify-center text-indigo-600 shadow-xs">
                  <i class="fa-solid fa-clock text-sm"></i>
                </div>
              </div>
              <div class="px-1">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Horaires</h3>
                <p class="text-slate-800 font-extrabold text-xs leading-tight">
                  Lun-Ven: 08:30-18:00, <br>Sam: 08:30-13:00
                </p>
              </div>
            </div>

          </div>

          <div class="flex justify-start pt-2">
            <a href="https://maps.app.goo.gl/HH4zAt8QnAACthWUA?g_st=iw" target="_blank" rel="noopener noreferrer" 
               class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-blue-600 text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-sm hover:bg-blue-700 hover:shadow-md transition-all duration-200">
              <i class="fa-solid fa-map-location-dot text-sm"></i>
              Voir sur Google Maps
            </a>
          </div>
        </div>

        <!-- Formulaire de contact -->
        <div class="lg:col-span-5 bg-white rounded-[2rem] p-6 sm:p-8 border border-slate-100 shadow-lg relative overflow-hidden">
          <form @submit.prevent="submitForm" class="space-y-5 relative z-10">

            <!-- Nom complet -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold text-slate-900">
                  Nom complet
                </label>
                <input
                  id="name"
                  type="text"
                  v-model="form.name"
                  placeholder="Sarah Chen"
                  :class="[
                      'w-full px-4 py-3 rounded-xl bg-white border transition-all text-slate-800 font-medium text-sm outline-none focus:ring-2',
                      errors.name ? 'border-red-500 focus:ring-red-100 focus:border-red-500' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                  ]"
                />
                <p v-if="errors.name" class="text-red-500 text-xs font-medium">{{ errors.name[0] }}</p>
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold text-slate-900">
                  Adresse e-mail
                </label>
                <input
                  id="email"
                  type="email"
                  v-model="form.email"
                  placeholder="sarah.chen@mail.com"
                  :class="[
                      'w-full px-4 py-3 rounded-xl bg-white border transition-all text-slate-800 font-medium text-sm outline-none focus:ring-2',
                      errors.email ? 'border-red-500 focus:ring-red-100 focus:border-red-500' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                  ]"
                />
                <p v-if="errors.email" class="text-red-500 text-xs font-medium">{{ errors.email[0] }}</p>
            </div>

            <!-- Téléphone -->
            <div class="space-y-1.5">
                <label for="telephone" class="block text-xs font-bold text-slate-900">
                  Téléphone
                </label>
                <input
                  id="telephone"
                  type="tel"
                  v-model="form.telephone"
                  placeholder="+212 6XXXXXXXX"
                  :class="[
                      'w-full px-4 py-3 rounded-xl bg-white border transition-all text-slate-800 font-medium text-sm outline-none focus:ring-2',
                      errors.telephone ? 'border-red-500 focus:ring-red-100 focus:border-red-500' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                  ]"
                />
                <p v-if="errors.telephone" class="text-red-500 text-xs font-medium">{{ errors.telephone[0] }}</p>
            </div>

            <!-- Select Service (Li zt lik jdid) -->
            <div class="space-y-1.5">
                <label for="service" class="block text-xs font-bold text-slate-900">
                  Service concerné
                </label>
                <select
                  id="service"
                  v-model="form.service_id"
                  :class="[
                      'w-full px-4 py-3 rounded-xl bg-white border transition-all text-slate-800 font-medium text-sm outline-none focus:ring-2 appearance-none',
                      errors.service_id ? 'border-red-500 focus:ring-red-100 focus:border-red-500' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                  ]"
                >
                  <option value="" disabled selected>Choisir un service...</option>
                  <option v-for="service in services" :key="service.id" :value="service.id">
                    {{ service.name }}
                  </option>
                </select>
                <p v-if="errors.service_id" class="text-red-500 text-xs font-medium">{{ errors.service_id[0] }}</p>
            </div>

            <!-- Sujet -->
            <div class="space-y-1.5">
                <label for="subject" class="block text-xs font-bold text-slate-900">
                  Sujet
                </label>
                <input
                  id="subject"
                  type="text"
                  v-model="form.subject"
                  placeholder="Demande d'assistance"
                  :class="[
                      'w-full px-4 py-3 rounded-xl bg-white border transition-all text-slate-800 font-medium text-sm outline-none focus:ring-2',
                      errors.subject ? 'border-red-500 focus:ring-red-100 focus:border-red-500' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                  ]"
                />
                <p v-if="errors.subject" class="text-red-500 text-xs font-medium">{{ errors.subject[0] }}</p>
            </div>

            <!-- Message -->
            <div class="space-y-1.5">
                <label for="message" class="block text-xs font-bold text-slate-900">
                  Message
                </label>
                <textarea
                  id="message"
                  rows="5"
                  v-model="form.message"
                  placeholder="Bonjour, je souhaite en savoir plus..."
                  :class="[
                      'w-full px-4 py-3 rounded-xl bg-white border transition-all text-slate-800 font-medium text-sm outline-none resize-none focus:ring-2',
                      errors.message ? 'border-red-500 focus:ring-red-100 focus:border-red-500' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                  ]"
                ></textarea>
                <p v-if="errors.message" class="text-red-500 text-xs font-medium">{{ errors.message[0] }}</p>
            </div>

            <!-- Button -->
            <div class="pt-2 relative">
                <button
                  type="submit"
                  :disabled="loading"
                  class="w-full py-3.5 bg-indigo-950 hover:bg-indigo-900 disabled:bg-slate-400 disabled:cursor-not-allowed text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-md transition-all duration-300 flex justify-center items-center gap-2"
                >
                  <i v-if="!loading" class="fa-solid fa-paper-plane text-[11px]"></i>
                  <i v-else class="fa-solid fa-spinner fa-spin"></i>
                  {{ loading ? 'Envoi...' : 'Envoyer le message' }}
                </button>

                <div class="absolute -bottom-4 -right-2 w-12 h-12 opacity-10 pointer-events-none select-none hidden sm:block">
                  <i class="fa-solid fa-paper-plane text-4xl text-slate-900 rotate-12"></i>
                </div>
            </div>

          </form>
        </div>
      </div>

      <!-- 4. Grande Carte Google Maps -->
      <section class="w-full bg-white rounded-[2rem] shadow-sm overflow-hidden border border-slate-100 p-2">
        <div class="w-full h-[400px] rounded-[1.6rem] overflow-hidden">
          <iframe 
            src="https://maps.google.com/maps?q=El+Jadida,+Maroc&t=&z=13&ie=UTF8&iwloc=&output=embed" 
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade"
            class="opacity-95 hover:opacity-100 transition-opacity duration-300">
          </iframe>
        </div>
      </section>

    </main>
  </div>
</template>