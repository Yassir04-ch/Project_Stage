<script setup>
import { ref, onMounted } from "vue";
import api from "@/api/axios";

const notifications = ref([]);
const loading = ref(false);

const loadNotifications = async () => {
    loading.value = true;

    try {
        const res = await api.get("/notifications");
        notifications.value = res.data;
    } catch (err) {
        console.log(err);
    } finally {
        loading.value = false;
    }
};

const markAsRead = async (notification) => {
    if (notification.is_read) return;

    try {
        await api.put(`/notifications/${notification.id}/read`);

        notification.is_read = true;
    } catch (err) {
        console.log(err);
    }
};

onMounted(() => {
    loadNotifications();
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 p-8">

        <div class="max-w-5xl mx-auto">

            <div class="mb-8">
                <h1 class="text-3xl font-black text-slate-900">
                    Notifications
                </h1>

                <p class="text-slate-500 mt-1">
                    Historique des alertes et événements.
                </p>
            </div>

            <div
                v-if="loading"
                class="bg-white rounded-2xl p-10 text-center border"
            >
                <div
                    class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto"
                ></div>

                <p class="mt-4 text-slate-500">
                    Chargement...
                </p>
            </div>

            <div
                v-else-if="notifications.length === 0"
                class="bg-white rounded-2xl border p-10 text-center"
            >
                <i
                    class="fas fa-bell-slash text-4xl text-slate-300"
                ></i>

                <h3
                    class="mt-4 text-lg font-semibold text-slate-700"
                >
                    Aucune notification
                </h3>

                <p class="text-slate-400 text-sm mt-1">
                    Vous n'avez reçu aucune notification.
                </p>
            </div>

            <div
                v-else
                class="space-y-4"
            >
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    @click="markAsRead(notification)"
                    class="bg-white rounded-2xl border p-5 cursor-pointer transition hover:shadow-md"
                    :class="!notification.is_read
                        ? 'border-indigo-300 bg-indigo-50'
                        : 'border-slate-200'"
                >

                    <div class="flex justify-between items-start">

                        <div class="flex gap-4">

                            <div
                                class="w-12 h-12 rounded-xl flex items-center justify-center"
                                :class="!notification.is_read
                                    ? 'bg-indigo-100 text-indigo-600'
                                    : 'bg-slate-100 text-slate-500'"
                            >
                                <i class="fas fa-bell"></i>
                            </div>

                            <div>

                                <h3
                                    class="font-bold text-slate-900"
                                >
                                    {{ notification.title }}
                                </h3>

                                <p
                                    class="text-sm text-slate-600 mt-1"
                                >
                                    {{ notification.message }}
                                </p>

                                <div
                                    class="mt-3 text-xs text-slate-400"
                                >
                                    {{ new Date(notification.created_at).toLocaleString('fr-FR') }}
                                </div>

                            </div>

                        </div>

                        <span
                            v-if="!notification.is_read"
                            class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-600 text-white"
                        >
                            Nouveau
                        </span>

                    </div>

                </div>
            </div>

        </div>

    </div>
</template>