<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()
const loading = ref(false)
const justifications = ref([])

const absenceId = computed(() => route.params.absenceId)

const updateStatus = async (id, status) => {
  try {
    await api.put(
      `/justifications/${id}`,
      { status },
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      },
    )
    loadJustifications()
  } catch (err) {
    console.log(err.response?.data)
  }
}

const loadJustifications = async () => {
  loading.value = true
  try {
    const res = await api.get(`/justifications`, {
      params: { absence_id: absenceId.value },
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    justifications.value = res.data.justifications || []
  } catch (err) {
    console.log(err.response?.data)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadJustifications()
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 p-6 md:p-10">
    <div class="max-w-5xl mx-auto">
      <!-- HEADER -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Absence Justifications</h1>
        <p class="text-slate-500 text-sm mt-1">All uploaded proofs & requests for this absence</p>
      </div>

      <!-- LOADING -->
      <div v-if="loading" class="text-center py-20 text-slate-500">Loading...</div>

      <!-- EMPTY -->
      <div
        v-else-if="justifications.length === 0"
        class="bg-white rounded-3xl p-10 text-center shadow-sm"
      >
        <p class="text-slate-500">No justification found</p>
      </div>

      <!-- LIST -->
      <div v-else class="space-y-6">
        <div
          v-for="j in justifications"
          :key="j.id"
          class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6"
        >
          <!-- TOP -->
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-xl font-bold text-slate-800">
                {{ j.type }}
              </h2>
              <p class="text-slate-500 mt-1">
                {{ j.reason }}
              </p>
            </div>
            <span
              class="px-3 py-1 rounded-full text-xs font-semibold"
              :class="{
                'bg-yellow-100 text-yellow-700': j.status === 'pending',
                'bg-green-100 text-green-700': j.status === 'approved',
                'bg-red-100 text-red-700': j.status === 'rejected',
              }"
            >
              {{ j.status }}
            </span>
          </div>

          <div class="mt-6">
            <p class="text-xs text-slate-400 mb-2">Proof Document</p>
            <div v-if="j.proof_file" class="flex gap-3">
              <a
                :href="`http://localhost:8000/storage/${j.proof_file}`"
                target="_blank"
                class="text-blue-600 underline"
              >
                View PDF
              </a>
              <a
                :href="`http://localhost:8000/storage/${j.proof_file}`"
                download
                class="bg-indigo-600 text-white px-3 py-1 rounded"
              >
                Download PDF
              </a>
            </div>
            <p v-else class="text-slate-400 text-sm">No file uploaded</p>
          </div>

          <!-- ACTIONS -->
          <div class="mt-6 flex gap-3">
            <button
              v-if="j.status === 'pending'"
              @click="updateStatus(j.id, 'approved')"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm"
            >
              Accept
            </button>

            <button
              v-if="j.status === 'pending'"
              @click="updateStatus(j.id, 'rejected')"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm"
            >
              Reject
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
