<template>
  <aside class="bg-slate-950 text-slate-200 w-72 min-h-screen p-6 flex flex-col justify-between shadow-2xl shrink-0 border-r border-slate-800/40">
    <div class="flex flex-col">
      <div class="flex items-center gap-3.5 mb-10 pb-4 border-b border-slate-900">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-indigo-500/20">D</div>
        <div>
          <h1 class="text-lg font-black tracking-tight text-white uppercase italic">DATAXPRESS</h1>
          <p class="text-xs text-indigo-400 font-bold tracking-wider uppercase">Workspace Admin</p>
        </div>
      </div>

      <div class="space-y-2">
        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Workspace</p>
        <nav class="space-y-1.5">
          <button @click="router.push('/dashboard')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
            <span class="text-base opacity-70 group-hover:opacity-100">🏠</span><span>Dashboard</span>
          </button>
          <button @click="router.push('/projects')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
            <span class="text-base opacity-70 group-hover:opacity-100">📁</span><span>Projets</span>
          </button>
          <button @click="router.push('/users')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
            <span class="text-base opacity-70 group-hover:opacity-100">👥</span><span>Utilisateurs</span>
          </button>
          <button @click="router.push('/absences')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
            <i class="fa-regular fa-calendar-minus text-base w-5"></i><span>Absences</span>
          </button>
          <button @click="router.push('/skills')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
            <i class="fa-solid fa-brain text-base w-5"></i><span>Compétences</span>
          </button>
          <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 text-sm text-left">
            <i class="fas fa-building text-base w-5"></i><span>Services</span>
          </button>
        </nav>
      </div>
    </div>

    <div class="mt-auto pt-4 border-t border-slate-900">
      <router-link to="/profile" class="bg-slate-900/80 border border-slate-800/40 rounded-xl p-3.5 flex items-center gap-3 hover:bg-slate-900 transition-all group cursor-pointer w-full">
        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center font-black text-white text-sm uppercase shrink-0">
          <span v-if="currentUser">{{ currentUser.firstname?.[0] }}{{ currentUser.lastname?.[0] }}</span>
          <span v-else class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
        </div>
        <div class="flex-1 min-w-0">
          <h2 v-if="currentUser" class="text-sm font-bold text-white truncate uppercase group-hover:text-indigo-400 transition-colors">
            {{ currentUser.firstname }} {{ currentUser.lastname }}
          </h2>
          <p v-if="currentUser" class="text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-0.5 truncate">
            {{ currentUser.role?.name ?? 'Admin' }}
          </p>
        </div>
        <span class="text-slate-600 group-hover:text-slate-400 transition-colors text-xs shrink-0">➔</span>
      </router-link>
    </div>
  </aside>
</template>

<script setup>
import { useRouter } from "vue-router";

defineProps({
  currentUser: {
    type: Object,
    default: null,
  },
});

const router = useRouter();
</script>