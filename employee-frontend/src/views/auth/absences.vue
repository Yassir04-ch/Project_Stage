<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const router = useRouter();

const absences = ref([]);
const loading = ref(false);
const unreadCount = ref(0);
const userId = ref(null);
const isDark = ref(localStorage.getItem("theme") !== "light");

const filterStatus = ref("all");

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
};

const filteredAbsences = computed(() => {
  if (filterStatus.value === "all") return absences.value;
  return absences.value.filter((a) => a.status === filterStatus.value);
});

const stats = computed(() => ({
  total: absences.value.length,
  present: absences.value.filter((a) => a.status === "present").length,
  absent: absences.value.filter((a) => a.status === "absent").length,
  late: absences.value.filter((a) => a.status === "late").length,
  half_day: absences.value.filter((a) => a.status === "half_day").length,
  justified: absences.value.filter((a) => a.is_justified).length,
}));

const loadMyAbsences = async () => {
  loading.value = true;
  try {
    const res = await api.get("/my-absences", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    absences.value = res.data.absences || [];
  } catch (err) {
    console.error(err.response?.data);
  } finally {
    loading.value = false;
  }
};

const loadProfile = async () => {
  try {
    const res = await api.get("/profile", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    userId.value = res.data.data?.id || null;
  } catch (err) {
    console.error(err);
  }
};

const loadUnreadCount = async () => {
  try {
    const res = await api.get("/notifications");
    const notifications = res.data.notifications || [];
    unreadCount.value = notifications.filter((n) => !n.is_read).length;
  } catch {}
};

const listenToNotifications = () => {
  if (!userId.value || !window.Echo) return;
  window.Echo.private(`notifications.${userId.value}`).listen(".notification.new", () => {
    unreadCount.value++;
  });
};

const stopListening = () => {
  if (userId.value && window.Echo) {
    window.Echo.leave(`notifications.${userId.value}`);
  }
};

const formatDate = (d) => {
  if (!d) return "—";
  return new Date(d).toLocaleDateString("fr-FR", {
    weekday: "short", day: "2-digit", month: "short", year: "numeric",
  });
};

const getStatusConfig = (status) => {
  const map = {
    present:  { label: "Présent",      dot: "bg-emerald-500", badge: "bg-emerald-500/10 text-emerald-500 border-emerald-500/20" },
    absent:   { label: "Absent",       dot: "bg-rose-500",    badge: "bg-rose-500/10 text-rose-500 border-rose-500/20" },
    late:     { label: "Retard",       dot: "bg-amber-500",   badge: "bg-amber-500/10 text-amber-500 border-amber-500/20" },
    half_day: { label: "Demi-journée", dot: "bg-indigo-500",  badge: "bg-indigo-500/10 text-indigo-500 border-indigo-500/20" },
  };
  return map[status] || { label: status, dot: "bg-slate-500", badge: "bg-slate-500/10 text-slate-400 border-slate-500/20" };
};

const getJustifConfig = (a) => {
  if (!a.justification) return { label: "Sans justificatif", badge: "bg-slate-500/10 text-slate-400 border-slate-500/20" };
  if (a.justification.status === "approved") return { label: "Justifiée", badge: "bg-emerald-500/10 text-emerald-500 border-emerald-500/20" };
  if (a.justification.status === "rejected") return { label: "Refusée", badge: "bg-rose-500/10 text-rose-500 border-rose-500/20" };
  return { label: "En attente", badge: "bg-amber-500/10 text-amber-500 border-amber-500/20" };
};

const filters = [
  { key: "all",      label: "Toutes" },
  { key: "present",  label: "Présences" },
  { key: "absent",   label: "Absences" },
  { key: "late",     label: "Retards" },
  { key: "half_day", label: "Demi-jours" },
];

onMounted(async () => {
  await Promise.all([loadMyAbsences(), loadProfile(), loadUnreadCount()]);
  listenToNotifications();
});

onUnmounted(() => stopListening());
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div :class="isDark ? 'bg-slate-950 text-slate-400' : 'bg-slate-50 text-slate-600'" class="min-h-screen font-sans antialiased w-full flex flex-col transition-colors duration-300">

    <nav :class="isDark ? 'bg-slate-950/40 border-slate-900/80 text-slate-200' : 'bg-white/60 border-slate-200 text-slate-800'" class="backdrop-blur-xl px-8 py-4 flex justify-between items-center shadow-2xl border-b shrink-0 sticky top-0 z-50 w-full transition-colors duration-300">
    <div class="flex items-center gap-3.5">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white text-xl font-black shadow-[0_4px_12px_rgba(99,102,241,0.3)] italic">
        D
      </div>
      <div>
        <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-black tracking-wider uppercase italic leading-none">DataXpress</h1>
        <p class="text-[10px] text-indigo-500 dark:text-indigo-400 font-bold tracking-widest uppercase mt-1">Workspace Hub</p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <div :class="isDark ? 'bg-slate-900/40 border-slate-800/60' : 'bg-slate-100 border-slate-200'" class="flex items-center gap-1 p-1.5 rounded-xl border">

        <button @click="router.push('/notifications')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="relative px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fas fa-bell text-sm opacity-60"></i>
          <span class="hidden md:inline">Notifications</span>
          <span v-if="unreadCount > 0"
            class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border border-white dark:border-slate-950">
            {{ unreadCount > 9 ? '9+' : unreadCount }}
          </span>
        </button>

        <button class="px-4 py-2.5 rounded-lg text-xs font-black bg-indigo-600 text-white shadow-[0_4px_12px_rgba(79,70,229,0.25)] flex items-center gap-2">
            <i class="fa-regular fa-calendar-minus text-sm"></i><span class="hidden md:inline">Mes Absences</span>
        </button>

        <button @click="router.push('/Myassignement')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fas fa-folder text-sm opacity-60"></i>
          <span class="hidden md:inline">Assignement</span>
        </button>

        <button @click="router.push('/allprojects')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fas fa-diagram-project text-sm opacity-60"></i>
          <span class="hidden md:inline">Projects</span>
        </button>

        <button
          v-if="user?.role?.name === 'Administrateur'"
          @click="router.push('/dashboard')"
          :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'"
          class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
          <i class="fas fa-gauge text-sm opacity-60"></i>
          <span class="hidden md:inline">Dashboard</span>
        </button>

        <button @click="router.push('/profile')" :class="isDark ? 'text-slate-400 hover:text-slate-200' : 'text-slate-500 hover:text-slate-900'" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
            <i class="fas fa-user text-sm opacity-60"></i><span class="hidden md:inline">Mon Profil</span>
        </button>

      </div>

      <button @click="toggleTheme" :class="isDark ? 'bg-slate-900 text-amber-400 border-slate-800' : 'bg-white text-indigo-600 border-slate-200 shadow-sm'" class="w-10 h-10 rounded-xl border flex items-center justify-center transition-all active:scale-95 text-sm">
        <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'"></i>
      </button>
    </div>
  </nav>


    <main class="flex-1 max-w-6xl w-full mx-auto px-8 md:px-12 py-10 space-y-8">
      <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-indigo-600/[0.04] blur-[160px] rounded-full pointer-events-none"></div>

      <div :class="isDark ? 'border-slate-900' : 'border-slate-200'" class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b">
        <div>
          <h2 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-2xl font-black tracking-tight uppercase">Mes Absences</h2>
          <p class="text-xs text-slate-400 font-medium mt-1">Historique de vos pointages et justifications.</p>
        </div>
        <button @click="router.push('/justification_create')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 self-start">
          <i class="fas fa-plus"></i> Soumettre une justification
        </button>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div v-for="(item, key) in [
          { label: 'Total',       value: stats.total,     icon: 'fa-layer-group', color: 'text-indigo-500', bg: 'bg-indigo-500/10 border-indigo-500/20' },
          { label: 'Présences',   value: stats.present,   icon: 'fa-circle-check', color: 'text-emerald-500', bg: 'bg-emerald-500/10 border-emerald-500/20' },
          { label: 'Absences',    value: stats.absent,    icon: 'fa-circle-xmark', color: 'text-rose-500', bg: 'bg-rose-500/10 border-rose-500/20' },
          { label: 'Retards',     value: stats.late,      icon: 'fa-clock', color: 'text-amber-500', bg: 'bg-amber-500/10 border-amber-500/20' },
          { label: 'Justifiées',  value: stats.justified, icon: 'fa-file-circle-check', color: 'text-teal-500', bg: 'bg-teal-500/10 border-teal-500/20' },
        ]" :key="key"
          :class="isDark ? 'bg-slate-900/30 border-slate-900' : 'bg-white border-slate-200/80 shadow-sm'"
          class="p-4 rounded-2xl border flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl border flex items-center justify-center shrink-0" :class="[item.bg, item.color]">
            <i :class="`fas ${item.icon} text-sm`"></i>
          </div>
          <div>
            <span :class="isDark ? 'text-white' : 'text-slate-900'" class="block text-lg font-black leading-none">{{ item.value }}</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ item.label }}</span>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-2 flex-wrap">
        <button v-for="f in filters" :key="f.key" @click="filterStatus = f.key"
          :class="filterStatus === f.key
            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
            : isDark
              ? 'bg-slate-900/30 text-slate-400 border-slate-900 hover:border-slate-700'
              : 'bg-white text-slate-500 border-slate-200 hover:border-slate-300 shadow-sm'"
          class="px-4 py-2 rounded-xl text-xs font-bold border transition-all">
          {{ f.label }}
          <span v-if="f.key !== 'all'" class="ml-1.5 font-mono text-[10px] opacity-60">
            {{ absences.filter(a => a.status === f.key).length }}
          </span>
        </button>
      </div>

      <div v-if="loading" class="flex flex-col items-center justify-center py-24">
        <div class="w-9 h-9 border-2 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="text-[10px] font-bold text-slate-500 mt-4 uppercase tracking-widest">Chargement...</p>
      </div>

      <div v-else-if="filteredAbsences.length === 0" :class="isDark ? 'bg-slate-900/20 border-slate-900/40' : 'bg-slate-50 border-slate-200'" class="p-16 rounded-2xl border text-center">
        <i class="fa-regular fa-calendar-xmark text-3xl text-slate-300 dark:text-slate-700 mb-3 block"></i>
        <p class="text-sm font-bold text-slate-400">Aucune absence enregistrée.</p>
      </div>

      <!-- TABLEAU CORRIGÉ : grid-cols-12 -->
      <div v-else :class="isDark ? 'bg-slate-900/20 border-slate-900' : 'bg-white border-slate-200/80 shadow-sm'" class="rounded-2xl border overflow-hidden">
        
        <div :class="isDark ? 'border-slate-900/80 bg-slate-900/20' : 'border-slate-100 bg-slate-50/60'" class="grid grid-cols-12 px-5 py-3 border-b">
          <span class="col-span-3 text-[9px] font-black uppercase tracking-widest text-slate-400">Date</span>
          <span class="col-span-2 text-[9px] font-black uppercase tracking-widest text-slate-400">Statut</span>
          <span class="col-span-2 text-[9px] font-black uppercase tracking-widest text-slate-400">Arrivée</span>
          <span class="col-span-2 text-[9px] font-black uppercase tracking-widest text-slate-400">Départ</span>
          <span class="col-span-2 text-[9px] font-black uppercase tracking-widest text-slate-400">Justification</span>
          <span class="col-span-1 text-[9px] font-black uppercase tracking-widest text-slate-400 text-center">Action</span>
        </div>

        <div v-for="a in filteredAbsences" :key="a.id" :class="isDark ? 'border-slate-900/60 hover:bg-slate-900/30' : 'border-slate-100 hover:bg-slate-50/80'" class="grid grid-cols-12 items-center px-5 py-4 border-b last:border-b-0 transition-colors gap-2">
          
          <div class="col-span-3 flex items-center gap-2.5 min-w-0">
            <div class="w-1.5 h-1.5 rounded-full shrink-0" :class="getStatusConfig(a.status).dot"></div>
            <span :class="isDark ? 'text-slate-200' : 'text-slate-800'" class="text-xs font-bold truncate">{{ formatDate(a.date) }}</span>
          </div>

          <div class="col-span-2">
            <span class="text-[9px] font-black px-2 py-1 rounded-full border uppercase tracking-wide" :class="getStatusConfig(a.status).badge">
              {{ getStatusConfig(a.status).label }}
            </span>
          </div>

          <div class="col-span-2 text-xs font-mono" :class="isDark ? 'text-slate-300' : 'text-slate-700'">{{ a.check_in ?? '—' }}</div>
          <div class="col-span-2 text-xs font-mono" :class="isDark ? 'text-slate-300' : 'text-slate-700'">{{ a.check_out ?? '—' }}</div>

          <div class="col-span-2">
            <span class="text-[9px] font-black px-2 py-1 rounded-full border uppercase tracking-wide" :class="getJustifConfig(a).badge">
              {{ getJustifConfig(a).label }}
            </span>
          </div>

          <div class="col-span-1 flex justify-center">
            <button v-if="(a.status === 'absent' || a.status === 'late') && !a.justification" @click="router.push(`/justification_create?absence_id=${a.id}`)" class="px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold transition-all hover:scale-105">
              Justifier
            </button>
            <span v-else :class="isDark ? 'text-slate-500' : 'text-slate-400'" class="text-xs">—</span>
          </div>
        </div>
      </div>

      <p v-if="!loading" class="text-[10px] text-slate-400 text-right font-medium">
        {{ filteredAbsences.length }} enregistrement(s) affiché(s)
      </p>
    </main>
  </div>
</template>