<script setup>
import { onMounted, ref } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const router = useRouter();

const users = ref([]);
const loading = ref(false);

const getUsers = async () => {

  loading.value = true;

  try {

    const response = await api.get("/users", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    users.value = response.data.users;

    console.log(users.value);

  } catch (error) {

    console.log(error.response?.data);

  } finally {

    loading.value = false;

  }
};
onMounted(() => {
  getUsers();
});

</script>

<template>
  <div class="min-h-screen bg-gray-100 flex">

    <aside
      class="bg-slate-900 text-white w-72 min-h-screen p-5 flex flex-col justify-between shadow-2xl"
    >
      <div>

        <div class="flex items-center gap-3 mb-10">

          <div
            class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-xl font-bold shadow-lg"
          >
            D
          </div>

          <div>
            <h1 class="text-2xl font-bold">DataExpress</h1>
            <p class="text-gray-400 text-sm">Admin Dashboard</p>
          </div>

        </div>

        <nav class="space-y-3">

          <a
            href="#"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white shadow-lg"
          >
            <span>🏠</span>
            <span>Dashboard</span>
          </a>

          <a
            href="#"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition"
          >
            <span>👥</span>
            <span>Utilisateurs</span>
          </a>

          <a
            href="#"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition"
          >
            <span>📁</span>
            <span>Projects</span>
          </a>

          <a
            href="#"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition"
          >
            <span>📊</span>
            <span>Analytics</span>
          </a>

          <a
            href="#"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition"
          >
            <span>⚙️</span>
            <span>Settings</span>
          </a>

        </nav>
      </div>

      <div
        class="bg-slate-800 rounded-2xl p-4 flex items-center gap-3 shadow-inner"
      >
        <div
          class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center font-bold"
        >
          YC
        </div>

        <div>
          <h2 class="font-semibold">Yassir Cherqui</h2>
          <p class="text-sm text-gray-400">Administrator</p>
        </div>
      </div>
    </aside>

    <main class="flex-1 p-8 overflow-hidden">

      <div
        class="bg-white rounded-3xl shadow-md px-6 py-4 flex justify-between items-center mb-8"
      >

        <div>
          <h1 class="text-3xl font-bold text-slate-800">
            Dashboard
          </h1>

          <p class="text-gray-500 mt-1">
            Welcome back 👋
          </p>
        </div>

        <div class="flex items-center gap-4">

        <button
          @click="router.push('/register')"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium shadow-lg transition duration-300 flex items-center gap-2">
            <span class="text-lg">+</span>
            <span>Ajouter Employee</span>
        </button>

          <button
            class="w-11 h-11 rounded-full bg-indigo-600 text-white shadow-lg"
          >
            🔔
          </button>

        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div
          class="bg-white p-6 rounded-3xl shadow-md hover:shadow-xl transition"
        >
          <p class="text-gray-500">Total Users</p>

          <h2 class="text-4xl font-bold mt-3 text-slate-800">
            120
          </h2>
        </div>

        <div
          class="bg-white p-6 rounded-3xl shadow-md hover:shadow-xl transition"
        >
          <p class="text-gray-500">Projects</p>

          <h2 class="text-4xl font-bold mt-3 text-slate-800">
            35
          </h2>
        </div>

        <div
          class="bg-white p-6 rounded-3xl shadow-md hover:shadow-xl transition"
        >
          <p class="text-gray-500">Revenue</p>

          <h2 class="text-4xl font-bold mt-3 text-slate-800">
            $24K
          </h2>
        </div>

      </div>

      <div class="bg-white rounded-3xl shadow-md overflow-hidden">

        <div class="p-6 border-b">
          <h2 class="text-2xl font-bold text-slate-800">
            Users List
          </h2>
        </div>

        <table class="w-full">

          <thead class="bg-gray-50">
            <tr>

              <th class="text-left p-4 text-gray-600">User</th>
              <th class="text-left p-4 text-gray-600">Email</th>
              <th class="text-left p-4 text-gray-600">salaire</th>
              <th class="text-left p-4 text-gray-600">CIN</th>
              <th class="text-left p-4 text-gray-600">Role</th>
              <th class="text-left p-4 text-gray-600">Status</th>
              <th class="text-left p-4 text-gray-600">Profile</th>

            </tr>
          </thead>

          <tbody>

            <tr
              v-for="user in users"
              :key="user.id"
              class="border-b hover:bg-gray-50 transition"
            >

              <td class="p-4">

                <div class="flex items-center gap-3">

                  <div
                    class="w-11 h-11 rounded-full bg-indigo-600/10 text-indigo-700 font-bold flex items-center justify-center uppercase"
                  >
                    {{ user.firstname[0] }}{{ user.lastname[0] }}
                  </div>

                  <div>
                    <h3 class="font-semibold text-slate-800">
                      {{ user.firstname }} {{ user.lastname }}
                    </h3>

                  </div>

                </div>

              </td>

              <td class="p-4 text-gray-600">
                {{ user.email }}
              </td>

              <td class="p-4">
                 <h3 class="font-semibold text-slate-800">
                    {{ user.salaire }}
                 </h3>
              </td>

               <td class="p-4">
                 <h3 class="font-semibold text-slate-800">
                    {{ user.cin }}
                 </h3>
              </td>

              <td class="p-4">
                <span
                  class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-medium"
                >
                  {{ user.role.name }}
                </span>
              </td>

              <td class="p-4">
                <span
                  class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium"
                >
                  Active
                </span>
              </td>

            </tr>

          </tbody>

        </table>

      </div>

    </main>

  </div>
</template>