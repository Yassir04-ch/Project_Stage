<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/api/axios";

const router = useRouter();
const route = useRoute();
const loading = ref(false);
const loadingEmployees = ref(false);
const errorMsg = ref("");
const successMsg = ref("");

const employees = ref([]); 

// Utilisation directe de user_id récupéré depuis l'URL (query)
const form = ref({
  user_id: route.query.employee_id || "", 
  date: new Date().toISOString().substr(0, 10), 
  status: "absent",
  note: "",
  check_in: "",
  check_out: "",
  is_justified: false,
});

// Trouver le collaborateur sélectionné pour l'afficher proprement dans le titre ou le sous-titre
const selectedEmployee = computed(() => {
  return employees.value.find(emp => String(emp.id) === String(form.value.user_id));
});

const loadEmployees = async () => {
  loadingEmployees.value = true;
  try {
    const res = await api.get("/users", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    employees.value = res.data.users || res.data;
  } catch (err) {
    console.error("Failed to load employees list:", err);
    errorMsg.value = "Impossible de charger les informations du collaborateur.";
  } finally {
    loadingEmployees.value = false;
  }
};

const createAbsence = async () => {
  loading.value = true;
  errorMsg.value = "";
  successMsg.value = "";
  
  const payload = {
    ...form.value,
    check_in: form.value.status !== 'absent' ? (form.value.check_in || null) : null,
    check_out: form.value.status !== 'absent' ? (form.value.check_out || null) : null,
    note: form.value.note || null,
  };

  try {
    await api.post("/absences", payload, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    successMsg.value = "Le suivi opérationnel a été enregistré avec succès.";
    
    // Reset form smoothly
    form.value = {
      user_id: route.query.employee_id || "",
      date: new Date().toISOString().substr(0, 10),
      status: "absent",
      note: "",
      check_in: "",
      check_out: "",
      is_justified: false,
    };

    setTimeout(() => {
      router.push("/absences"); 
    }, 1500);

  } catch (err) {
    console.error(err.response?.data);
    if (err.response?.data?.errors) {
      errorMsg.value = Object.values(err.response.data.errors).flat().join(" | ");
    } else {
      errorMsg.value = err.response?.data?.message || "Une erreur est survenue lors de l'enregistrement.";
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadEmployees(); 
});
</script>

<template>
  <div class="min-h-screen bg-slate-50/50 font-sans antialiased flex justify-center items-center p-4 sm:p-6 md:p-10">
    <div class="w-full max-w-2xl bg-white rounded-2xl border border-slate-200/60 shadow-[0_10px_30px_rgba(0,0,0,0.02)] p-6 sm:p-8 relative overflow-hidden transition-all">
      
      <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600"></div>

      <div class="mb-6 flex flex-col border-b border-slate-100 pb-5">
        <button 
          @click="router.back()" 
          type="button"
          class="text-[10px] font-extrabold text-slate-400 hover:text-indigo-600 transition-colors flex items-center gap-1.5 mb-2.5 uppercase tracking-widest w-fit"
        >
          ← Retour au tableau
        </button>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">
          Feuille de Temps & Présences
        </h1>
        
        <div class="mt-2 p-3 bg-slate-50 border border-slate-200/60 rounded-xl flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 text-sm font-semibold">
              👤
            </span>
            <div>
              <p class="text-xs font-bold text-slate-700">
                <span v-if="loadingEmployees" class="text-slate-400 animate-pulse">Chargement du profil...</span>
                <span v-else-if="selectedEmployee">{{ selectedEmployee.firstname }} {{ selectedEmployee.lastname }}</span>
                <span v-else class="text-slate-500">Collaborateur ID: {{ form.user_id }}</span>
              </p>
              <p class="text-[10px] text-slate-400 font-medium">
                <span v-if="selectedEmployee">[{{ selectedEmployee.role?.name || 'Staff' }}]</span>
                <span v-else>Suivi opérationnel personnalisé</span>
              </p>
            </div>
          </div>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
            Profil sélectionné
          </span>
        </div>
      </div>

      <div v-if="successMsg" class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-semibold animate-fadeIn flex items-center gap-2.5">
        <span class="flex h-5 w-5 items-center justify-center rounded-lg bg-emerald-500 text-white text-[10px]">✓</span>
        <p>{{ successMsg }}</p>
      </div>
      
      <div v-if="errorMsg" class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-semibold animate-fadeIn flex items-center gap-2.5">
        <span class="flex h-5 w-5 items-center justify-center rounded-lg bg-rose-500 text-white text-[10px]">⚠️</span>
        <p>{{ errorMsg }}</p>
      </div>

      <form @submit.prevent="createAbsence" class="space-y-6">
        
        <div class="space-y-1.5 max-w-xs">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Date d'effet</label>
          <input
            v-model="form.date"
            type="date"
            required
            class="w-full border border-slate-200 px-3.5 py-2.5 text-xs rounded-xl outline-none transition-all bg-slate-50/40 focus:border-indigo-500 focus:bg-white text-slate-700 font-medium shadow-2xs"
          />
        </div>

        <div class="space-y-2">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nature de la qualification</label>
          <div class="grid grid-cols-3 gap-3">
            
            <label class="border rounded-xl p-3.5 flex flex-col items-center justify-center gap-1.5 cursor-pointer transition-all select-none group relative overflow-hidden shadow-2xs"
              :class="form.status === 'absent' ? 'border-rose-300 bg-rose-50/40 text-rose-700 font-bold ring-1 ring-rose-400/20' : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50/80 hover:text-slate-700'">
              <input type="radio" v-model="form.status" value="absent" class="sr-only">
              <span class="text-base">🛑</span>
              <span class="text-[11px] uppercase tracking-wide font-semibold">Absent</span>
            </label>

            <label class="border rounded-xl p-3.5 flex flex-col items-center justify-center gap-1.5 cursor-pointer transition-all select-none group relative overflow-hidden shadow-2xs"
              :class="form.status === 'late' ? 'border-amber-300 bg-amber-50/40 text-amber-800 font-bold ring-1 ring-amber-400/20' : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50/80 hover:text-slate-700'">
              <input type="radio" v-model="form.status" value="late" class="sr-only">
              <span class="text-base">⏳</span>
              <span class="text-[11px] uppercase tracking-wide font-semibold">En Retard</span>
            </label>

            <label class="border rounded-xl p-3.5 flex flex-col items-center justify-center gap-1.5 cursor-pointer transition-all select-none group relative overflow-hidden shadow-2xs"
              :class="form.status === 'present' ? 'border-emerald-300 bg-emerald-50/40 text-emerald-800 font-bold ring-1 ring-emerald-400/20' : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50/80 hover:text-slate-700'">
              <input type="radio" v-model="form.status" value="present" class="sr-only">
              <span class="text-base">✅</span>
              <span class="text-[11px] uppercase tracking-wide font-semibold">Présent</span>
            </label>

          </div>
        </div>

        <transition enter-active-class="transition duration-200 ease-out" enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100">
          <div v-show="form.status !== 'absent'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50/40 border border-slate-200/60 border-dashed animate-fadeIn">
            <div class="space-y-1.5">
              <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Heure de pointage (In)</label>
              <input
                v-model="form.check_in"
                type="time"
                class="w-full border border-slate-200 px-3 py-2 text-xs rounded-lg outline-none transition-all focus:border-indigo-500 bg-white text-slate-700 font-mono"
              />
            </div>

            <div class="space-y-1.5">
              <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Heure de départ (Out)</label>
              <input
                v-model="form.check_out"
                type="time"
                class="w-full border border-slate-200 px-3 py-2 text-xs rounded-lg outline-none transition-all focus:border-indigo-500 bg-white text-slate-700 font-mono"
              />
            </div>
          </div>
        </transition>

        <div class="p-3.5 rounded-xl border border-slate-200/50 bg-slate-50/20 flex items-center justify-between gap-4">
          <div class="space-y-0.5">
            <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wide">Régularisation / Justificatif</span>
            <p class="text-[10px] text-slate-400 leading-normal">Cochez si un document ou motif valable a été fourni par l'employé.</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer select-none group shrink-0">
            <input 
              type="checkbox" 
              v-model="form.is_justified" 
              class="sr-only peer"
            >
            <div class="w-10 h-5.5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4.5 after:w-4.5 after:transition-all peer-checked:bg-indigo-600"></div>
          </label>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Commentaire & Observations du management</label>
          <textarea
            v-model="form.note"
            class="w-full border border-slate-200 px-3.5 py-2.5 text-xs rounded-xl outline-none transition-all bg-slate-50/40 focus:border-indigo-500 focus:bg-white resize-none text-slate-700 leading-relaxed shadow-2xs"
            rows="3"
            placeholder="Ex: Certificat médical d'arrêt de travail de 48h déposé ce matin..."
          ></textarea>
        </div>

        <div class="pt-2">
          <button
            type="submit"
            :disabled="loading"
            class="bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold px-5 py-3 rounded-xl w-full disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200 shadow-md active:scale-[0.99] uppercase tracking-wider flex items-center justify-center gap-2"
          >
            <span v-if="loading" class="inline-flex items-center gap-2 justify-center">
              <span class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              Synchronisation des données...
            </span>
            <span v-else>Enregistrer dans le registre</span>
          </button>
        </div>
      </form>

    </div>
  </div>
</template>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(3px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>