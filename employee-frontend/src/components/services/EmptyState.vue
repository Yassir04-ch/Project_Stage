<template>
  <div v-if="variant === 'no-services'" class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
    <i class="fas fa-building text-2xl text-slate-200 mb-2 block"></i>
    <p class="text-xs text-slate-400 font-medium">Aucun service créé.</p>
  </div>

  <div v-else-if="variant === 'no-selection'" class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
    <i class="fas fa-building text-4xl text-slate-200 mb-4 block"></i>
    <p class="text-sm font-bold text-slate-400">Sélectionnez un service pour voir les détails.</p>
  </div>

  <div v-else-if="variant === 'no-members'" class="p-14 text-center">
    <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-300">
      <i class="fas fa-users text-xl"></i>
    </div>
    <p class="text-xs text-slate-400 font-semibold">Aucun membre n'est affecté à ce service pour le moment.</p>
  </div>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    required: true,
  },
});
</script>