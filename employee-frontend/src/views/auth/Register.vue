<script setup>
import { reactive, ref, onMounted } from "vue";
import { registerUser } from "@/services/authService";
import api from "@/api/axios";

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

const errors         = ref({});
const loading        = ref(false);
const availableSkills = ref([]);
const selectedSkills  = ref([]);

const handleFile = (event) => {
  form.photo = event.target.files[0];
};

const loadSkills = async () => {
  try {
    const res = await api.get("/skills");
    availableSkills.value = res.data.skills;
  } catch (err) {
    console.log(err);
  }
};

const toggleSkill = (skillId) => {
  const exists = selectedSkills.value.find((s) => s.id === skillId);
  if (exists) {
    selectedSkills.value = selectedSkills.value.filter((s) => s.id !== skillId);
  } else {
    selectedSkills.value.push({ id: skillId, level: "beginner" });
  }
};

const isSelected = (skillId) => selectedSkills.value.some((s) => s.id === skillId);
const getLevel   = (skillId) => selectedSkills.value.find((s) => s.id === skillId)?.level ?? "beginner";
const setLevel   = (skillId, level) => {
  const s = selectedSkills.value.find((s) => s.id === skillId);
  if (s) s.level = level;
};

const submitRegister = async () => {
  loading.value = true;
  errors.value  = {};

  try {
    const formData = new FormData();
    formData.append("firstname", form.firstname);
    formData.append("lastname", form.lastname);
    formData.append("cin", form.cin);
    formData.append("email", form.email);
    formData.append("password", form.password);
    formData.append("password_confirmation", form.password_confirmation);
    formData.append("role_id", form.role_id);

    if (form.telephone)      formData.append("telephone", form.telephone);
    if (form.adresse)        formData.append("adresse", form.adresse);
    if (form.genre)          formData.append("genre", form.genre);
    if (form.date_naissance) formData.append("date_naissance", form.date_naissance);
    if (form.date_embauche)  formData.append("date_embauche", form.date_embauche);
    if (form.type_contrat)   formData.append("type_contrat", form.type_contrat);
    if (form.salaire)        formData.append("salaire", form.salaire);
    if (form.photo)          formData.append("photo", form.photo);

    const response = await registerUser(formData);

    console.log("REGISTER RESPONSE:", response);

    const employeeId = response.data.data.user.id;

    console.log("EMPLOYEE ID:", employeeId);
 
    if (selectedSkills.value.length > 0) {
      await api.post(`/users/${employeeId}/skills`, {
        skills: selectedSkills.value,
      });
    }

    alert("Employé ajouté avec succès !");

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

onMounted(() => loadSkills());
</script>

<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-center font-sans">
    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

      <div class="p-8 border-b border-slate-100">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Ajouter un Nouvel Employé</h2>
        <p class="mt-1 text-sm text-slate-500">Remplissez les informations ci-dessous pour inscrire le collaborateur dans le système.</p>
      </div>

      <form @submit.prevent="submitRegister" class="p-8 space-y-8">

        <!-- Informations Personnelles -->
        <div>
          <h3 class="text-base font-semibold text-slate-800 mb-5">Informations Personnelles</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Nom</label>
              <input v-model="form.lastname" type="text" placeholder="Nom de l'employé"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.lastname ? 'border-red-500' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.lastname">{{ errors.lastname[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Prénom</label>
              <input v-model="form.firstname" type="text" placeholder="Prénom de l'employé"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.firstname ? 'border-red-500' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.firstname">{{ errors.firstname[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">CIN</label>
              <input v-model="form.cin" type="text" placeholder="Ex: AB123456"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.cin ? 'border-red-500' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.cin">{{ errors.cin[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Genre</label>
              <select v-model="form.genre"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10 appearance-none">
                <option value="">Sélectionner le genre</option>
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>
              </select>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Date de Naissance</label>
              <input v-model="form.date_naissance" type="date"
                class="w-full px-3.5 py-1.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Photo de Profil</label>
              <div class="w-full border border-slate-300 rounded-lg px-3 py-1 bg-white h-10 flex items-center">
                <input type="file" @change="handleFile"
                  class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
              </div>
            </div>

          </div>
        </div>

        <hr class="border-slate-100" />

        <!-- Contact -->
        <div>
          <h3 class="text-base font-semibold text-slate-800 mb-5">Contact & Adresse</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-medium text-slate-600">Adresse Email Professionnelle</label>
              <input v-model="form.email" type="email" placeholder="exemple@entreprise.com"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.email ? 'border-red-500' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.email">{{ errors.email[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-medium text-slate-600">Téléphone</label>
              <input v-model="form.telephone" type="text" placeholder="+212 600-000000"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-medium text-slate-600">Adresse Résidentielle</label>
              <textarea v-model="form.adresse" placeholder="Adresse complète..."
                class="w-full h-20 px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 resize-none pt-2.5"></textarea>
            </div>

          </div>
        </div>

        <hr class="border-slate-100" />

        <!-- Détails Professionnels -->
        <div>
          <h3 class="text-base font-semibold text-slate-800 mb-5">Détails Professionnels</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Type de Contrat</label>
              <select v-model="form.type_contrat"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10 appearance-none">
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
              <input v-model="form.salaire" type="number" placeholder="Ex: 6000"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-medium text-slate-600">Date d'embauche</label>
              <input v-model="form.date_embauche" type="date"
                class="w-full px-3.5 py-1.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>

          </div>
        </div>

        <hr class="border-slate-100" />

        <!-- ✅ Compétences -->
        <div>
          <h3 class="text-base font-semibold text-slate-800 mb-1">Compétences</h3>
          <p class="text-xs text-slate-400 mb-5">Cliquez sur une compétence pour la sélectionner, puis choisissez le niveau.</p>

          <div v-if="availableSkills.length === 0"
            class="text-sm text-slate-400 py-6 text-center bg-slate-50 rounded-xl border border-slate-100">
            Aucune compétence disponible — ajoutez-en depuis la page Compétences.
          </div>

          <div v-else class="flex flex-wrap gap-3">
            <div v-for="skill in availableSkills" :key="skill.id" class="flex flex-col gap-1.5">

              <!-- Toggle -->
              <button type="button"
                @click="toggleSkill(skill.id)"
                :class="isSelected(skill.id)
                  ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-200'
                  : 'bg-white text-slate-600 border-slate-300 hover:border-indigo-400 hover:text-indigo-600'"
                class="px-3.5 py-1.5 rounded-lg border text-xs font-semibold transition duration-200">
                {{ skill.name }}
              </button>

              <!-- Level -->
              <select v-if="isSelected(skill.id)"
                :value="getLevel(skill.id)"
                @change="setLevel(skill.id, $event.target.value)"
                class="text-xs border border-slate-200 rounded-lg px-2 py-1 outline-none focus:border-indigo-500 bg-white text-slate-700">
                <option value="beginner">🟢 Débutant</option>
                <option value="intermediate">🟡 Intermédiaire</option>
                <option value="advanced">🔴 Avancé</option>
              </select>

            </div>
          </div>

          <!-- Counter -->
          <p v-if="selectedSkills.length > 0" class="text-xs text-indigo-600 font-semibold mt-4">
            ✓ {{ selectedSkills.length }} compétence(s) sélectionnée(s)
          </p>
        </div>

        <hr class="border-slate-100" />

        <!-- Sécurité -->
        <div>
          <h3 class="text-base font-semibold text-slate-800 mb-5">Sécurité du Compte</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Mot de passe</label>
              <input v-model="form.password" type="password" placeholder="••••••••"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10"
                :class="errors.password ? 'border-red-500' : 'border-slate-300'" />
              <p class="text-xs font-medium text-red-500 mt-0.5" v-if="errors.password">{{ errors.password[0] }}</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-600">Confirmer le mot de passe</label>
              <input v-model="form.password_confirmation" type="password" placeholder="••••••••"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition duration-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 h-10" />
            </div>

          </div>
        </div>

        <!-- Submit -->
        <div class="pt-4 flex justify-end">
          <button type="submit" :disabled="loading"
            class="w-full sm:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition duration-200 disabled:bg-slate-400 disabled:cursor-not-allowed shadow-sm shadow-indigo-600/10 h-10 flex items-center justify-center gap-2">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span>{{ loading ? 'Enregistrement...' : 'Créer le profil de l\'employé' }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>