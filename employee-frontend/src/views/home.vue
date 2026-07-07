<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/api/axios"; // Assure-toi que had l-path s7i7 integration dial axial 3ndek

const router = useRouter();
const services = ref([]);
const loading = ref(true);

const loadServices = async () => {
  try {
    const res = await api.get("/services");
    // Hna ila l-api dial public makay7tajch token, rani khllito 3adi
    services.value = res.data.services || [];
  } catch (err) {
    console.error("Erreur lors du chargement des services:", err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadServices();
});
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 font-sans antialiased relative overflow-x-hidden selection:bg-blue-600 selection:text-white">
    
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1400px] h-[600px] bg-gradient-to-b from-blue-100/40 via-sky-50/10 to-transparent rounded-full blur-3xl -z-10"></div>
    <div class="absolute top-[400px] right-0 w-[300px] h-[300px] bg-sky-100/30 rounded-full blur-3xl -z-10"></div>

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
        <a href="#home" class="text-slate-900 hover:text-blue-600 transition-colors">Accueil</a>
        <a href="#expertise" class="hover:text-blue-600 transition-colors">Expertise</a>
        <a href="/contacts" class="hover:text-blue-600 transition-colors">Contact & Audit</a>
        
        <button
          @click="router.push('/login')"
          class="bg-white text-slate-700 border border-slate-200/80 px-4.5 py-2 rounded-xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all font-bold text-xs uppercase tracking-wider shadow-2xs">
          Connexion Employé
        </button>
      </div>
    </nav>

    <header id="home" class="max-w-7xl mx-auto px-6 lg:px-16 pt-20 pb-24 text-center relative">
      <div class="max-w-3xl mx-auto space-y-6">
        
        <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-100/80 px-3 py-1 rounded-full text-[11px] font-bold text-blue-700 uppercase tracking-widest mx-auto shadow-2xs animate-fadeIn">
          <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></span>
          <span class="font-extrabold">Ingénierie & Architecture Data</span>
        </div>

        <h2 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 uppercase leading-[1.15]">
          Accelerate Your<br>
          <span class="bg-gradient-to-r from-blue-600 to-sky-500 bg-clip-text text-transparent">Data Maturity</span>
        </h2>
        
        <p class="text-slate-500 text-sm sm:text-base max-w-xl mx-auto leading-relaxed font-medium">
          DATAXPRESS orchestre et modernise vos écosystèmes complexes en pipelines analytiques fluides, sécurisés et hautement performants.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 pt-4">
          <a
            href="/contacts"
            class="w-full sm:w-auto bg-slate-900 text-white px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:bg-blue-600 transition-all text-center"
          >
            Demander un Audit Gratuit
          </a>
          <a
            href="#expertise"
            class="w-full sm:w-auto bg-white text-slate-700 border border-slate-200/80 px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 transition-all shadow-2xs text-center"
          >
            Découvrir nos Services
          </a>
        </div>
      </div>
    </header>

    <!-- SECTION DYNAMIC EXPERTISE -->
    <section id="expertise" class="max-w-7xl mx-auto px-6 lg:px-16 pb-28">
      <div class="text-center max-w-xl mx-auto mb-16 space-y-2">
        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">Notre savoir-faire</span>
        <h3 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 uppercase">Enterprise Data Services</h3>
        <p class="text-slate-400 text-xs font-medium">Modernisation end-to-end adaptée aux stacks de production critiques.</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-20 space-y-4">
        <div class="w-9 h-9 border-3 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Chargement des services de l'entreprise...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="services.length === 0" class="text-center py-16 bg-white border border-slate-200/60 rounded-2xl p-8 max-w-md mx-auto">
        <p class="text-sm font-bold text-slate-400">Aucun service disponible pour le moment.</p>
      </div>

      <!-- Services Grid list from API -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        <div v-for="service in services" :key="service.id" 
          class="bg-white border border-slate-200/60 p-6 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between group hover:border-blue-500/40 hover:shadow-[0_15px_40px_rgba(37,99,235,0.05)] transition-all duration-300">
          <div>
            <!-- Header Icon block inside dynamic loop -->
            <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-center mb-5 group-hover:bg-blue-50 group-hover:border-blue-100 transition-colors">
              <svg class="w-5 h-5 text-slate-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            
            <h4 class="font-extrabold text-slate-900 text-sm uppercase tracking-tight mb-2">{{ service.name }}</h4>
            <p class="text-slate-400 text-xs font-medium leading-relaxed">
              {{ service.description || "Pas de description fournie pour ce département d'ingénierie." }}
            </p>
          </div>
          
          <div class="flex items-center justify-between pt-5 mt-4 border-t border-slate-50">
            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider font-mono">Expertise active &rarr;</span>
            <span v-if="service.employees_count || service.employees?.length" class="text-[10px] bg-slate-100 text-slate-500 font-extrabold px-2 py-0.5 rounded-md uppercase">
              {{ service.employees_count ?? service.employees?.length }} Consultants
            </span>
          </div>
        </div>

      </div>
    </section>

    <footer class="bg-slate-900 text-slate-400 py-16 px-8 border-t border-slate-800">
      <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 text-left">
        
        <div class="space-y-4">
          <h4 class="font-black text-white text-xl tracking-tight uppercase">
            Data<span class="text-blue-400">Express</span>
          </h4>
          <p class="text-slate-400 text-xs leading-relaxed max-w-xs font-medium">
            Transformation d'écosystèmes complexes en architectures analytics de haute vélocité. Intégration cloud, data streaming, et intelligence de données.
          </p>
          <p class="italic text-[11px] text-blue-400/90 font-bold tracking-wide">
            "Accélérateur de votre maturité Data."
          </p>
        </div>

        <div class="space-y-4">
          <h5 class="text-white text-[11px] font-black tracking-widest uppercase text-slate-300">Siège Social & Contact</h5>
          <ul class="space-y-3 text-xs font-medium">
            <li class="flex items-start gap-2.5">
              <span class="opacity-70 text-sm">📍</span>
              <span class="leading-relaxed">Casablanca Nearshore Park, Shore 7,<br>Boulevard Al Qods, Sidi Maârouf, Maroc</span>
            </li>
            <li class="flex items-center gap-2.5">
              <span class="opacity-70 text-sm">📞</span>
              <span class="font-mono text-slate-300">+212 (0) 522 45 89 00</span>
            </li>
            <li class="flex items-center gap-2.5">
              <span class="opacity-70 text-sm">✉️</span>
              <span class="font-mono text-blue-300">contact@dataexpress.ma</span>
            </li>
          </ul>
        </div>

        <div class="space-y-4">
          <h5 class="text-white text-[11px] font-black tracking-widest uppercase text-slate-300">Navigation Rapide</h5>
          <div class="grid grid-cols-2 gap-3 text-xs font-bold uppercase tracking-tight">
            <a href="#home" class="hover:text-white transition-colors">&middot; Accueil Hub</a>
            <a href="#expertise" class="hover:text-white transition-colors">&middot; Services</a>
            <a href="/contacts" class="hover:text-white transition-colors">&middot; Demande Audit</a>
            <span @click="router.push('/login')" class="hover:text-white transition-colors cursor-pointer">&middot; Espace Employé</span>
          </div>
        </div>

      </div>

      <div class="max-w-7xl mx-auto mt-12 pt-6 border-t border-slate-800/60 flex flex-col sm:flex-row justify-between items-center gap-4 text-[11px] text-slate-500 font-bold uppercase tracking-wider">
        <p>&copy; 2026 DataXpress S.A. Tous droits réservés.</p>
        <div class="flex gap-6">
          <a href="#" class="hover:text-slate-400 transition-colors">Mentions Légales</a>
          <a href="#" class="hover:text-slate-400 transition-colors">Confidentialité</a>
        </div>
      </div>
    </footer>

    <a 
      href="https://wa.me/212522458900" 
      target="_blank" 
      rel="noopener noreferrer"
      class="fixed bottom-6 right-6 z-50 bg-[#25D366] hover:bg-[#20ba56] text-white p-3.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 flex items-center justify-center group"
      title="Contactez-nous sur WhatsApp"
    >
      <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.713-1.457L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.852.002-2.632-1.023-5.105-2.887-6.971C16.581 1.964 14.12 .939 11.5 1.05 6.07 1.05 1.642 5.47 1.638 10.902c-.001 1.705.463 3.372 1.343 4.849l-.997 3.639 3.738-.98c1.479.807 3.037 1.233 4.671 1.235z"></path>
      </svg>
    </a>

  </div>
</template>