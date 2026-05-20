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

const getError = (field) => {
  return errors.value?.[field]?.[0];
};

const submitProject = async () => {
  if (loading.value) return;
  loading.value = true;
  errors.value = {};

  try {
    await createProject(form);

    alert("Project créé avec succès !");

    form.name = "";
    form.description = "";
    form.budget = "";
    form.start_date = "";
    form.end_date = "";
    form.status = "planning";

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
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-6">

    <div class="w-full max-w-2xl bg-white rounded-xl shadow p-6">

      <h2 class="text-xl font-bold mb-6">Créer un Project</h2>

      <form @submit.prevent="submitProject" class="space-y-4">

        <div>
          <input
            v-model="form.name"
            placeholder="Project name"
            class="border p-2 w-full rounded"/>
          <p v-if="getError('name')" class="text-red-500 text-sm">
            {{ getError('name') }}
          </p>
        </div>

        <textarea
          v-model="form.description"
          placeholder="Description"
          class="border p-2 w-full rounded"></textarea>

        <input
          v-model="form.budget"
          type="number"
          placeholder="Budget"
          class="border p-2 w-full rounded"/>
        <p v-if="getError('budget')" class="text-red-500 text-sm">
          {{ getError('budget') }}
        </p>

        <input
          v-model="form.start_date"
          type="date"
          class="border p-2 w-full rounded"/>
        <p v-if="getError('start_date')" class="text-red-500 text-sm">
          {{ getError('start_date') }}
        </p>

        <input
          v-model="form.end_date"
          type="date"
          class="border p-2 w-full rounded"/>
        <p v-if="getError('end_date')" class="text-red-500 text-sm">
          {{ getError('end_date') }}
        </p>

        <select v-model="form.status" class="border p-2 w-full rounded">
          <option value="planning">Planning</option>
          <option value="active">Active</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>

        <button
          type="submit"
          :disabled="loading"
          class="bg-indigo-600 text-white px-4 py-2 rounded w-full disabled:opacity-50">
          {{ loading ? "Saving..." : "Create Project" }}
        </button>

      </form>

    </div>
  </div>
</template>