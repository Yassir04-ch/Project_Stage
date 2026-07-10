<script setup>
import { useServices } from "@/composables/useServices";

import Sidebar from "@/components/services/Sidebar.vue";
import Header from "@/components/services/Header.vue";
import FlashMessage from "@/components/services/FlashMessage.vue";
import ServiceList from "@/components/services/ServiceList.vue";
import ServiceDetails from "@/components/services/ServiceDetails.vue";
import ServiceFormModal from "@/components/services/ServiceFormModal.vue";
import AddMemberModal from "@/components/services/AddMemberModal.vue";

const {
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
  loadServiceDetail,
  submitForm,
  deleteService,
  handleAssign,
  removeEmployee,
  openCreate,
  openEdit,
  resetForm,
  filteredUnassignedEmployees,
  getPhotoUrl,
} = useServices();
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <div class="min-h-screen bg-slate-50 flex antialiased font-sans selection:bg-indigo-500 selection:text-white">
    <Sidebar :current-user="currentUser" />

    <main class="flex-1 p-8 overflow-y-auto max-h-screen">
      <FlashMessage :flash="flash" @close="flash.visible = false" />

      <Header @create="openCreate" />

      <div class="grid grid-cols-12 gap-6">
        <ServiceList
          :services="services"
          :selected-service="selectedService"
          :loading="loading"
          @select="loadServiceDetail"
          @edit="openEdit"
          @delete="deleteService"
        />

        <ServiceDetails
          :selected-service="selectedService"
          :get-photo-url="getPhotoUrl"
          @edit="openEdit"
          @add-member="showAddMemberModal = true"
          @remove-employee="removeEmployee"
        />
      </div>
    </main>
  </div>

  <ServiceFormModal
    :show="showForm"
    :form-mode="formMode"
    :form="form"
    :employees="employees"
    :form-loading="formLoading"
    @close="showForm = false; resetForm()"
    @submit="submitForm"
  />

  <AddMemberModal
    :show="showAddMemberModal"
    :search-employee="searchEmployee"
    :filtered-unassigned-employees="filteredUnassignedEmployees"
    :get-photo-url="getPhotoUrl"
    @close="showAddMemberModal = false; searchEmployee = ''"
    @assign="handleAssign"
    @update:search-employee="searchEmployee = $event"
  />
</template>
