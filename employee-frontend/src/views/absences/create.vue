<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/api/axios";

const router = useRouter();
const loading = ref(false);
const loadingEmployees = ref(false);
const errorMsg = ref("");
const successMsg = ref("");

const employees = ref([]); 

const form = ref({
  user_id: "",
  date: "",
  status: "absent",
  note: "",
  check_in: "",
  check_out: "",
  is_justified: false,
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
    errorMsg.value = "Could not fetch the employees list.";
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
    check_in: form.value.check_in || null,
    check_out: form.value.check_out || null,
    note: form.value.note || null,
  };

  try {
    await api.post("/absences", payload, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    successMsg.value = "Absence record created successfully!";
    
    form.value = {
      user_id: "",
      date: "",
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
      errorMsg.value = err.response?.data?.message || "Failed to create absence record.";
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
  <div class="min-h-screen bg-[#f8fafc] p-6 sm:p-10 flex justify-center items-center">
    <div class="w-full max-w-2xl bg-white p-8 sm:p-12 rounded-[2.5rem] shadow-[0_30px_70px_rgba(0,0,0,0.04)] border border-slate-100">
      
      <div class="mb-8">
        <button 
          @click="router.back()" 
          class="text-sm font-medium text-slate-400 hover:text-indigo-600 transition-colors flex items-center gap-1 mb-2"
        >
          ← Back to Overview
        </button>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase">
          Log New Absence
        </h1>
        <p class="text-slate-400 text-sm mt-1">
          Fill in the required information to log a team member's operational status.
        </p>
      </div>

      <div v-if="successMsg" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium">
        ✨ {{ successMsg }}
      </div>
      <div v-if="errorMsg" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-700 text-sm font-medium">
        ⚠️ {{ errorMsg }}
      </div>

      <form @submit.prevent="createAbsence" class="grid sm:grid-cols-2 gap-6">
        
        <div class="space-y-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Select Employee</label>
          <div class="relative">
            <select
              v-model="form.user_id"
              required
              class="w-full border border-slate-200 p-3.5 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 bg-slate-50/50 text-sm transition-all appearance-none"
            >
              <option value="" disabled selected>
                {{ loadingEmployees ? ' Loading team list...' : 'Choose an employee...' }}
              </option>
              <option 
                v-for="emp in employees" 
                :key="emp.id" 
                :value="emp.id"
              >
                👤 {{ emp.firstname }} {{ emp.lastname }} ({{ emp.role.name }})
              </option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 text-xs">
              ▼
            </div>
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Log Date</label>
          <input
            v-model="form.date"
            type="date"
            required
            class="w-full border border-slate-200 p-3.5 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 bg-slate-50/50 text-sm transition-all"
          />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Category</label>
          <div class="relative">
            <select
              v-model="form.status"
              class="w-full border border-slate-200 p-3.5 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 bg-slate-50/50 text-sm transition-all appearance-none"
            >
              <option value="absent">🛑 Absent</option>
              <option value="late">⏳ Late</option>
              <option value="present">✅ Present</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 text-xs">
              ▼
            </div>
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Check In (Optional)</label>
          <input
            v-model="form.check_in"
            type="time"
            class="w-full border border-slate-200 p-3.5 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 bg-slate-50/50 text-sm transition-all"
          />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Check Out (Optional)</label>
          <input
            v-model="form.check_out"
            type="time"
            class="w-full border border-slate-200 p-3.5 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 bg-slate-50/50 text-sm transition-all"
          />
        </div>

        <div class="flex items-center gap-3 sm:pt-7">
          <label class="relative inline-flex items-center cursor-pointer">
            <input 
              type="checkbox" 
              v-model="form.is_justified" 
              class="sr-only peer"
            >
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            <span class="ml-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Is Justified?</span>
          </label>
        </div>

        <div class="sm:col-span-2 space-y-1.5">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Manager Notes / Reason</label>
          <textarea
            v-model="form.note"
            class="w-full border border-slate-200 p-3.5 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 bg-slate-50/50 text-sm transition-all"
            rows="3"
            placeholder="Provide context regarding the context of the schedule disruption..."
          ></textarea>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="sm:col-span-2 bg-indigo-600 text-white font-semibold p-4 rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-100 transition-all text-sm mt-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? "Saving Record..." : "Confirm & Save Record" }}
        </button>
      </form>

    </div>
  </div>
</template>