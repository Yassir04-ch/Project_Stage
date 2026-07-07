<script setup>
import { ref, computed, onMounted } from "vue";
import api from "@/api/axios";
import { useRouter } from "vue-router";

const router = useRouter();

const services      = ref([]);
const employees     = ref([]);
const loading       = ref(false);
const currentUser   = ref(null);
const selectedService = ref(null);
const showForm      = ref(false);
const formMode      = ref("create");
const formLoading   = ref(false);
const flash         = ref({ visible: false, message: "", type: "success" });

const form = ref({
  name:        "",
  description: "",
  chef_id:     null,
});

const showFlash = (message, type = "success") => {
  flash.value = { visible: true, message, type };
  setTimeout(() => (flash.value.visible = false), 4000);
};

const loadServices = async () => {
  loading.value = true;
  try {
    const res = await api.get("/services", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    services.value = res.data.services || [];
    if (services.value.length && !selectedService.value) {
      selectedService.value = services.value[0];
    }
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const loadEmployees = async () => {
  try {
    const res = await api.get("/emploiyee", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    employees.value = res.data.data || [];
  } catch {}
};

const loadCurrentUser = async () => {
  try {
    const res = await api.get("/users", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    currentUser.value = res.data.admin || null;
  } catch {}
};

const loadServiceDetail = async (service) => {
  try {
    const res = await api.get(`/services/${service.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    selectedService.value = res.data.service;
  } catch {}
};

const submitForm = async () => {
  formLoading.value = true;
  try {
    if (formMode.value === "create") {
      const res = await api.post("/services", form.value, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });
      services.value.unshift(res.data.service);
      selectedService.value = res.data.service;
      showFlash("Service créé avec succès !");
    } else {
      const res = await api.put(`/services/${selectedService.value.id}`, form.value, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });
      const idx = services.value.findIndex(s => s.id === selectedService.value.id);
      if (idx !== -1) services.value[idx] = res.data.service;
      selectedService.value = res.data.service;
      showFlash("Service mis à jour !");
    }
    showForm.value = false;
    resetForm();
  } catch (err) {
    showFlash(err.response?.data?.message || "Une erreur est survenue.", "error");
  } finally {
    formLoading.value = false;
  }
};

const deleteService = async (service) => {
  if (!confirm(`Supprimer le service "${service.name}" ?`)) return;
  try {
    await api.delete(`/services/${service.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
    services.value = services.value.filter(s => s.id !== service.id);
    selectedService.value = services.value[0] || null;
    showFlash("Service supprimé.");
  } catch (err) {
    showFlash("Impossible de supprimer ce service.", "error");
  }
};

const assignEmployee = async (userId) => {
  if (!selectedService.value) return;
  try {
    const res = await api.post(`/services/${selectedService.value.id}/assign`,
      { user_id: userId },
      { headers: { Authorization: `Bearer ${localStorage.getItem("token")}` } }
    );
    selectedService.value = res.data.service;
    showFlash("Employé assigné au service !");
  } catch (err) {
    showFlash(err.response?.data?.message || "Erreur lors de l'assignation.", "error");
  }
};

const removeEmployee = async (userId) => {
  if (!selectedService.value) return;
  try {
    const res = await api.post(`/services/${selectedService.value.id}/remove-employee`,
      { user_id: userId },
      { headers: { Authorization: `Bearer ${localStorage.getItem("token")}` } }
    );
    selectedService.value = res.data.service;
    showFlash("Employé retiré du service.");
  } catch (err) {
    showFlash("Erreur lors du retrait.", "error");
  }
};

const openCreate = () => {
  formMode.value = "create";
  resetForm();
  showForm.value = true;
};

const openEdit = (service) => {
  formMode.value     = "edit";
  form.value.name    = service.name;
  form.value.description = service.description || "";
  form.value.chef_id = service.chef_id || null;
  showForm.value     = true;
};

const resetForm = () => {
  form.value = { name: "", description: "", chef_id: null };
};

const unassignedEmployees = computed(() => {
  if (!selectedService.value) return employees.value;
  const assignedIds = (selectedService.value.employees || []).map(e => e.id);
  return employees.value.filter(e => !assignedIds.includes(e.id));
});

const getPhotoUrl = (photo) => photo ? `http://127.0.0.1:8000/storage/${photo}` : null;

onMounted(async () => {
  await Promise.all([loadServices(), loadEmployees(), loadCurrentUser()]);
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 flex antialiased font-sans selection:bg-indigo-500 selection:text-white">

    <aside class="bg-slate-950 text-slate-200 w-72 min-h-screen p-6 flex flex-col justify-between shadow-2xl shrink-0 border-r border-slate-800/40">
      <div class="flex flex-col">
        <div class="flex items-center gap-3.5 mb-10 pb-4 border-b border-slate-900">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-indigo-500/20">D</div>
          <div>
            <h1 class="text-lg font-black tracking-tight text-white uppercase italic">DATAXPRESS</h1>
            <p class="text-xs text-indigo-400 font-bold tracking-wider uppercase">Workspace Admin</p>
          </div>
        </div>

        <div class="space-y-2">
          <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Workspace</p>
          <nav class="space-y-1.5">
            <button @click="router.push('/dashboard')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100">🏠</span><span>Dashboard</span>
            </button>
            <button @click="router.push('/projects')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100">📁</span><span>Projets</span>
            </button>
            <button @click="router.push('/users')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <span class="text-base opacity-70 group-hover:opacity-100">👥</span><span>Utilisateurs</span>
            </button>
            <button @click="router.push('/absences')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fa-regular fa-calendar-minus text-base w-5"></i><span>Absences</span>
            </button>
            <button @click="router.push('/skills')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-900/60 font-medium transition-all text-sm group text-left">
              <i class="fa-solid fa-brain text-base w-5"></i><span>Compétences</span>
            </button>
            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-600/10 text-sm text-left">
              <i class="fas fa-building text-base w-5"></i><span>Services</span>
            </button>
          </nav>
        </div>
      </div>

      <div class="mt-auto pt-4 border-t border-slate-900">
        <router-link to="/profile" class="bg-slate-900/80 border border-slate-800/40 rounded-xl p-3.5 flex items-center gap-3 hover:bg-slate-900 transition-all group cursor-pointer w-full">
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center font-black text-white text-sm uppercase shrink-0">
            <span v-if="currentUser">{{ currentUser.firstname?.[0] }}{{ currentUser.lastname?.[0] }}</span>
            <span v-else class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
          </div>
          <div class="flex-1 min-w-0">
            <h2 v-if="currentUser" class="text-sm font-bold text-white truncate uppercase group-hover:text-indigo-400 transition-colors">
              {{ currentUser.firstname }} {{ currentUser.lastname }}
            </h2>
            <p v-if="currentUser" class="text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-0.5 truncate">
              {{ currentUser.role?.name ?? 'Admin' }}
            </p>
          </div>
          <span class="text-slate-600 group-hover:text-slate-400 transition-colors text-xs shrink-0">➔</span>
        </router-link>
      </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto max-h-screen">

      <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-3" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="flash.visible" class="fixed top-5 right-5 z-50 flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-semibold min-w-[280px]"
          :class="flash.type === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'">
          <span>{{ flash.type === 'success' ? '✅' : '❌' }}</span>
          <span class="flex-1">{{ flash.message }}</span>
          <button @click="flash.visible = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
      </Transition>

      <div class="bg-white rounded-2xl border border-slate-200/60 px-6 py-5 flex justify-between items-center mb-8 shadow-sm">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Gestion des Services</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">Organisez vos départements et gérez les équipes.</p>
        </div>
        <button @click="openCreate"
          class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider px-4 py-3 rounded-xl shadow-md shadow-indigo-600/15 transition-all flex items-center gap-2 active:scale-95">
          <span>+</span> Nouveau Service
        </button>
      </div>

      <div class="grid grid-cols-12 gap-6">

        <div class="col-span-4 space-y-3">
          <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-1">{{ services.length }} service(s)</p>

          <div v-if="loading" class="flex justify-center py-10">
            <div class="w-7 h-7 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
          </div>

          <div v-else-if="services.length === 0" class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
            <i class="fas fa-building text-2xl text-slate-200 mb-2 block"></i>
            <p class="text-xs text-slate-400 font-medium">Aucun service créé.</p>
          </div>

          <div v-for="s in services" :key="s.id"
            @click="loadServiceDetail(s)"
            class="bg-white rounded-2xl border p-4 cursor-pointer transition-all hover:shadow-md group"
            :class="selectedService?.id === s.id ? 'border-indigo-500 ring-2 ring-indigo-500/10' : 'border-slate-200/80 hover:border-slate-300'">
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                  <i class="fas fa-building text-sm"></i>
                </div>
                <div class="min-w-0">
                  <h3 class="text-sm font-black text-slate-900 truncate uppercase tracking-wide">{{ s.name }}</h3>
                  <p class="text-[10px] text-slate-400 font-medium mt-0.5">
                    {{ s.employees_count ?? s.employees?.length ?? 0 }} membre(s)
                  </p>
                </div>
              </div>
              <div class="flex gap-1.5 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click.stop="openEdit(s)"
                  class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 flex items-center justify-center transition-colors">
                  <i class="fas fa-pen text-[10px]"></i>
                </button>
                <button @click.stop="deleteService(s)"
                  class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-colors">
                  <i class="fas fa-trash text-[10px]"></i>
                </button>
              </div>
            </div>
            <p v-if="s.description" class="text-xs text-slate-400 mt-2 line-clamp-2 leading-relaxed">{{ s.description }}</p>
            <div v-if="s.chef" class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100">
              <i class="fas fa-crown text-amber-500 text-[10px]"></i>
              <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Chef:</span>
              <span class="text-[10px] font-bold text-slate-700">{{ s.chef.firstname }} {{ s.chef.lastname }}</span>
            </div>
          </div>
        </div>

        <div class="col-span-8">
          <div v-if="!selectedService" class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
            <i class="fas fa-building text-4xl text-slate-200 mb-4 block"></i>
            <p class="text-sm font-bold text-slate-400">Sélectionnez un service pour voir les détails.</p>
          </div>

          <div v-else class="space-y-5">

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
              <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                  <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xl font-black shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-building"></i>
                  </div>
                  <div>
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ selectedService.name }}</h2>
                    <p class="text-xs text-slate-400 mt-1">{{ selectedService.description || 'Aucune description.' }}</p>
                  </div>
                </div>
                <div class="flex gap-2">
                  <button @click="openEdit(selectedService)"
                    class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <i class="fas fa-pen text-[10px]"></i> Modifier
                  </button>
                </div>
              </div>

              <div v-if="selectedService.chef" class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-3">
                <i class="fas fa-crown text-amber-500 text-sm"></i>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Chef du service:</span>
                <div class="flex items-center gap-2 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                  <div class="w-6 h-6 rounded-md overflow-hidden bg-indigo-100 shrink-0">
                    <img v-if="selectedService.chef.photo" :src="getPhotoUrl(selectedService.chef.photo)" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center text-indigo-600 text-[10px] font-black uppercase">
                      {{ selectedService.chef.firstname?.[0] }}
                    </div>
                  </div>
                  <span class="text-xs font-bold text-slate-800">{{ selectedService.chef.firstname }} {{ selectedService.chef.lastname }}</span>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
              <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                  <i class="fa-solid fa-users text-indigo-500 text-xs"></i>
                  <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">
                    Membres de l'équipe ({{ selectedService.employees?.length ?? 0 }})
                  </h3>
                </div>

                <div class="relative group/assign">
                  <button class="px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                    <i class="fas fa-plus text-[10px]"></i> Ajouter un membre
                  </button>

                  <div class="absolute right-0 top-10 z-20 w-72 bg-white border border-slate-200 rounded-2xl shadow-xl hidden group-focus-within:block group-hover/assign:block overflow-hidden transition-all">
                    <div class="p-2 max-h-52 overflow-y-auto">
                      <p v-if="unassignedEmployees.length === 0" class="text-xs text-slate-400 text-center py-4 font-medium">Tous les employés sont assignés.</p>
                      <button v-for="emp in unassignedEmployees" :key="emp.id"
                        @click="assignEmployee(emp.id)"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors text-left group/item">
                        <div class="w-8 h-8 rounded-xl overflow-hidden bg-indigo-50 border border-indigo-100 shrink-0">
                          <img v-if="emp.photo" :src="getPhotoUrl(emp.photo)" class="w-full h-full object-cover" />
                          <div v-else class="w-full h-full flex items-center justify-center text-indigo-600 text-[10px] font-black uppercase">
                            {{ emp.firstname?.[0] }}
                          </div>
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-xs font-bold text-slate-800 truncate group-hover/item:text-indigo-600 transition-colors">{{ emp.firstname }} {{ emp.lastname }}</p>
                          <p class="text-[10px] text-slate-400 font-medium truncate">{{ emp.role?.name || 'Collaborateur' }}</p>
                        </div>
                        <span class="text-indigo-500 opacity-0 group-hover/item:opacity-100 transition-opacity text-xs font-bold pr-1">+</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="!selectedService.employees?.length" class="p-14 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-300">
                  <i class="fas fa-users text-xl"></i>
                </div>
                <p class="text-xs text-slate-400 font-semibold">Aucun membre n'est affecté à ce service pour le moment.</p>
              </div>

              <div v-else class="divide-y divide-slate-100">
                <div v-for="emp in selectedService.employees" :key="emp.id"
                  class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/60 transition-colors group">
                  <div class="w-11 h-11 rounded-xl overflow-hidden bg-indigo-50 border border-indigo-100 shrink-0 shadow-sm relative">
                    <img v-if="emp.photo" :src="getPhotoUrl(emp.photo)" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center text-indigo-600 font-black text-sm uppercase bg-gradient-to-br from-indigo-50 to-indigo-100">
                      {{ emp.firstname?.[0] }}{{ emp.lastname?.[0] }}
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate group-hover:text-indigo-600 transition-colors">{{ emp.firstname }} {{ emp.lastname }}</p>
                    <div class="flex items-center gap-2 mt-0.5 text-[11px] font-medium text-slate-400 truncate">
                      <span class="text-indigo-600 font-semibold bg-indigo-50 px-1.5 py-0.5 rounded-md text-[10px]">{{ emp.role?.name || 'Membre' }}</span>
                      <span>·</span>
                      <span>{{ emp.email }}</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-1 group-hover:translate-x-0">
                    <button @click="router.push(`/getUser/${emp.id}`)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-600 text-[11px] font-bold transition-all shadow-sm">
                      Voir Profil
                    </button>
                    <button @click="removeEmployee(emp.id)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-200 text-slate-500 hover:text-rose-600 text-[11px] font-bold transition-all shadow-sm">
                      Retirer
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </main>
  </div>

  <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md p-7 space-y-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-black text-slate-900 uppercase tracking-tight">
            {{ formMode === 'create' ? '+ Nouveau Service' : '✏️ Modifier Service' }}
          </h2>
          <button @click="showForm = false; resetForm()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <div class="space-y-4">
          <div class="space-y-1.5">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Nom du service *</label>
            <input v-model="form.name" type="text" placeholder="ex: Département IT"
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-colors" />
          </div>

          <div class="space-y-1.5">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Description</label>
            <textarea v-model="form.description" rows="3" placeholder="Description du service..."
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-colors resize-none"></textarea>
          </div>

          <div class="space-y-1.5">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Chef du service</label>
            <select v-model="form.chef_id"
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-colors appearance-none cursor-pointer bg-white">
              <option :value="null">Aucun chef assigné</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.firstname }} {{ emp.lastname }} — {{ emp.role?.name }}
              </option>
            </select>
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <button @click="showForm = false; resetForm()" type="button"
            class="flex-1 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition-colors">
            Annuler
          </button>
          <button @click="submitForm" :disabled="formLoading || !form.name"
            class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-md shadow-indigo-600/20 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <div v-if="formLoading" class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
            {{ formLoading ? 'Enregistrement...' : formMode === 'create' ? 'Créer' : 'Mettre à jour' }}
          </button>
        </div>
      </div>
    </div>
  </Transition>

</template>