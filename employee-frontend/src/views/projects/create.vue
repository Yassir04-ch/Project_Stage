<script setup>
import { reactive, ref } from "vue";
import { createProject } from "@/services/projectService";

const form = reactive({
  name: "",
  description: "",
  budget: "",
  start_date: "",
  end_date: "",
  status: "planning",
});

const errors = ref({});
const loading = ref(false);
const showSuccess = ref(false);

const getError = (field) => {
  return errors.value?.[field]?.[0];
};

const submitProject = async () => {
  if (loading.value) return;
  loading.value = true;
  errors.value = {};
  showSuccess.value = false;

  try {
    await createProject(form);

    showSuccess.value = true;

    // Reset form clean state
    form.name = "";
    form.description = "";
    form.budget = "";
    form.start_date = "";
    form.end_date = "";
    form.status = "planning";

    // Auto hide notification card menu
    setTimeout(() => {
      showSuccess.value = false;
    }, 4000);

  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.log(error);
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-slate-50/50 font-sans antialiased flex items-center justify-center p-4 sm:p-6">
    <div class="w-full max-w-xl bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 relative overflow-hidden transition-all">
      
      <div class="absolute top-0 left-0 right-0 h-[4px] bg-indigo-600"></div>

      <div class="mb-8">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Créer un nouveau projet</h2>
        <p class="text-xs text-slate-500 mt-1">Renseignez les détails ci-dessous pour initialiser l'espace du projet.</p>
      </div>

      <div v-if="showSuccess" class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center gap-3 text-emerald-800 text-xs font-semibold animate-fadeIn">
        <span>✅</span>
        <p>Le projet a été créé avec succès dans l'application !</p>
      </div>

      <form @submit.prevent="submitProject" class="space-y-5">
        
        <div class="space-y-1.5">
          <label class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Nom du projet</label>
          <input
            v-model="form.name"
            type="text"
            placeholder="Ex: Refonte Plateforme StayEase"
            class="border px-3 py-2 text-xs w-full rounded-xl outline-none transition-all placeholder:text-slate-400 bg-slate-50/30"
            :class="getError('name') ? 'border-rose-300 focus:border-rose-500 bg-rose-50/[0.01]' : 'border-slate-200 focus:border-indigo-500 focus:bg-white focus:shadow-sm'"
          />
          <p v-if="getError('name')" class="text-rose-600 text-[11px] font-medium flex items-center gap-1 animate-fadeIn">
            <span>⚠️</span> {{ getError('name') }}
          </p>
        </div>

        <div class="space-y-1.5">
          <label class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            placeholder="Détails, objectifs globaux et livrables attendus..."
            class="border px-3 py-2 text-xs w-full rounded-xl outline-none transition-all placeholder:text-slate-400 bg-slate-50/30 border-slate-200 focus:border-indigo-500 focus:bg-white focus:shadow-sm resize-none"
          ></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Budget (MAD)</label>
            <div class="relative flex items-center">
              <input
                v-model="form.budget"
                type="number"
                placeholder="50 000"
                class="border pl-3 pr-10 py-2 text-xs w-full rounded-xl outline-none transition-all placeholder:text-slate-400 bg-slate-50/30"
                :class="getError('budget') ? 'border-rose-300 focus:border-rose-500' : 'border-slate-200 focus:border-indigo-500 focus:bg-white'"
              />
              <span class="absolute right-3.5 text-[10px] font-bold text-slate-400 uppercase">DH</span>
            </div>
            <p v-if="getError('budget')" class="text-rose-600 text-[11px] font-medium animate-fadeIn">{{ getError('budget') }}</p>
          </div>

          <div class="space-y-1.5">
            <label class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Statut Initial</label>
            <select 
              v-model="form.status" 
              class="border px-3 py-2 text-xs w-full rounded-xl outline-none transition-all bg-slate-50/30 border-slate-200 focus:border-indigo-500 focus:bg-white appearance-none cursor-pointer"
            >
              <option value="planning">📋 Planning</option>
              <option value="active">⚡ Active</option>
              <option value="completed">✅ Completed</option>
              <option value="cancelled">❌ Cancelled</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Date de début</label>
            <input
              v-model="form.start_date"
              type="date"
              class="border px-3 py-2 text-xs w-full rounded-xl outline-none transition-all bg-slate-50/30"
              :class="getError('start_date') ? 'border-rose-300 focus:border-rose-500' : 'border-slate-200 focus:border-indigo-500 focus:bg-white'"
            />
            <p v-if="getError('start_date')" class="text-rose-600 text-[11px] font-medium animate-fadeIn">{{ getError('start_date') }}</p>
          </div>

          <div class="space-y-1.5">
            <label class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Date de fin</label>
            <input
              v-model="form.end_date"
              type="date"
              class="border px-3 py-2 text-xs w-full rounded-xl outline-none transition-all bg-slate-50/30"
              :class="getError('end_date') ? 'border-rose-300 focus:border-rose-500' : 'border-slate-200 focus:border-indigo-500 focus:bg-white'"
            />
            <p v-if="getError('end_date')" class="text-rose-600 text-[11px] font-medium animate-fadeIn">{{ getError('end_date') }}</p>
          </div>
        </div>

        <div class="pt-2">
          <button
            type="submit"
            :disabled="loading"
            class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl w-full disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-150 shadow-md shadow-indigo-600/10 active:scale-[0.99] uppercase tracking-wider"
          >
            <span v-if="loading" class="inline-flex items-center gap-2 justify-center">
              <span class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              Enregistrement...
            </span>
            <span v-else>Créer le Projet</span>
          </button>
        </div>

      </form>

    </div>
  </div>
</template>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(2px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s ease-out forwards;
}
</style>