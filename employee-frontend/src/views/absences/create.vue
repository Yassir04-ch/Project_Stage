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

const form = ref({
  user_id: route.query.employee_id || "", 
  date: new Date().toISOString().substr(0, 10), 
  status: "absent", 
  check_in: "",
  check_out: "",
  note: "",
  is_justified: false,
});

const selectedEmployee = computed(() => {
  return employees.value.find(emp => String(emp.id) === String(form.value.user_id));
});

const loadEmployees = async () => {
  loadingEmployees.value = true;
  try {
    const res = await api.get("/users", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    employees.value = res.data.users || res.data;
  } catch (err) {
    console.error(err);
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
    is_justified: !!form.value.is_justified,
  };

  try {
    await api.post("/absences", payload, {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });

    successMsg.value = "L'enregistrement a été synchronisé avec le registre des absences.";
    
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
    if (err.response?.data?.errors) {
      errorMsg.value = Object.values(err.response.data.errors).flat().join(" | ");
    } else {
      errorMsg.value = err.response?.data?.message || "Une erreur est survenue lors de l'enregistrement.";
    }
  } finally {
    loading.value = false;
  }
};

onMounted(loadEmployees);
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 text-slate-600 font-sans antialiased flex justify-center items-center p-4 sm:p-6 md:p-10">
    <div class="w-full max-w-2xl bg-white rounded-3xl border border-slate-200 shadow-[0_20px_40px_rgba(0,0,0,0.03)] p-6 sm:p-8 relative overflow-hidden transition-all">
      
      <!-- Ligne décorative haute -->
      <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600"></div>

      <!-- HEADER -->
      <div class="mb-8 flex flex-col border-b border-slate-100 pb-5">
        <button 
          @click="router.back()" 
          type="button"
          class="text-[10px] font-black text-slate-400 hover:text-blue-600 transition-colors flex items-center gap-2 mb-4 uppercase tracking-widest w-fit"
        >
          <i class="fas fa-arrow-left"></i> Retour au tableau
        </button>
        
        <h1 class="text-xl font-black tracking-tight text-slate-900 uppercase">
          Suivi Opérationnel & Présences
        </h1>
        
        <!-- Collaborateur Info Card -->
        <div class="mt-4 p-4 bg-slate-50 border border-slate-200/60 rounded-2xl flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 text-sm font-semibold border border-blue-100">
              <i class="fas fa-user"></i>
            </span>
            <div>
              <p class="text-xs font-black text-slate-800">
                <span v-if="loadingEmployees" class="text-slate-400 animate-pulse">Chargement...</span>
                <span v-else-if="selectedEmployee">{{ selectedEmployee.firstname }} {{ selectedEmployee.lastname }}</span>
                <span v-else>ID Collaborateur: #{{ form.user_id }}</span>
              </p>
              <p class="text-[10px] text-slate-400 font-bold tracking-wider uppercase mt-0.5">
                <span v-if="selectedEmployee">{{ selectedEmployee.role?.name || 'Staff' }}</span>
                <span v-else>Fiche d'enregistrement</span>
              </p>
            </div>
          </div>
          <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
            Cible active
          </span>
        </div>
      </div>

      <!-- ALERTS -->
      <div v-if="successMsg" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-bold flex items-center gap-3 animate-fadeIn">
        <i class="fas fa-circle-check text-sm text-emerald-600"></i>
        <p>{{ successMsg }}</p>
      </div>
      
      <div v-if="errorMsg" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold flex items-center gap-3 animate-fadeIn">
        <i class="fas fa-triangle-exclamation text-sm text-rose-600"></i>
        <p>{{ errorMsg }}</p>
      </div>

      <!-- FORM -->
      <form @submit.prevent="createAbsence" class="space-y-6">
        
        <!-- DATE -->
        <div class="space-y-2 max-w-xs">
          <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Date d'effet</label>
          <input
            v-model="form.date"
            type="date"
            required
            class="w-full border border-slate-200 bg-slate-50/50 px-4 py-3 text-xs rounded-xl outline-none transition-all focus:border-blue-500 focus:bg-white text-slate-800 font-bold"
          />
        </div>

        <!-- STATUS SELECTION (RADIO CARDS) -->
        <div class="space-y-3">
          <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Nature de la qualification (Status)</label>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            
            <!-- Absent -->
            <label class="border rounded-xl p-3.5 flex flex-col items-center justify-center gap-2 cursor-pointer transition-all select-none relative"
              :class="form.status === 'absent' ? 'border-rose-400 bg-rose-50 text-rose-700 font-black shadow-sm shadow-rose-100' : 'border-slate-200 bg-white text-slate-400 hover:bg-slate-50 hover:text-slate-600'">
              <input type="radio" v-model="form.status" value="absent" class="sr-only">
              <i class="fas fa-user-slash text-base"></i>
              <span class="text-[9px] uppercase tracking-wider font-black">Absent</span>
            </label>

            <!-- Late -->
            <label class="border rounded-xl p-3.5 flex flex-col items-center justify-center gap-2 cursor-pointer transition-all select-none relative"
              :class="form.status === 'late' ? 'border-amber-400 bg-amber-50 text-amber-700 font-black shadow-sm shadow-amber-100' : 'border-slate-200 bg-white text-slate-400 hover:bg-slate-50 hover:text-slate-600'">
              <input type="radio" v-model="form.status" value="late" class="sr-only">
              <i class="fas fa-clock text-base"></i>
              <span class="text-[9px] uppercase tracking-wider font-black">Retard</span>
            </label>

            <!-- Half Day -->
            <label class="border rounded-xl p-3.5 flex flex-col items-center justify-center gap-2 cursor-pointer transition-all select-none relative"
              :class="form.status === 'half_day' ? 'border-blue-400 bg-blue-50 text-blue-700 font-black shadow-sm shadow-blue-100' : 'border-slate-200 bg-white text-slate-400 hover:bg-slate-50 hover:text-slate-600'">
              <input type="radio" v-model="form.status" value="half_day" class="sr-only">
              <i class="fas fa-calendar-minus text-base"></i>
              <span class="text-[9px] uppercase tracking-wider font-black">Demi-Jour</span>
            </label>

            <!-- Present -->
            <label class="border rounded-xl p-3.5 flex flex-col items-center justify-center gap-2 cursor-pointer transition-all select-none relative"
              :class="form.status === 'present' ? 'border-emerald-400 bg-emerald-50 text-emerald-700 font-black shadow-sm shadow-emerald-100' : 'border-slate-200 bg-white text-slate-400 hover:bg-slate-50 hover:text-slate-600'">
              <input type="radio" v-model="form.status" value="present" class="sr-only">
              <i class="fas fa-user-check text-base"></i>
              <span class="text-[9px] uppercase tracking-wider font-black">Présent</span>
            </label>

          </div>
        </div>

        <!-- CONDITIONAL HOURS -->
        <transition enter-active-class="transition duration-200 ease-out" enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100">
          <div v-show="form.status !== 'absent'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 border-dashed">
            <div class="space-y-2">
              <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Heure d'arrivée (check_in)</label>
              <input
                v-model="form.check_in"
                type="time"
                class="w-full border border-slate-200 bg-white px-3 py-2 text-xs rounded-lg outline-none transition-all focus:border-blue-500 text-slate-800 font-mono"
              />
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Heure de départ (check_out)</label>
              <input
                v-model="form.check_out"
                type="time"
                class="w-full border border-slate-200 bg-white px-3 py-2 text-xs rounded-lg outline-none transition-all focus:border-blue-500 text-slate-800 font-mono"
              />
            </div>
          </div>
        </transition>

        <!-- JUSTIFICATION CHECKBOX -->
        <div 
          :class="form.is_justified ? 'border-blue-200 bg-blue-50/40 text-blue-700' : 'border-slate-200 bg-slate-50/50'"
          class="p-4 rounded-xl border transition-all duration-300 flex items-center justify-between gap-4"
        >
          <div class="space-y-1">
            <span :class="form.is_justified ? 'text-blue-700' : 'text-slate-700'" class="text-[11px] font-black uppercase tracking-wider block transition-colors">
              Régularisation / Justifié (is_justified)
            </span>
            <p class="text-[10px] text-slate-400 leading-normal font-medium">Cochez si l'absence ou anomalie est officiellement couverte par un motif valable.</p>
          </div>
          
          <label class="relative inline-flex items-center cursor-pointer select-none shrink-0">
            <input 
              type="checkbox" 
              v-model="form.is_justified" 
              class="sr-only peer"
            >
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          </label>
        </div>

        <!-- NOTE -->
        <div class="space-y-2">
          <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Notes & Observations (note)</label>
          <textarea
            v-model="form.note"
            class="w-full border border-slate-200 bg-slate-50/50 px-4 py-3 text-xs rounded-xl outline-none transition-all focus:border-blue-500 focus:bg-white resize-none text-slate-800 leading-relaxed"
            rows="3"
            placeholder="Ajouter des détails sur la décision managériale ou l'anomalie..."
          ></textarea>
        </div>

        <!-- FOOTER ACTIONS -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-100">
          <button
            @click="router.back()"
            type="button"
            class="flex-1 bg-white hover:bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 text-[10px] font-black uppercase tracking-widest py-3.5 rounded-xl transition-all"
          >
            <i class="fas fa-chevron-left mr-2"></i> Annuler & Retour
          </button>
          
          <button
            type="submit"
            :disabled="loading"
            class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-3.5 rounded-xl disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-lg shadow-blue-600/10 flex items-center justify-center gap-2"
          >
            <span v-if="loading" class="inline-flex items-center gap-2">
              <span class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              Enregistrement...
            </span>
            <span v-else><i class="fas fa-save mr-1.5"></i> Valider l'enregistrement</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>