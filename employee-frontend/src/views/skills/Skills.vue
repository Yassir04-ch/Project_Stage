<script setup>
import { onMounted, onUnmounted, ref, computed } from "vue";
import api from '@/api/axios'
import { useRouter } from "vue-router";


const router = useRouter();

const skills     = ref([])
const loading    = ref(false)
const creating   = ref(false)
const showForm   = ref(false)
const newSkillName = ref('')
const success    = ref('')
const error      = ref('')
const formError  = ref('')
const currentUser = ref(null);



const loadSkills = async () => {
  loading.value = true
  try {

     const response = await api.get("/skills", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    const res = await api.get('/skills')
    skills.value = res.data.skills
    currentUser.value = response.data.admin || null;

  } catch (err) {
    error.value = 'Erreur lors du chargement'
  } finally {
    loading.value = false
  }
}

const createSkill = async () => {
  if (!newSkillName.value.trim()) {
    formError.value = 'Le nom est obligatoire'
    return
  }
  creating.value = true
  formError.value = ''
  try {
    const res = await api.post('/skills', { name: newSkillName.value.trim() })
    skills.value.push(res.data.skill)
    newSkillName.value = ''
    showForm.value = false
    success.value = 'Compétence créée avec succès'
    setTimeout(() => success.value = '', 3000)
  } catch (err) {
    formError.value = err.response?.data?.errors?.name?.[0] ?? 'Erreur lors de la création'
  } finally {
    creating.value = false
  }
}

const deleteSkill = async (skill) => {
  if (!confirm(`Supprimer "${skill.name}" ?`)) return
  try {
    await api.delete(`/skills/${skill.id}`)
    skills.value = skills.value.filter(s => s.id !== skill.id)
    success.value = 'Compétence supprimée'
    setTimeout(() => success.value = '', 3000)
  } catch (err) {
    error.value = 'Erreur lors de la suppression'
    setTimeout(() => error.value = '', 3000)
  }
}

const canAccessSkills = computed(() =>
  ["Administrateur"].includes(currentUser.value?.role?.name)
);

onMounted(() => loadSkills())
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-gray-100 flex">

    <aside class="bg-slate-900 text-white w-72 min-h-screen p-5 flex flex-col justify-between shadow-2xl">
      <div>
         <div class="flex items-center gap-3.5 mb-10 pb-4 border-b border-slate-900">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-indigo-500/20">
            D
          </div>
          <div>
            <h1 class="text-lg font-black tracking-tight text-white uppercase italic">DATAXPRESS</h1>
            <p class="text-xs text-indigo-400 font-bold tracking-wider uppercase">Workspace Admin</p>
          </div>
        </div>
     <div class="space-y-2">
          <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Workspace</p>     
       <nav class="space-y-1.5">
            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">🏠</span>
              <span>Dashboard</span>
            </button>

            <button v-if="canAccessSkills" @click="router.push('/projects')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left"">
              <span class="text-base group-hover:scale-110 transition-transform">📁</span>
              <span>Projects</span>
            </button>

            <button @click="router.push('/users')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">👥</span>
              <span>Utilisateurs</span>
            </button>

            <button @click="router.push('/absences')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fa-regular fa-calendar-minus text-base w-5 group-hover:scale-110 transition-transform"></i>
              <span>Absences</span>
            </button>

            <button @click="router.push('/skills')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 group transition-all text-sm text-left"">
              <i class="fa-solid fa-brain text-base w-5 group-hover:scale-110 transition-transform"></i>
              <span>Compétences</span>
            </button>
             <button v-if="canAccessSkills" @click="router.push('/services')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fas fa-building text-base w-5"></i><span>Services</span>
            </button>
          </nav>
      </div>
    </div>

       <div class="mt-auto pt-4 border-t border-slate-900">
        <router-link
          to="/profile"
          class="bg-slate-900/80 border border-slate-800/40 rounded-xl p-3.5 flex items-center gap-3 shadow-inner hover:bg-slate-900 hover:border-slate-700/60 transition-all group cursor-pointer w-full"
        >
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center font-black text-white text-sm uppercase tracking-wide border border-indigo-400/20 shrink-0">
            <span v-if="currentUser">{{ currentUser.firstname?.[0] }}{{ currentUser.lastname?.[0] }}</span>
            <span v-else class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
          </div>

          <div class="flex-1 min-w-0">
            <div v-if="currentUser">
              <h2 class="text-sm font-bold text-white tracking-tight truncate uppercase group-hover:text-indigo-400 transition-colors">
                {{ currentUser.firstname }} {{ currentUser.lastname }}
              </h2>
              <p class="text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-0.5 truncate">
                {{ currentUser.role?.name ?? 'Admin' }}
              </p>
            </div>
            
            <div v-else class="space-y-1">
              <div class="h-3 w-24 bg-slate-800 rounded animate-pulse"></div>
              <div class="h-2 w-16 bg-slate-800 rounded animate-pulse"></div>
            </div>
          </div>

          <span class="text-slate-600 group-hover:text-slate-400 transition-colors text-xs shrink-0 pl-1">➔</span>
        </router-link>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 p-8 overflow-y-auto">

      <!-- Topbar -->
      <div class="bg-white rounded-3xl shadow-sm px-6 py-4 flex justify-between items-center mb-8 border border-gray-100">
        <div>
          <h1 class="text-2xl font-bold text-slate-800">Compétences</h1>
          <p class="text-gray-400 text-sm mt-0.5">Gérer les compétences disponibles</p>
        </div>
        <button @click="showForm = true"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium shadow-lg transition flex items-center gap-2">
          <span>+</span> Nouvelle compétence
        </button>
      </div>

      <div v-if="success" class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        ✅ {{ success }}
      </div>
      <div v-if="error" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        ❌ {{ error }}
      </div>

       <div v-if="showForm" class="bg-white rounded-2xl border border-slate-200 p-6 mb-6 shadow-sm">
        <h2 class="text-base font-semibold text-slate-800 mb-4">Ajouter une compétence</h2>
        <div class="flex gap-3">
          <input v-model="newSkillName" type="text" placeholder="ex: Laravel, Vue.js, Docker..."
            class="flex-1 px-4 py-2.5 border border-slate-300 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
            @keyup.enter="createSkill"
          />
          <button @click="createSkill" :disabled="creating"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition disabled:opacity-60">
            {{ creating ? '...' : 'Ajouter' }}
          </button>
          <button @click="showForm = false; newSkillName = ''"
            class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-100 transition">
            Annuler
          </button>
        </div>
        <p v-if="formError" class="text-red-500 text-xs mt-2">{{ formError }}</p>
      </div>

      <!-- Skills list -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
            {{ skills.length }} compétence(s)
          </p>
        </div>

        <div v-if="loading" class="p-10 text-center">
          <div class="w-8 h-8 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
        </div>

        <div v-else-if="skills.length === 0" class="p-10 text-center text-slate-400 text-sm">
          Aucune compétence trouvée — commencez par en ajouter une.
        </div>

        <div v-else class="divide-y divide-slate-100">
          <div v-for="skill in skills" :key="skill.id"
            class="flex items-center justify-between px-5 py-4 hover:bg-slate-50 transition group">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold">
                {{ skill.name[0].toUpperCase() }}
              </div>
              <span class="text-sm font-semibold text-slate-800">{{ skill.name }}</span>
            </div>
            <button @click="deleteSkill(skill)"
              class="opacity-0 group-hover:opacity-100 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-3 py-1.5 rounded-xl text-xs font-medium transition">
              🗑️ Supprimer
            </button>
          </div>
        </div>
      </div>

    </main>
  </div>
</template>