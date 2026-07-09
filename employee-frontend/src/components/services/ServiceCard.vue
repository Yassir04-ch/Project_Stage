<template>
  <div
    @click="$emit('select', service)"
    class="bg-white rounded-2xl border p-4 cursor-pointer transition-all hover:shadow-md group"
    :class="selected ? 'border-indigo-500 ring-2 ring-indigo-500/10' : 'border-slate-200/80 hover:border-slate-300'">
    <div class="flex items-start justify-between gap-3">
      <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
          <i class="fas fa-building text-sm"></i>
        </div>
        <div class="min-w-0">
          <h3 class="text-sm font-black text-slate-900 truncate uppercase tracking-wide">{{ service.name }}</h3>
          <p class="text-[10px] text-slate-400 font-medium mt-0.5">
            {{ service.employees_count ?? service.employees?.length ?? 0 }} membre(s)
          </p>
        </div>
      </div>
      <div class="flex gap-1.5 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
        <button @click.stop="$emit('edit', service)"
          class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 flex items-center justify-center transition-colors">
          <i class="fas fa-pen text-[10px]"></i>
        </button>
        <button @click.stop="$emit('delete', service)"
          class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-colors">
          <i class="fas fa-trash text-[10px]"></i>
        </button>
      </div>
    </div>
    <p v-if="service.description" class="text-xs text-slate-400 mt-2 line-clamp-2 leading-relaxed">{{ service.description }}</p>
    <div v-if="service.chef" class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100">
      <i class="fas fa-crown text-amber-500 text-[10px]"></i>
      <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Chef:</span>
      <span class="text-[10px] font-bold text-slate-700">{{ service.chef.firstname }} {{ service.chef.lastname }}</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  service: {
    type: Object,
    required: true,
  },
  selected: {
    type: Boolean,
    default: false,
  },
});

defineEmits(["select", "edit", "delete"]);
</script>