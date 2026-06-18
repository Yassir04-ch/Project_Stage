<script setup>
import { useRouter } from "vue-router";

const router = useRouter();
</script>

<template>
<div class="min-h-screen bg-[#f8fafc] text-[#1e293b] font-sans antialiased relative overflow-x-hidden">
  
  <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1200px] h-[500px] bg-gradient-to-b from-indigo-100/40 via-blue-50/20 to-transparent rounded-full blur-3xl -z-10"></div>

  <nav class="max-w-7xl mx-auto px-6 lg:px-16 py-6 flex justify-between items-center">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-600 flex items-center justify-center text-white font-black text-lg shadow-md shadow-indigo-200">
        DX
      </div>
      <h1 class="text-xl font-bold tracking-tight text-slate-900">
        Data<span class="text-indigo-600">Xpress</span>
      </h1>
    </div>

    <div class="hidden md:flex items-center gap-10 text-[15px] font-medium text-slate-500">
      <a href="#home" class="text-slate-900 font-semibold hover:text-indigo-600 transition-colors">Home</a>
      <a href="#expertise" class="hover:text-indigo-600 transition-colors">Expertise</a>
      <a href="#contact" class="hover:text-indigo-600 transition-colors">Contact</a>
      <button
        @click="router.push('/login')"
        class="bg-slate-200/70 text-slate-700 px-5 py-2 rounded-full hover:bg-slate-200 transition-all font-semibold text-sm"
      >
        Client Portal
      </button>
    </div>
  </nav>

  <header id="home" class="max-w-7xl mx-auto px-6 lg:px-16 pt-12 pb-20 text-center">
    <div class="max-w-3xl mx-auto space-y-6">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 border border-indigo-100/80 text-indigo-700 text-xs font-semibold uppercase tracking-wider mx-auto">
        🚀 Data Integration & Cloud Experts
      </div>
      
      <h2 class="text-4xl sm:text-5xl font-black tracking-tight text-[#0f172a] uppercase leading-tight">
        Accelerate Your<br>
        <span class="bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">Data Maturity</span>
      </h2>
      
      <p class="text-slate-500 text-base sm:text-lg max-w-xl mx-auto leading-relaxed font-normal">
        DATAXPRESS transforms complex enterprise structures into smart, scalable data assets. We help you unify databases and unlock automated intelligence.
      </p>

      <div class="flex items-center justify-center gap-4 pt-2">
        <a
          href="#contact"
          class="bg-[#1e293b] text-white px-7 py-3.5 rounded-full font-semibold text-sm shadow-xl shadow-slate-300 hover:bg-slate-800 transition-all"
        >
          Get Free Audit
        </a>
        <a
          href="#expertise"
          class="bg-white text-slate-800 border border-slate-200 px-7 py-3.5 rounded-full font-semibold text-sm hover:bg-slate-50 transition-all shadow-sm"
        >
          Our Services
        </a>
      </div>
    </div>
  </header>

  <section id="expertise" class="max-w-6xl mx-auto px-6 pb-24">
    <div class="text-center mb-12">
      <h3 class="text-2xl font-bold tracking-tight text-slate-900">Enterprise Data Services</h3>
      <p class="text-slate-400 text-sm mt-1">End-to-end modernization for modern tech stacks.</p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <div class="bg-white/70 backdrop-blur-md p-6 rounded-[2rem] border border-white/80 shadow-[0_20px_50px_rgba(0,0,0,0.03)] flex flex-col group hover:scale-[1.02] hover:bg-white transition-all duration-300">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-xl mb-5 shadow-inner">
          ⚙️
        </div>
        <h4 class="font-bold text-slate-900 text-base mb-2">Data Integration</h4>
        <p class="text-slate-500 text-xs leading-relaxed">
          Robust ETL/ELT streaming pipelines. Connect siloed applications smoothly using Talend & automated workflows.
        </p>
      </div>

      <div class="bg-white/70 backdrop-blur-md p-6 rounded-[2rem] border border-white/80 shadow-[0_20px_50px_rgba(0,0,0,0.03)] flex flex-col group hover:scale-[1.02] hover:bg-white transition-all duration-300">
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mb-5 shadow-sm">
          ☁️
        </div>
        <h4 class="font-bold text-slate-900 text-base mb-2">Cloud Infrastructure</h4>
        <p class="text-slate-500 text-xs leading-relaxed">
          Migration & optimization on Snowflake, AWS, or BigQuery. Scalable frameworks designed for fast analytical queries.
        </p>
      </div>

      <div class="bg-white/70 backdrop-blur-md p-6 rounded-[2rem] border border-white/80 shadow-[0_20px_50px_rgba(0,0,0,0.03)] flex flex-col group hover:scale-[1.02] hover:bg-white transition-all duration-300">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-xl mb-5 shadow-inner">
          🛡️
        </div>
        <h4 class="font-bold text-slate-900 text-base mb-2">Data Governance</h4>
        <p class="text-slate-500 text-xs leading-relaxed">
          Enforce full compliance, strict quality controls, and catalog management to secure corporate metadata.
        </p>
      </div>

      <div class="bg-white/70 backdrop-blur-md p-6 rounded-[2rem] border border-white/80 shadow-[0_20px_50px_rgba(0,0,0,0.03)] flex flex-col group hover:scale-[1.02] hover:bg-white transition-all duration-300">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-xl mb-5 shadow-inner">
          📊
        </div>
        <h4 class="font-bold text-slate-900 text-base mb-2">BI & Analytics</h4>
        <p class="text-slate-500 text-xs leading-relaxed">
          Turn databases into interactive executive dashboards. Power rapid operational decision-making.
        </p>
      </div>

    </div>
  </section>

  <section id="contact" class="max-w-4xl mx-auto px-6 pb-28">
    <div class="bg-white p-8 sm:p-12 rounded-[2.5rem] shadow-[0_30px_70px_rgba(0,0,0,0.04)] border border-slate-100/80">
      <div class="text-center max-w-xl mx-auto mb-8 space-y-2">
        <h3 class="text-2xl font-bold text-slate-900">Start Your Architecture Audit</h3>
        <p class="text-slate-400 text-sm">Consult our cloud engineers about your production pipelines.</p>
      </div>

      <form @submit.prevent class="grid sm:grid-cols-2 gap-5">
        <div class="space-y-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Company Email</label>
          <input
            type="email"
            class="w-full border border-slate-200/80 p-3 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 bg-slate-50 text-sm transition-all"
            placeholder="name@company.com"
          />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Primary Tech Stack</label>
          <input
            type="text"
            class="w-full border border-slate-200/80 p-3 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 bg-slate-50 text-sm transition-all"
            placeholder="e.g., Snowflake, Talend"
          />
        </div>

        <div class="sm:col-span-2 space-y-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Project Scope</label>
          <textarea
            class="w-full border border-slate-200/80 p-3 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 bg-slate-50 text-sm transition-all"
            rows="3"
            placeholder="Describe your migration or pipeline challenges..."
          ></textarea>
        </div>

        <button
          type="submit"
          class="sm:col-span-2 bg-indigo-600 text-white font-semibold p-3.5 rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-100 transition-all text-sm mt-2"
        >
          Request Free Consultation
        </button>
      </form>
    </div>
  </section>

  <footer class="bg-slate-900 text-slate-400 py-12 text-center px-6 border-t border-slate-800">
    <div class="max-w-7xl mx-auto space-y-3">
      <h4 class="font-extrabold text-white text-xl tracking-tight">
        Data<span class="text-indigo-400">Express</span>
      </h4>
      <p class="italic text-xs text-slate-500">
        "Accélérateur de votre maturité Data."
      </p>
    </div>
  </footer>

</div>
</template>