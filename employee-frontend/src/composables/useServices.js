import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import serviceApi from "@/services/serviceApi";

export function useServices() {
  const router = useRouter();

  const services = ref([]);
  const employees = ref([]);
  const loading = ref(false);
  const currentUser = ref(null);
  const selectedService = ref(null);
  const showForm = ref(false);
  const formMode = ref("create");
  const formLoading = ref(false);
  const flash = ref({ visible: false, message: "", type: "success" });

  const showAddMemberModal = ref(false);
  const searchEmployee = ref("");

  const form = ref({
    name: "",
    description: "",
    chef_id: null,
  });

  const showFlash = (message, type = "success") => {
    flash.value = { visible: true, message, type };
    setTimeout(() => (flash.value.visible = false), 4000);
  };

  const loadServices = async () => {
    loading.value = true;
    try {
      const res = await serviceApi.getServices();
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
      const res = await serviceApi.getEmployees();
      employees.value = res.data.data || [];
    } catch {}
  };

  const loadCurrentUser = async () => {
    try {
      const res = await serviceApi.getCurrentUser();
      currentUser.value = res.data.admin || null;
    } catch {}
  };

  const loadServiceDetail = async (service) => {
    try {
      const res = await serviceApi.getServiceDetail(service.id);
      selectedService.value = res.data.service;
    } catch {}
  };

  const submitForm = async () => {
    formLoading.value = true;
    try {
      if (formMode.value === "create") {
        const res = await serviceApi.createService(form.value);
        services.value.unshift(res.data.service);
        selectedService.value = res.data.service;
        showFlash("Service créé avec succès !");
      } else {
        const res = await serviceApi.updateService(selectedService.value.id, form.value);
        const idx = services.value.findIndex((s) => s.id === selectedService.value.id);
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
      await serviceApi.deleteService(service.id);
      services.value = services.value.filter((s) => s.id !== service.id);
      selectedService.value = services.value[0] || null;
      showFlash("Service supprimé.");
    } catch (err) {
      showFlash("Impossible de supprimer ce service.", "error");
    }
  };

  const assignEmployee = async (userId) => {
    if (!selectedService.value) return;
    try {
      const res = await serviceApi.assignEmployee(selectedService.value.id, userId);
      selectedService.value = res.data.service;
      showFlash("Employé assigné au service !");
    } catch (err) {
      showFlash(err.response?.data?.message || "Erreur lors de l'assignation.", "error");
    }
  };

  const handleAssign = async (userId) => {
    await assignEmployee(userId);
    searchEmployee.value = "";
  };

  const removeEmployee = async (userId) => {
    if (!selectedService.value) return;
    try {
      const res = await serviceApi.removeEmployee(selectedService.value.id, userId);
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
    formMode.value = "edit";
    form.value.name = service.name;
    form.value.description = service.description || "";
    form.value.chef_id = service.chef_id || null;
    showForm.value = true;
  };

  const resetForm = () => {
    form.value = { name: "", description: "", chef_id: null };
  };

  const unassignedEmployees = computed(() => {
    if (!selectedService.value) return employees.value;
    const assignedIds = (selectedService.value.employees || []).map((e) => e.id);
    return employees.value.filter((e) => !assignedIds.includes(e.id));
  });

  const filteredUnassignedEmployees = computed(() => {
    if (!searchEmployee.value) return unassignedEmployees.value;

    const search = searchEmployee.value.toLowerCase();
    return unassignedEmployees.value.filter(
      (emp) =>
        (emp.firstname && emp.firstname.toLowerCase().includes(search)) ||
        (emp.lastname && emp.lastname.toLowerCase().includes(search)) ||
        (emp.email && emp.email.toLowerCase().includes(search))
    );
  });

  const getPhotoUrl = (photo) => (photo ? `http://127.0.0.1:8000/storage/${photo}` : null);

  onMounted(async () => {
    await Promise.all([loadServices(), loadEmployees(), loadCurrentUser()]);
  });

  return {
    router,
    services,
    employees,
    loading,
    currentUser,
    selectedService,
    showForm,
    formMode,
    formLoading,
    flash,
    showAddMemberModal,
    searchEmployee,
    form,
    showFlash,
    loadServices,
    loadEmployees,
    loadCurrentUser,
    loadServiceDetail,
    submitForm,
    deleteService,
    assignEmployee,
    handleAssign,
    removeEmployee,
    openCreate,
    openEdit,
    resetForm,
    unassignedEmployees,
    filteredUnassignedEmployees,
    getPhotoUrl,
  };
}