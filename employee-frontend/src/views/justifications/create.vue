<script setup>
import { ref, onMounted } from "vue";
import api from "@/api/axios";

const justifications = ref([]);
const loading = ref(false);

const form = ref({
  absence_id: "",
  justified_by: "",
  type: "",
  reason: "",
  status: "pending",
  proof_file: null,
});

const getJustifications = async () => {
  try {

    const response = await api.get("/justifications", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    justifications.value =
      response.data.justifications;

  } catch (error) {

    console.log(error.response?.data);

  }
};

const handleFile = (e) => {

  form.value.proof_file =
    e.target.files[0];

};

const createJustification = async () => {

  loading.value = true;

  try {

    const formData = new FormData();

    Object.keys(form.value).forEach((key) => {

      formData.append(
        key,
        form.value[key]
      );

    });

    await api.post(
      "/justifications",
      formData,
      {
        headers: {
          Authorization:
            `Bearer ${localStorage.getItem("token")}`,
          "Content-Type":
            "multipart/form-data",
        },
      }
    );

    alert("created");

    getJustifications();

  } catch (error) {

    console.log(error.response?.data);

  } finally {

    loading.value = false;

  }
};

    onMounted(() => {

    getJustifications();

    });
    </script>

<template>

    <div class="p-8 bg-gray-100 min-h-screen">

    <h1 class="text-3xl font-bold mb-6">
    Justifications
    </h1>

    <div class="bg-white rounded-2xl shadow p-6 mb-8">

    <div class="grid md:grid-cols-2 gap-4">

    <input
    v-model="form.absence_id"
    placeholder="Absence ID"
    class="border p-3 rounded-xl"
    />

    <input
    v-model="form.justified_by"
    placeholder="User ID"
    class="border p-3 rounded-xl"
    />

    <select
    v-model="form.type"
    class="border p-3 rounded-xl"
    >

    <option value="">
    Type
    </option>

    <option value="medical">
    Medical
    </option>

    <option value="family">
    Family
    </option>

    <option value="mission">
    Mission
    </option>

    <option value="other">
    Other
    </option>

    </select>

    <select
    v-model="form.status"
    class="border p-3 rounded-xl"
    >

    <option value="pending">
    Pending
    </option>

    <option value="approved">
    Approved
    </option>

    <option value="rejected">
    Rejected
    </option>

    </select>

    <textarea
    v-model="form.reason"
    placeholder="Reason"
    class="border p-3 rounded-xl col-span-2"
    />

    <input
    type="file"
    @change="handleFile"
    />

    </div>

    <button
    @click="createJustification"
    class="mt-5 bg-indigo-600 text-white px-5 py-3 rounded-xl"
    >

    {{ loading ? "Loading..." : "Ajouter" }}

    </button>

    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">

    <table class="w-full">

    <thead class="bg-gray-50">

    <tr>

    <th class="p-4 text-left">
    ID
    </th>

    <th class="p-4 text-left">
    Type
    </th>

    <th class="p-4 text-left">
    Reason
    </th>

    <th class="p-4 text-left">
    Status
    </th>

    </tr>

    </thead>

    <tbody>

    <tr
    v-for="j in justifications"
    :key="j.id"
    class="border-b"
    >

    <td class="p-4">
    {{ j.id }}
    </td>

    <td class="p-4">
    {{ j.type }}
    </td>

    <td class="p-4">
    {{ j.reason }}
    </td>

    <td class="p-4">

    <span
    class="px-3 py-1 rounded-full text-sm"
    :class="{
    'bg-yellow-100 text-yellow-700':
    j.status==='pending',

    'bg-green-100 text-green-700':
    j.status==='approved',

    'bg-red-100 text-red-700':
    j.status==='rejected'
    }"
    >

    {{ j.status }}

    </span>

    </td>

    </tr>

    </tbody>

    </table>

    </div>

    </div>

</template>