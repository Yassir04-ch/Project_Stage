<script setup>
import { reactive, ref } from "vue";
import { registerUser } from "@/services/authService";

const form = reactive({
  firstname: "",
  lastname: "",
  email: "",
  password: "",
  password_confirmation: "",
  cin: "",
  telephone: "",
  adresse: "",
  genre: "",
  date_naissance: "",
  date_embauche: "",
  type_contrat: "",
  salaire: "",
  photo: null,
  role_id: 1,
});

const errors = ref({});
const loading = ref(false);

const handleFile = (event) => {
  form.photo = event.target.files[0];
};

const submitRegister = async () => {
  loading.value = true;
  errors.value = {};

  try {
    const formData = new FormData();

    formData.append("firstname", form.firstname);
    formData.append("lastname", form.lastname);
    formData.append("cin", form.cin);
    formData.append("email", form.email);
    formData.append("password", form.password);
    formData.append("password_confirmation", form.password_confirmation);
    formData.append("role_id", form.role_id);

    if (form.telephone) formData.append("telephone", form.telephone);
    if (form.adresse) formData.append("adresse", form.adresse);
    if (form.genre) formData.append("genre", form.genre);
    if (form.date_naissance) formData.append("date_naissance", form.date_naissance);
    if (form.date_embauche) formData.append("date_embauche", form.date_embauche);
    if (form.type_contrat) formData.append("type_contrat", form.type_contrat);
    if (form.salaire) formData.append("salaire", form.salaire);

    if (form.photo) {
      formData.append("photo", form.photo);
    }

    const response = await registerUser(formData);

    localStorage.setItem("token", response.data.token);

    alert("Employé ajouté avec succès !");
    console.log(response);

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
  <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-center font-sans">
    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      
      <div class="p-8 border-b border-slate-100">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Ajouter un Nouvel Employé</h2>
        <p class="mt-1 text-sm text-slate-500">Remplissez les informations ci-dessous pour inscrire le collaborateur dans le système.</p>
      </div>

      <form @submit.prevent="submitRegister" class="p-8 space-y-8">
        
        <!-- SECTION 1: Informations Personnelles -->
        <div>
          <h3 class="text-base font-semibold text-slate-800 mb-5">Informations Personnelles</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Nom</label>
              <input v-model="form.lastname" type="text" placeholder="Nom de l'employé" 
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.lastname ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.lastname">{{ errors.lastname[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Prénom</label>
              <input v-model="form.firstname" type="text" placeholder="Prénom de l'employé" 
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.firstname ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.firstname">{{ errors.firstname[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">CIN</label>
              <input v-model="form.cin" type="text" placeholder="Ex: AB123456" 
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.cin ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.cin">{{ errors.cin[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Genre</label>
              <!-- Zdna bg-none o padding icon bch mayb9ach select m3ye9 -->
              <select v-model="form.genre" class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10 appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2364748b%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.25rem] bg-[right_0.75rem_center] bg-no-repeat pr-10">
                <option value="">Sélectionner le genre</option>
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>
              </select>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Date de Naissance</label>
              <input v-model="form.date_naissance" type="date" class="w-full px-3.5 py-1.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Photo de Profil</label>
              <div class="w-full border border-slate-300 rounded-lg px-3 py-1 bg-white h-10 flex items-center">
                <input type="file" @change="handleFile" class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
              </div>
            </div>
          </div>
        </div>

        <hr class="border-slate-100" />

        <div>
          <h3 class="text-base font-semibold text-slate-800 mb-5">Contact & Adresse</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-medium text-slate-600">Adresse Email Professionnelle</label>
              <input v-model="form.email" type="email" placeholder="exemple@entreprise.com" 
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.email">{{ errors.email[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-medium text-slate-600">Téléphone</label>
              <input v-model="form.telephone" type="text" placeholder="+212 600-000000" class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-medium text-slate-600">Adresse Résidentielle</label>
              <textarea v-model="form.adresse" placeholder="Adresse complète..." class="w-full h-20 px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 resize-none pt-2.5"></textarea>
            </div>
          </div>
        </div>

        <hr class="border-slate-100" />

        <div>
          <h3 class="text-base font-semibold text-slate-800 mb-5">Détails Professionnels</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Type de Contrat</label>
              <select v-model="form.type_contrat" class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10 appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2364748b%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.25rem] bg-[right_0.75rem_center] bg-no-repeat pr-10">
                <option value="">Sélectionner un contrat</option>
                <option value="CDI">CDI</option>
                <option value="CDD">CDD</option>
                <option value="Stage">Stage</option>
                <option value="Freelance">Freelance</option>
                <option value="Interim">Interim</option>
              </select>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Salaire Mensuel (DH)</label>
              <input v-model="form.salaire" type="number" placeholder="Ex: 6000" class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-medium text-slate-600">Date d'embauche</label>
              <input v-model="form.date_embauche" type="date" class="w-full px-3.5 py-1.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>
          </div>
        </div>

        <hr class="border-slate-100" />

        <!-- SECTION 4: Sécurité / Mot de passe -->
        <div>
          <h3 class="text-base font-semibold text-slate-800 mb-5">Sécurité du Compte</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Mot de passe</label>
              <input v-model="form.password" type="password" placeholder="••••••••" 
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.password ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.password">{{ errors.password[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Confirmer le mot de passe</label>
              <input v-model="form.password_confirmation" type="password" placeholder="••••••••" class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>
          </div>
        </div>

        <div class="pt-4 flex justify-end">
          <button type="submit" :disabled="loading" 
            class="w-full sm:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition duration-200 disabled:bg-slate-400 disabled:cursor-not-allowed shadow-sm shadow-indigo-600/10 h-10 flex items-center justify-center">
            <span v-if="loading">Enregistrement en cours...</span>
            <span v-else>Créer le profil de l'employé</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>