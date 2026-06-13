<script setup>
import { ref, onMounted } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const absences = ref([]);
const loading = ref(false);

const router = useRouter();

const goToJustifications = (absenceId) => {

  router.push(`/justifications/absence/${absenceId}`);

};

const loadAbsences = async () => {
  loading.value = true;
  try {
    const res = await api.get("/absences", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    absences.value = res.data.absences;
  } catch (err) {
    console.error(err.response?.data);
  } finally {
    loading.value = false;
  }
};

const deleteAbsence = async (id) => {
  if (!confirm("Are you sure you want to delete this log?")) return;
  
  try {
    await api.delete(`/absences/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    loadAbsences();
  } catch (err) {
    console.error(err.response?.data);
  }
};

onMounted(() => {
  loadAbsences();
});
</script>

<template>
  <div class="min-h-screen bg-[#f8fafc] p-6 sm:p-10 font-sans antialiased">
    <div class="max-w-6xl mx-auto space-y-6">
      
      <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-slate-200/60 pb-5">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">
            Absences Log Center
          </h1>
          <p class="text-slate-400 text-xs mt-0.5">
            Monitor, verify, and manage company attendance flow records.
          </p>
        </div>
        
        <button 
          @click="loadAbsences" 
          :disabled="loading"
          class="inline-flex items-center justify-center gap-2 bg-white border border-slate-200 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-all shadow-sm active:scale-95 disabled:opacity-60"
        >
          <span :class="{ 'animate-spin': loading }">🔄</span> 
          {{ loading ? 'Updating...' : 'Refresh Logs' }}
        </button>
      </div>

      <div class="bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.02)] border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            
            <thead>
              <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                <th class="p-4 pl-6">Employee Info</th>
                <th class="p-4">Log Date</th>
                <th class="p-4">Status</th>
                <th class="p-4">Check In</th>
                <th class="p-4">Check Out</th>
                <th class="p-4">justifications</th>
                <th class="p-4">Justified</th>
                <th class="p-4 pr-6 text-right">Actions</th>
              </tr>
            </thead>

            <tbody v-if="loading">
              <tr v-for="n in 5" :key="n" class="border-b border-slate-100/60 animate-pulse">
                <td class="p-4 pl-6">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-200"></div>
                    <div class="space-y-2">
                      <div class="w-28 h-3.5 bg-slate-200 rounded-full"></div>
                      <div class="w-16 h-2.5 bg-slate-100 rounded-full"></div>
                    </div>
                  </div>
                </td>
                <td class="p-4"><div class="w-20 h-3 bg-slate-200 rounded-full"></div></td>
                <td class="p-4"><div class="w-16 h-5 bg-slate-200 rounded-full"></div></td>
                <td class="p-4"><div class="w-12 h-3 bg-slate-100 rounded-full"></div></td>
                <td class="p-4"><div class="w-12 h-3 bg-slate-100 rounded-full"></div></td>
                <td class="p-4"><div class="w-10 h-4 bg-slate-200 rounded-full"></div></td>
                <td class="p-4 pr-6 text-right"><div class="w-16 h-7 bg-slate-200 rounded-lg ml-auto"></div></td>
              </tr>
            </tbody>

            <tbody v-else-if="absences.length === 0">
              <tr>
                <td colspan="7" class="py-16 text-center">
                  <div class="text-3xl mb-2">📁</div>
                  <h3 class="text-sm font-bold text-slate-700">No records discovered</h3>
                  <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1">
                    All clear! There are currently no recorded absence flags logged inside the platform databases.
                  </p>
                </td>
              </tr>
            </tbody>

            <tbody v-else>
              <tr 
                v-for="a in absences" 
                :key="a.id" 
                class="border-b border-slate-100/80 hover:bg-slate-50/60 transition-colors group text-sm"
              >
                <td class="p-4 pl-6">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                      {{ a.user?.firstname?.[0] || 'U' }}{{ a.user?.lastname?.[0] || '' }}
                    </div>
                    <div>
                      <span class="font-semibold text-slate-800 block leading-tight">
                        {{ a.user?.firstname }} {{ a.user?.lastname }}
                      </span>
                      <span class="text-[10px] text-slate-400">ID: #{{ a.user_id }}</span>
                    </div>
                  </div>
                </td>

                <td class="p-4 font-medium text-slate-600">
                  {{ a.date }}
                </td>

                <td class="p-4">
                  <span 
                    class="px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1 shadow-sm"
                    :class="{
                      'bg-rose-50 text-rose-700 border border-rose-100/50': a.status === 'absent',
                      'bg-amber-50 text-amber-700 border border-amber-100/50': a.status === 'late',
                      'bg-emerald-50 text-emerald-700 border border-emerald-100/50': a.status === 'present'
                    }"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="{ 'bg-rose-500': a.status === 'absent', 'bg-amber-500': a.status === 'late', 'bg-emerald-500': a.status === 'present' }"></span>
                    <span class="capitalize text-[11px]">{{ a.status }}</span>
                  </span>
                </td>

                <td class="p-4 text-slate-500 font-mono text-xs">
                  {{ a.check_in || '--:--' }}
                </td>
                <td class="p-4 text-slate-500 font-mono text-xs">
                  {{ a.check_out || '--:--' }}
                </td>

                <td class="p-4">
                  <span 
                    class="text-xs font-semibold px-2 py-0.5 rounded"
                    :class="a.is_justified ? 'text-emerald-600 bg-emerald-50' : 'text-slate-400 bg-slate-100'"
                  >
                    {{ a.is_justified ? 'Yes' : 'No' }}
                  </span>
                </td>

                <td class="p-4 pr-6 text-right">
                  <button
                    @click="deleteAbsence(a.id)"
                    class="opacity-0 group-hover:opacity-100 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white px-3 py-1.5 rounded-xl text-xs font-medium transition-all duration-200 shadow-sm shadow-rose-100"
                  >
                    Delete Log
                  </button>
                </td>

                 <td class="p-4 pr-6 text-right">

                    <button
                      @click="goToJustifications(a.id)"
                      class=" bg-blue-600 text-white px-3 py-1.5 rounded-xl text-xs font-medium transition-all duration-200 shadow-sm"
                    >

                      Justifications

                    </button>

                  </td>

              </tr>
            </tbody>

          </table>
        </div>
      </div>

    </div>
  </div>
</template>