<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { loginUser } from "@/services/authService";

const router = useRouter();

const form = reactive({
  email: "",
  password: "",
});

const errors = ref({});
const loading = ref(false);

const submitlogin = async () => {
  loading.value = true;
  errors.value = {};

  try {
    const response = await loginUser(form);
    localStorage.setItem("token", response.data.data.token);
    const user = response.data.data.user;
    console.log(user);

    router.push("/profile");

  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.log(error.response?.data);
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center font-sans">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      
      <div class="p-8 border-b border-slate-100 text-center">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Espace Connexion</h2>
        <p class="mt-1 text-sm text-slate-500">Connectez-vous pour accéder à votre espace de gestion.</p>
      </div>

      <form @submit.prevent="submitlogin" class="p-8 space-y-6">
        
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-medium text-slate-600">Adresse Email</label>
          <input 
            v-model="form.email" 
            type="email" 
            placeholder="exemple@entreprise.com" 
            class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
            :class="errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-slate-300'" 
          />
          <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.email">
            {{ errors.email[0] }}
          </p>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-medium text-slate-600">Mot de passe</label>
          <input 
            v-model="form.password" 
            type="password" 
            placeholder="••••••••" 
            class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
            :class="errors.password ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-slate-300'" 
          />
          <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.password">
            {{ errors.password[0] }}
          </p>
        </div>

        <div class="pt-2">
          <button 
            type="submit" 
            :disabled="loading" 
            class="w-full px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition duration-200 disabled:bg-slate-400 disabled:cursor-not-allowed shadow-sm shadow-indigo-600/10 h-10 flex items-center justify-center"
          >
            <span v-if="loading">Connexion en cours...</span>
            <span v-else>Se connecter</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>