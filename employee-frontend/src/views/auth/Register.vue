<script setup>
import { reactive, ref, onMounted } from "vue";
import { registerUser } from "@/services/authService";
import { useRouter } from "vue-router";
import api from "@/api/axios";

const router = useRouter();

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
  role_id: 4, // Default nkhaliwh Employé (4) awla bdlo l-li bghiti
});

const errors         = ref({});
const loading        = ref(false);
const availableSkills = ref([]);
const selectedSkills  = ref([]);

const roles = [
  { id: 1, name: "Administrateur", desc: "Accès complet à toute l'application", icon: "⚡" },
  { id: 2, name: "Manager", desc: "Gestion des projets et des équipes", icon: "📊" },
  { id: 3, name: "Ressources Humaines", desc: "Gestion administrative des employés", icon: "👥" },
  { id: 4, name: "Employé", desc: "Accès limité aux informations personnelles", icon: "👤" }
];

const handleFile = (event) => {
  form.photo = event.target.files[0];
  console.log('Photo selected:', form.photo);
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

    console.log('FormData photo:', formData.get('photo'));

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
      console.log("VALIDATION ERRORS:", error.response.data.errors); 
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
  <div class="min-h-screen bg-[#f8fafc] py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-center font-sans relative overflow-hidden">
    
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[400px] bg-gradient-to-b from-indigo-50/50 via-slate-50/10 to-transparent rounded-full blur-3xl -z-10"></div>

    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-[0_4px_30px_rgba(15,23,42,0.04)] border border-slate-200/80 overflow-hidden">

      <div class="p-8 sm:p-10 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100/60 text-indigo-700 text-xs font-semibold uppercase tracking-wider mb-3">
            💼 Administration HR
          </div>
          <h2 class="text-2xl font-bold text-slate-900 tracking-tight sm:text-3xl">Ajouter un Nouvel Employé</h2>
          <p class="mt-1 text-sm text-slate-500">Inscrire un collaborateur et configurer ses paramètres d'accès & compétences.</p>
        </div>
      </div>

      <form @submit.prevent="submitRegister" class="p-8 sm:p-10 space-y-10">

        <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100/80 space-y-6">
          <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
            <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-bold">01</span>
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Informations Personnelles</h3>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-slate-600">Nom <span class="text-rose-500">*</span></label>
              <input v-model="form.lastname" type="text" placeholder="Nom de l'employé"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm"
                :class="errors.lastname ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-50' : 'border-slate-200'" />
              <p class="text-xs font-medium text-rose-500 mt-0.5 flex items-center gap-1" v-if="errors.lastname">
                <span>⚠️</span> {{ errors.lastname[0] }}
              </p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-slate-600">Prénom <span class="text-rose-500">*</span></label>
              <input v-model="form.firstname" type="text" placeholder="Prénom de l'employé"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm"
                :class="errors.firstname ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-50' : 'border-slate-200'" />
              <p class="text-xs font-medium text-rose-500 mt-0.5 flex items-center gap-1" v-if="errors.firstname">
                <span>⚠️</span> {{ errors.firstname[0] }}
              </p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-slate-600">CIN <span class="text-rose-500">*</span></label>
              <input v-model="form.cin" type="text" placeholder="Ex: AB123456"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm"
                :class="errors.cin ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-50' : 'border-slate-200'" />
              <p class="text-xs font-medium text-rose-500 mt-0.5 flex items-center gap-1" v-if="errors.cin">
                <span>⚠️</span> {{ errors.cin[0] }}
              </p>
            </div>

            <div class="flex flex-col gap-1.5 relative">
              <label class="text-xs font-semibold text-slate-600">Genre</label>
              <div class="relative">
                <select v-model="form.genre"
                  class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm appearance-none cursor-pointer">
                  <option value="">Sélectionner le genre</option>
                  <option value="homme">Homme</option>
                  <option value="femme">Femme</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
              </div>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-slate-600">Date de Naissance</label>
              <input v-model="form.date_naissance" type="date"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm cursor-pointer" />
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-slate-600">Photo de Profil</label>
              <div class="w-full border border-slate-200 rounded-xl px-3 bg-white h-11 flex items-center shadow-sm hover:border-slate-300 transition-colors">
                <input type="file" @change="handleFile"
                  class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
              </div>
            </div>
          </div>
        </div>

        <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100/80 space-y-6">
          <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
            <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-bold">02</span>
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Contact & Coordonnées</h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-semibold text-slate-600">Adresse Email Professionnelle <span class="text-rose-500">*</span></label>
              <input v-model="form.email" type="email" placeholder="exemple@entreprise.com"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm"
                :class="errors.email ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-50' : 'border-slate-200'" />
              <p class="text-xs font-medium text-rose-500 mt-0.5 flex items-center gap-1" v-if="errors.email">
                <span>⚠️</span> {{ errors.email[0] }}
              </p>
            </div>

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-semibold text-slate-600">Téléphone</label>
              <input v-model="form.telephone" type="text" placeholder="+212 600-000000"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm" />
            </div>

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-semibold text-slate-600">Adresse Résidentielle</label>
              <textarea v-model="form.adresse" placeholder="Adresse complète..."
                class="w-full h-24 px-3.5 py-3 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 resize-none shadow-sm"></textarea>
            </div>
          </div>
        </div>

        <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100/80 space-y-6">
          <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
            <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-bold">03</span>
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Détails Professionnels</h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex flex-col gap-1.5 relative">
              <label class="text-xs font-semibold text-slate-600">Type de Contrat</label>
              <div class="relative">
                <select v-model="form.type_contrat"
                  class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm appearance-none cursor-pointer">
                  <option value="">Sélectionner un contrat</option>
                  <option value="CDI">CDI</option>
                  <option value="CDD">CDD</option>
                  <option value="Stage">Stage</option>
                  <option value="Freelance">Freelance</option>
                  <option value="Interim">Interim</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
              </div>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-slate-600">Salaire Mensuel (DH)</label>
              <div class="relative rounded-xl shadow-sm">
                <input v-model="form.salaire" type="number" placeholder="Ex: 6000"
                  class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11" />
                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400 uppercase">
                  MAD
                </div>
              </div>
            </div>

            <div class="flex flex-col gap-1.5 sm:col-span-2">
              <label class="text-xs font-semibold text-slate-600">Date d'embauche</label>
              <input v-model="form.date_embauche" type="date"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm cursor-pointer" />
            </div>

            <div class="flex flex-col gap-3 sm:col-span-2 pt-2">
              <div>
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Rôle & Permissions du Profil <span class="text-rose-500">*</span></label>
                <p class="text-xs text-slate-400 mt-0.5">Définit le niveau de visibilité et les permissions de l'utilisateur sur le dashboard.</p>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div 
                  v-for="role in roles" 
                  :key="role.id"
                  @click="form.role_id = role.id"
                  class="bg-white border rounded-xl p-4 flex items-start gap-3.5 cursor-pointer select-none transition-all duration-200 shadow-sm relative overflow-hidden group"
                  :class="form.role_id === role.id ? 'border-indigo-600 ring-2 ring-indigo-500/10 bg-indigo-50/10' : 'border-slate-200/70 hover:border-slate-300'"
                >
                  <div class="text-xl p-1.5 rounded-lg bg-slate-50 transition-colors" :class="form.role_id === role.id ? 'bg-indigo-50' : ''">
                    {{ role.icon }}
                  </div>
                  <div class="space-y-0.5 pr-6">
                    <h4 class="text-sm font-bold transition-colors" :class="form.role_id === role.id ? 'text-indigo-900' : 'text-slate-800'">
                      {{ role.name }}
                    </h4>
                    <p class="text-xs text-slate-400 font-medium leading-relaxed">
                      {{ role.desc }}
                    </p>
                  </div>
                  <div class="absolute top-4 right-4 w-4 h-4 rounded-full border flex items-center justify-center"
                    :class="form.role_id === role.id ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                    <div class="w-1.5 h-1.5 rounded-full bg-white" v-if="form.role_id === role.id"></div>
                  </div>
                </div>
              </div>
              <p class="text-xs font-medium text-rose-500 mt-0.5" v-if="errors.role_id">⚠️ {{ errors.role_id[0] }}</p>
            </div>
          </div>
        </div>

        <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100/80 space-y-5">
          <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
            <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-bold">04</span>
            <div>
              <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Cartographie des Compétences</h3>
              <p class="text-xs text-slate-400 mt-0.5">Sélectionnez les compétences clés et qualifiez le niveau d'expertise globale.</p>
            </div>
          </div>

          <div v-if="availableSkills.length === 0"
            class="text-sm text-slate-400 py-8 text-center bg-white rounded-xl border border-slate-200/60 shadow-inner">
            🔄 Aucune compétence disponible pour le moment...
          </div>

          <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 pt-2">
            <div v-for="skill in availableSkills" :key="skill.id" 
              class="bg-white border rounded-xl p-3 flex flex-col justify-between gap-3 transition-all duration-200 shadow-sm"
              :class="isSelected(skill.id) ? 'border-indigo-200 bg-indigo-50/20 ring-1 ring-indigo-100' : 'border-slate-200/70 hover:border-slate-300'">
              
              <button type="button" @click="toggleSkill(skill.id)"
                class="flex items-center justify-between text-left w-full group">
                <span class="text-xs font-bold transition-colors" :class="isSelected(skill.id) ? 'text-indigo-900' : 'text-slate-700 group-hover:text-indigo-600'">
                  {{ skill.name }}
                </span>
                <span class="w-4.5 h-4.5 rounded-full border flex items-center justify-center text-[10px] transition-all"
                  :class="isSelected(skill.id) ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-300 group-hover:border-indigo-400 text-transparent'">
                  ✓
                </span>
              </button>

              <div v-if="isSelected(skill.id)" class="relative mt-1 animate-fadeIn">
                <select :value="getLevel(skill.id)" @change="setLevel(skill.id, $event.target.value)"
                  class="w-full text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 outline-none focus:border-indigo-500 bg-white text-slate-700 appearance-none cursor-pointer font-medium shadow-sm">
                  <option value="beginner">🟢 Débutant</option>
                  <option value="intermediate">🟡 Intermédiaire</option>
                  <option value="advanced">🔴 Avancé</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
              </div>
            </div>
          </div>

          <div v-if="selectedSkills.length > 0" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold mt-2">
            <span>✨</span> {{ selectedSkills.length }} compétence(s) affectée(s) avec succès
          </div>
        </div>

        <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100/80 space-y-6">
          <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
            <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-bold">05</span>
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Sécurité du Compte</h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-slate-600">Mot de passe <span class="text-rose-500">*</span></label>
              <input v-model="form.password" type="password" placeholder="••••••••"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm"
                :class="errors.password ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-50' : 'border-slate-200'" />
              <p class="text-xs font-medium text-rose-500 mt-0.5 flex items-center gap-1" v-if="errors.password">
                <span>⚠️</span> {{ errors.password[0] }}
              </p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-slate-600">Confirmer le mot de passe</label>
              <input v-model="form.password_confirmation" type="password" placeholder="••••••••"
                class="w-full px-3.5 py-2 text-sm text-slate-900 bg-white border border-slate-200 rounded-xl outline-none transition duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 h-11 shadow-sm" />
            </div>
          </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
          <button 
            type="button" 
            @click="router.back()" 
            class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-800 active:scale-[0.99] transition duration-150 h-11 flex items-center justify-center"
          >
            Annuler
          </button>

          <button 
            type="submit" 
            :disabled="loading"
            class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 active:scale-[0.99] transition duration-150 disabled:bg-slate-300 disabled:cursor-not-allowed shadow-md shadow-indigo-600/10 h-11 flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span>{{ loading ? 'Enregistrement sécurisé...' : 'Créer le profil de l\'employé' }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(2px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s ease-out forwards;
}
</style>