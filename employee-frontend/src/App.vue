<template>
  <RouterView />

  <ChatWidget v-if="showChatbot" />
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import ChatWidget from "@/components/ChatWidget.vue";

const route = useRoute();

const publicPages = [
  "home",
  "contact",
  "careers",
];

const showChatbot = computed(() => {
  return publicPages.includes(route.name);
});
</script>