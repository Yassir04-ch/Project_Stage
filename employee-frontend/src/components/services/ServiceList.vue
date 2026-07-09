<template>
  <div class="col-span-4 space-y-3">
    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-1">{{ services.length }} service(s)</p>

    <LoadingSpinner v-if="loading" />

    <EmptyState v-else-if="services.length === 0" variant="no-services" />

    <ServiceCard
      v-for="s in services"
      :key="s.id"
      :service="s"
      :selected="selectedService?.id === s.id"
      @select="$emit('select', $event)"
      @edit="$emit('edit', $event)"
      @delete="$emit('delete', $event)"
    />
  </div>
</template>

<script setup>
import LoadingSpinner from "./LoadingSpinner.vue";
import EmptyState from "./EmptyState.vue";
import ServiceCard from "./ServiceCard.vue";

defineProps({
  services: {
    type: Array,
    required: true,
  },
  selectedService: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(["select", "edit", "delete"]);
</script>