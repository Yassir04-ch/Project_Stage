<template>
  <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md p-7 space-y-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-black text-slate-900 uppercase tracking-tight">
            {{ formMode === 'create' ? '+ Nouveau Service' : '✏️ Modifier Service' }}
          </h2>
          <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <div class="space-y-4">
          <div class="space-y-1.5">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Nom du service *</label>
            <input v-model="form.name" type="text" placeholder="ex: Département IT"
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-colors" />
          </div>

          <div class="space-y-1.5">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Description</label>
            <textarea v-model="form.description" rows="3" placeholder="Description du service..."
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-colors resize-none"></textarea>
          </div>

          <div class="space-y-1.5">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Chef du service</label>
            <select v-model="form.chef_id"
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-colors appearance-none cursor-pointer bg-white">
              <option :value="null">Aucun chef assigné</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.firstname }} {{ emp.lastname }} — {{ emp.role?.name }}
              </option>
            </select>
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <button @click="$emit('close')" type="button"
            class="flex-1 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition-colors">
            Annuler
          </button>
          <button @click="$emit('submit')" :disabled="formLoading || !form.name"
            class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-md shadow-indigo-600/20 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <div v-if="formLoading" class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
            {{ formLoading ? 'Enregistrement...' : formMode === 'create' ? 'Créer' : 'Mettre à jour' }}
          </button>
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
  formMode: {
    type: String,
    default: "create",
  },
  form: {
    type: Object,
    required: true,
  },
  employees: {
    type: Array,
    default: () => [],
  },
  formLoading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(["close", "submit"]);
</script>