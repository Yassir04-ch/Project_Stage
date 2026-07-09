<template>
  <Transition 
    enter-active-class="transition ease-out duration-200" 
    enter-from-class="opacity-0 scale-95" 
    enter-to-class="opacity-100 scale-100" 
    leave-active-class="transition ease-in duration-150" 
    leave-from-class="opacity-100 scale-100" 
    leave-to-class="opacity-0 scale-95">
    
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden flex flex-col max-h-[85vh]">
        
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm shadow-sm">
              <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
              <h2 class="text-sm font-black text-slate-900 uppercase tracking-tight">Ajouter au service</h2>
              <p class="text-[11px] font-medium text-slate-400 mt-0.5">Sélectionnez les membres à intégrer</p>
            </div>
          </div>
          <button @click="$emit('close')" class="w-8 h-8 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 transition-colors">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="p-4 border-b border-slate-100 relative">
          <i class="fas fa-search absolute left-8 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            :value="searchEmployee"
            @input="$emit('update:searchEmployee', $event.target.value)"
            type="text" 
            placeholder="Rechercher par nom, prénom ou email..."
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-colors bg-slate-50 focus:bg-white" 
          />
        </div>

        <div class="overflow-y-auto p-2 flex-1 bg-slate-50/30">
          <div v-if="filteredUnassignedEmployees.length === 0" class="py-12 text-center flex flex-col items-center">
            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-300 mb-3">
              <i class="fas fa-user-slash text-lg"></i>
            </div>
            <p class="text-xs text-slate-500 font-bold">Aucun collaborateur trouvé.</p>
            <p class="text-[10px] text-slate-400 mt-1">Tous les employés sont peut-être déjà assignés, ou l-recherche khawya.</p>
          </div>

          <div v-else class="space-y-1">
            <div v-for="emp in filteredUnassignedEmployees" :key="emp.id" 
              class="flex items-center justify-between p-3 rounded-xl hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200 transition-all group">
              
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl overflow-hidden bg-indigo-50 border border-indigo-100 shrink-0">
                  <img v-if="emp.photo" :src="getPhotoUrl(emp.photo)" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-indigo-600 text-xs font-black uppercase">
                    {{ emp.firstname?.[0] }}{{ emp.lastname?.[0] }}
                  </div>
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-bold text-slate-800 truncate">{{ emp.firstname }} {{ emp.lastname }}</p>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded-md">{{ emp.role?.name || 'Membre' }}</span>
                    <span class="text-[10px] text-slate-400 truncate">{{ emp.email }}</span>
                  </div>
                </div>
              </div>

              <button @click="$emit('assign', emp.id)" 
                class="shrink-0 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white text-[11px] font-bold transition-colors border border-indigo-100 hover:border-indigo-600 flex items-center gap-1.5">
                <i class="fas fa-plus"></i> Ajouter
              </button>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  searchEmployee: {
    type: String,
    default: "",
  },
  filteredUnassignedEmployees: {
    type: Array,
    default: () => [],
  },
  getPhotoUrl: {
    type: Function,
    required: true,
  },
});

defineEmits(["close", "assign", "update:searchEmployee"]);
</script>