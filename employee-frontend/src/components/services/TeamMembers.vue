<template>
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-users text-indigo-500 text-xs"></i>
        <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">
          Membres de l'équipe ({{ selectedService.employees?.length ?? 0 }})
        </h3>
      </div>

      <button @click="$emit('add-member')" class="px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
        <i class="fas fa-plus text-[10px]"></i> Ajouter un membre
      </button>
    </div>

    <EmptyState v-if="!selectedService.employees?.length" variant="no-members" />

    <div v-else class="divide-y divide-slate-100">
      <div v-for="emp in selectedService.employees" :key="emp.id"
        class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/60 transition-colors group">
        <div class="w-11 h-11 rounded-xl overflow-hidden bg-indigo-50 border border-indigo-100 shrink-0 shadow-sm relative">
          <img v-if="emp.photo" :src="getPhotoUrl(emp.photo)" class="w-full h-full object-cover" />
          <div v-else class="w-full h-full flex items-center justify-center text-indigo-600 font-black text-sm uppercase bg-gradient-to-br from-indigo-50 to-indigo-100">
            {{ emp.firstname?.[0] }}{{ emp.lastname?.[0] }}
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-slate-800 truncate group-hover:text-indigo-600 transition-colors">{{ emp.firstname }} {{ emp.lastname }}</p>
          <div class="flex items-center gap-2 mt-0.5 text-[11px] font-medium text-slate-400 truncate">
            <span class="text-indigo-600 font-semibold bg-indigo-50 px-1.5 py-0.5 rounded-md text-[10px]">{{ emp.role?.name || 'Membre' }}</span>
            <span>·</span>
            <span>{{ emp.email }}</span>
          </div>
        </div>
        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-1 group-hover:translate-x-0">
          <button @click="router.push(`/getUser/${emp.id}`)"
            class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-600 text-[11px] font-bold transition-all shadow-sm">
            Voir Profil
          </button>
          <button @click="$emit('remove-employee', emp.id)"
            class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-200 text-slate-500 hover:text-rose-600 text-[11px] font-bold transition-all shadow-sm">
            Retirer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";
import EmptyState from "./EmptyState.vue";

defineProps({
  selectedService: {
    type: Object,
    required: true,
  },
  getPhotoUrl: {
    type: Function,
    required: true,
  },
});

defineEmits(["add-member", "remove-employee"]);

const router = useRouter();
</script>