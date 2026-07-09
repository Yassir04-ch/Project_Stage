<template>
  <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-3" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
    <div v-if="flash.visible" class="fixed top-5 right-5 z-50 flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-semibold min-w-[280px]"
      :class="flash.type === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'">
      <span>{{ flash.type === 'success' ? '✅' : '❌' }}</span>
      <span class="flex-1">{{ flash.message }}</span>
      <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600">✕</button>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  flash: {
    type: Object,
    required: true,
  },
});

defineEmits(["close"]);
</script>