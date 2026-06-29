<script setup>
import { useRouter } from "vue-router";

const router = useRouter();
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
        <a href="#contact" class="hover:text-blue-600 transition-colors">Contact & Audit</a>
        
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
          Ingénierie & Architecture Data
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
            href="#contact"
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

    <section id="expertise" class="max-w-7xl mx-auto px-6 lg:px-16 pb-28">
      <div class="text-center max-w-xl mx-auto mb-16 space-y-2">
        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">Notre savoir-faire</span>
        <h3 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 uppercase">Enterprise Data Services</h3>
        <p class="text-slate-400 text-xs font-medium">Modernisation end-to-end adaptée aux stacks de production critiques.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white border border-slate-200/60 p-6 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between group hover:border-blue-500/40 hover:shadow-[0_15px_40px_rgba(37,99,235,0.05)] transition-all duration-300">
          <div>
            <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-center mb-5 group-hover:bg-blue-50 group-hover:border-blue-100 transition-colors">
              <svg class="w-5 h-5 text-slate-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.128l1.41-.513M5.106 17.785l1.15-.827m11.379-8.16l1.15-.827M8.14 21.27l.707-1.03m10.304-6.117l.707-1.03M12 3v1.5M12 19.5V21M9.143 3.73l.707 1.03M11.414 12h.008v.008H11.414V12zm0-3h.008v.008H11.414V9zm0 6h.008v.008H11.414V15z"></path></svg>
            </div>
            <h4 class="font-extrabold text-slate-900 text-sm uppercase tracking-tight mb-2">Data Integration</h4>
            <p class="text-slate-400 text-xs font-medium leading-relaxed">
              Pipelines ETL/ELT robustes et streaming temps réel. Connexion unifiée de vos silos applicatifs via Talend et architectures managées.
            </p>
          </div>
          <span class="text-[10px] font-bold text-blue-600/0 group-hover:text-blue-600/100 transition-all pt-4 block font-mono">ETL / Streaming &rarr;</span>
        </div>

        <div class="bg-white border border-slate-200/60 p-6 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between group hover:border-blue-500/40 hover:shadow-[0_15px_40px_rgba(37,99,235,0.05)] transition-all duration-300">
          <div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100/50 flex items-center justify-center mb-5">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z"></path></svg>
            </div>
            <h4 class="font-extrabold text-slate-900 text-sm uppercase tracking-tight mb-2">Cloud Architecture</h4>
            <p class="text-slate-400 text-xs font-medium leading-relaxed">
              Conception et optimisation sur Snowflake, AWS et BigQuery. Modélisations scalables pensées pour les requêtes analytiques massives.
            </p>
          </div>
          <span class="text-[10px] font-bold text-blue-600/100 pt-4 block font-mono">Snowflake & AWS &rarr;</span>
        </div>

        <div class="bg-white border border-slate-200/60 p-6 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between group hover:border-blue-500/40 hover:shadow-[0_15px_40px_rgba(37,99,235,0.05)] transition-all duration-300">
          <div>
            <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-center mb-5 group-hover:bg-blue-50 group-hover:border-blue-100 transition-colors">
              <svg class="w-5 h-5 text-slate-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"></path></svg>
            </div>
            <h4 class="font-extrabold text-slate-900 text-sm uppercase tracking-tight mb-2">Data Governance</h4>
            <p class="text-slate-400 text-xs font-medium leading-relaxed">
              Mise en conformité complète, audits de qualité de données et gestion fine des catalogues pour sécuriser les métadonnées de l'entreprise.
            </p>
          </div>
          <span class="text-[10px] font-bold text-blue-600/0 group-hover:text-blue-600/100 transition-all pt-4 block font-mono">Security / Lineage &rarr;</span>
        </div>

        <div class="bg-white border border-slate-200/60 p-6 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.01)] flex flex-col justify-between group hover:border-blue-500/40 hover:shadow-[0_15px_40px_rgba(37,99,235,0.05)] transition-all duration-300">
          <div>
            <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-center mb-5 group-hover:bg-blue-50 group-hover:border-blue-100 transition-colors">
              <svg class="w-5 h-5 text-slate-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"></path></svg>
            </div>
            <h4 class="font-extrabold text-slate-900 text-sm uppercase tracking-tight mb-2">BI & Analytics</h4>
            <p class="text-slate-400 text-xs font-medium leading-relaxed">
              Transformation de bases de données brutes en dashboards décisionnels interactifs pour guider le pilotage stratégique de votre direction.
            </p>
          </div>
          <span class="text-[10px] font-bold text-blue-600/0 group-hover:text-blue-600/100 transition-all pt-4 block font-mono">Dashboards / KPI &rarr;</span>
        </div>

      </div>
    </section>

    <section id="contact" class="max-w-4xl mx-auto px-6 pb-28">
      <div class="bg-white p-8 sm:p-10 rounded-2xl shadow-[0_20px_50px_rgba(15,23,42,0.02)] border border-slate-200/60 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-500 to-sky-400"></div>
        
        <div class="text-center max-w-xl mx-auto mb-10 space-y-1.5">
          <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Initier votre Audit Architecture</h3>
          <p class="text-slate-400 text-xs font-medium">Échangez avec nos ingénieurs Cloud à propos de vos pipelines de production.</p>
        </div>

        <form @submit.prevent class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email Entreprise</label>
            <input
              type="email"
              class="w-full border border-slate-200 px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white bg-slate-50 text-xs font-semibold transition-all shadow-2xs"
              placeholder="nom@entreprise.com"
            />
          </div>

          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stack Technique Principale</label>
            <input
              type="text"
              class="w-full border border-slate-200 px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white bg-slate-50 text-xs font-semibold transition-all shadow-2xs"
              placeholder="Ex: Snowflake, Talend, AWS"
            />
          </div>

          <div class="sm:col-span-2 space-y-1.5">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Périmètre du Projet / Besoins</label>
            <textarea
              class="w-full border border-slate-200 px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white bg-slate-50 text-xs font-semibold transition-all shadow-2xs resize-none"
              rows="4"
              placeholder="Décrivez brièvement vos problématiques d'intégration ou vos enjeux de migration..."
            ></textarea>
          </div>

          <button
            type="submit"
            class="sm:col-span-2 bg-slate-900 hover:bg-blue-600 text-white font-bold p-3 rounded-xl shadow-md transition-all text-xs uppercase tracking-wider mt-2"
          >
            Demander une consultation gratuite
          </button>
        </form>
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
            <a href="#contact" class="hover:text-white transition-colors">&middot; Demande Audit</a>
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

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>