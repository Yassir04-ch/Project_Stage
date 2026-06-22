<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'

const skills     = ref([])
const loading    = ref(false)
const creating   = ref(false)
const showForm   = ref(false)
const newSkillName = ref('')
const success    = ref('')
const error      = ref('')
const formError  = ref('')

const loadSkills = async () => {
  loading.value = true
  try {
    const res = await api.get('/skills')
    skills.value = res.data.skills
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

onMounted(() => loadSkills())
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex">

    <!-- SIDEBAR -->
    <aside class="bg-slate-900 text-white w-72 min-h-screen p-5 flex flex-col justify-between shadow-2xl">
      <div>
        <div class="flex items-center gap-3 mb-10">
          <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-xl font-bold shadow-lg">D</div>
          <div>
            <h1 class="text-2xl font-bold">DataXpress</h1>
            <p class="text-gray-400 text-sm">Admin Dashboard</p>
          </div>
        </div>
        <nav class="space-y-2">
          <router-link to="/" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition text-gray-300 hover:text-white" active-class="bg-indigo-600 text-white">
            <span>🏠</span><span>Dashboard</span>
          </router-link>
          <router-link to="/users" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition text-gray-300 hover:text-white" active-class="bg-indigo-600 text-white">
            <span>👥</span><span>Utilisateurs</span>
          </router-link>
          <router-link to="/projects" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition text-gray-300 hover:text-white" active-class="bg-indigo-600 text-white">
            <span>📁</span><span>Projets</span>
          </router-link>
          <router-link to="/absences" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition text-gray-300 hover:text-white" active-class="bg-indigo-600 text-white">
            <span>📋</span><span>Absences</span>
          </router-link>
          <router-link to="/skills" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition text-gray-300 hover:text-white" active-class="bg-indigo-600 text-white">
            <span>🛠️</span><span>Compétences</span>
          </router-link>
        </nav>
      </div>
      <router-link to="/profile" class="bg-slate-800 rounded-2xl p-4 flex items-center gap-3 hover:bg-slate-700 transition">
        <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white text-sm uppercase">A</div>
        <div>
          <p class="font-semibold text-white text-sm">Admin</p>
          <p class="text-xs text-gray-400">Administrateur</p>
        </div>
      </router-link>
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

      <!-- Flash -->
      <div v-if="success" class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        ✅ {{ success }}
      </div>
      <div v-if="error" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        ❌ {{ error }}
      </div>

      <!-- Add form -->
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