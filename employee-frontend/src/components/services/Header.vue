<template>
  <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex justify-between items-center mb-8 shadow-sm">
    <div>
      <h1 class="text-2xl font-black text-slate-900 tracking-tight">Gestion des Services</h1>
      <p class="text-xs text-slate-400 font-medium mt-0.5">Organisez vos départements et gérez les équipes.</p>
    </div>
    <button @click="$emit('create')"
      class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider px-4 py-3 rounded-xl shadow-md shadow-indigo-600/15 transition-all flex items-center gap-2 active:scale-95">
      <span>+</span> Nouveau Service
    </button>
  </div>
</template>

<script setup>
defineEmits(["create"]);
</script>