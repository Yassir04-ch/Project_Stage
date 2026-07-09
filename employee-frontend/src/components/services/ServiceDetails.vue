<template>
  <div class="col-span-8">
    <EmptyState v-if="!selectedService" variant="no-selection" />

    <div v-else class="space-y-5">
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-start justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xl font-black shadow-lg shadow-indigo-500/20">
              <i class="fas fa-building"></i>
            </div>
            <div>
              <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ selectedService.name }}</h2>
              <p class="text-xs text-slate-400 mt-1">{{ selectedService.description || 'Aucune description.' }}</p>
            </div>
          </div>
          <div class="flex gap-2">
            <button @click="$emit('edit', selectedService)"
              class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors flex items-center gap-1.5">
              <i class="fas fa-pen text-[10px]"></i> Modifier
            </button>
          </div>
        </div>

        <div v-if="selectedService.chef" class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-3">
          <i class="fas fa-crown text-amber-500 text-sm"></i>
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Chef du service:</span>
          <div class="flex items-center gap-2 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
            <div class="w-6 h-6 rounded-md overflow-hidden bg-indigo-100 shrink-0">
              <img v-if="selectedService.chef.photo" :src="getPhotoUrl(selectedService.chef.photo)" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-indigo-600 text-[10px] font-black uppercase">
                {{ selectedService.chef.firstname?.[0] }}
              </div>
            </div>
            <span class="text-xs font-bold text-slate-800">{{ selectedService.chef.firstname }} {{ selectedService.chef.lastname }}</span>
          </div>
        </div>
      </div>

      <TeamMembers
        :selected-service="selectedService"
        :get-photo-url="getPhotoUrl"
        @add-member="$emit('add-member')"
        @remove-employee="$emit('remove-employee', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import EmptyState from "./EmptyState.vue";
import TeamMembers from "./TeamMembers.vue";

defineProps({
  selectedService: {
    type: Object,
    default: null,
  },
  getPhotoUrl: {
    type: Function,
    required: true,
  },
});

defineEmits(["edit", "add-member", "remove-employee"]);
</script>