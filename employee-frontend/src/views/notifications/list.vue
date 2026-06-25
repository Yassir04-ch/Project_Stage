<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { getNotifications, markAsRead, deleteNotification, markAllAsRead } from "@/services/notificationService";
import { useRouter } from "vue-router";

const router = useRouter();

const notifications = ref([]);
const loading = ref(false);
const activeTab = ref("all"); 

// Initialisation clean match dynamic laravel entity structure
const currentUser = ref({
    id: null,
    name: "Utilisateur",
    role: "Employé",
    photo: null
});

const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.is_read).length;
});

const photoUrl = computed(() => {
    if (!currentUser.value?.photo) return null;
    return `http://127.0.0.1:8000/storage/${currentUser.value.photo}`;
});

// Filtrage dyal l-notifications ela hssab l-tab active
const filteredNotifications = computed(() => {
    if (activeTab.value === "unread") {
        return notifications.value.filter(n => !n.is_read);
    }
    return notifications.value;
});

const loadNotifications = async () => {
    loading.value = true;
    try {
        const responseData = await getNotifications();
        
        notifications.value = responseData.notifications || [];
        
        const backendUser = responseData.user;
        if (backendUser) {
            currentUser.value = {
                id: backendUser.id, // ✅ zid id -- khasso bach listener ykhdam
                name: `${backendUser.firstname || ''} ${backendUser.lastname || ''}`.trim() || backendUser.name || "Utilisateur",
                role: backendUser.role?.name || "Employé",
                photo: backendUser.photo || backendUser.avatar || null
            };
        }
    } catch (err) {
        console.error("Erreur chargement notifications:", err);
    } finally {
        loading.value = false;
    }
};

// ✅ Real-time listener via Pusher / Laravel Echo
const listenToNotifications = () => {
    const userId = currentUser.value?.id;

    if (!userId) {
        console.warn('User ID not available, cannot listen to notifications');
        return;
    }

    if (!window.Echo) {
        console.error('window.Echo not found -- check bootstrap.js import in main.js');
        return;
    }

    window.Echo.private(`notifications.${userId}`)
        .listen('.notification.new', (e) => {
            console.log('🔔 NOTIFICATION RECEIVED:', e);

            // ✅ zid notification jdida f top, bla duplication
            const exists = notifications.value.some(n => n.id === e.notification.id);
            if (!exists) {
                notifications.value.unshift(e.notification);
            }
        });

    console.log(`✅ Listening on private channel: notifications.${userId}`);
};

const stopListening = () => {
    const userId = currentUser.value?.id;
    if (userId && window.Echo) {
        window.Echo.leave(`notifications.${userId}`);
    }
};

const handleRead = async (notification) => {
    if (notification.is_read) return;
    try {
        await markAsRead(notification.id);
        notification.is_read = true;
    } catch (err) {
        console.error(err);
    }
};

const handleDelete = async (id) => {
    try {
        await deleteNotification(id);
        notifications.value = notifications.value.filter(n => n.id !== id);
    } catch (err) {
        console.error(err);
    }
};

const handleReadAll = async () => {
    if (unreadCount.value === 0) return;
    try {
        await markAllAsRead();
        notifications.value = notifications.value.map(n => ({
            ...n,
            is_read: true
        }));
    } catch (err) {
        console.error(err);
    }
};

const formatTime = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
};

onMounted(async () => {
    await loadNotifications();
    listenToNotifications(); // ✅ zid had ba3d ma currentUser.id khassha tkun mwjouda
});

onUnmounted(() => {
    stopListening(); // ✅ cleanup mlli component khrj
});
</script>

<template>
<div class="min-h-screen bg-[#f8fafc] font-sans antialiased pb-12">
    
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 shadow-sm">
        <div class="max-w-6xl mx-auto flex items-center justify-between h-16">
            
            <div class="flex items-center gap-2 cursor-pointer" @click="router.push('/dashboard')">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-md shadow-indigo-600/20">
                    ⚡
                </div>
                <span class="text-sm font-bold text-slate-900 tracking-tight hidden sm:block">Dashboard HR</span>
            </div>

            <div class="flex items-center gap-4">
                <button class="relative p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-50 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.03 6.03 0 00-7-5.91V4a2 2 0 11-4 0v1.09A6.03 6.03 0 000 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full"></span>
                </button>

                <div class="h-6 w-[1px] bg-slate-200"></div>

                <div class="flex items-center gap-2.5 cursor-pointer group" @click="router.push('/profile')">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center font-bold text-xs text-indigo-700 overflow-hidden shadow-sm group-hover:border-indigo-400 transition-colors uppercase">
                        <img v-if="photoUrl" :src="photoUrl" alt="Avatar Frame" class="w-full h-full object-cover" />
                        <span v-else>{{ currentUser?.name ? currentUser.name.charAt(0) : 'U' }}</span>
                    </div>
                    <div class="hidden sm:flex flex-col text-left">
                        <span class="text-xs font-bold text-slate-800 tracking-tight group-hover:text-indigo-600 transition-colors">{{ currentUser?.name }}</span>
                        <span class="text-[10px] font-medium text-slate-400">{{ currentUser?.role }}</span>
                    </div>
                </div>
            </div>

        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight sm:text-3xl">Centre d'alertes</h1>
                <p class="text-sm text-slate-500 mt-1">Gérez et suivez l'historique complet de vos notifications.</p>
            </div>
            <button @click="handleReadAll" :disabled="unreadCount === 0" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-all shadow-sm disabled:opacity-40 uppercase tracking-wider">Tout marquer comme lu</button>
        </div>

        <div class="flex border-b border-slate-200 mb-6 gap-6">
            <button @click="activeTab = 'all'" class="pb-3 text-sm font-bold tracking-tight transition-all relative" :class="activeTab === 'all' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'">Toutes les alertes <span class="ml-1.5 px-1.5 py-0.5 text-[10px] bg-slate-100 rounded text-slate-600">{{ notifications.length }}</span><div v-if="activeTab === 'all'" class="absolute bottom-0 left-0 right-0 h-[2px] bg-indigo-600 rounded-full animate-fadeIn"></div></button>
            <button @click="activeTab = 'unread'" class="pb-3 text-sm font-bold tracking-tight transition-all relative flex items-center gap-1.5" :class="activeTab === 'unread' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'">Non lues <span v-if="unreadCount > 0" class="px-1.5 py-0.5 text-[10px] bg-indigo-500 text-white rounded font-bold">{{ unreadCount }}</span><div v-if="activeTab === 'unread'" class="absolute bottom-0 left-0 right-0 h-[2px] bg-indigo-600 rounded-full animate-fadeIn"></div></button>
        </div>

        <div>
            <div v-if="loading" class="bg-white p-12 text-center rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="w-9 h-9 border-3 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
                <p class="mt-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Synchronisation des alertes...</p>
            </div>

            <div v-else-if="filteredNotifications.length === 0" class="bg-white p-16 text-center rounded-2xl border border-slate-200/80 shadow-sm flex flex-col items-center justify-center animate-fadeIn">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Aucune notification trouvée</h3>
            </div>

            <div v-else class="space-y-3">
                <div v-for="n in filteredNotifications" :key="n.id" @click="handleRead(n)" class="group bg-white border rounded-2xl p-4 sm:p-5 flex gap-4 justify-between items-start transition-all duration-200 cursor-pointer shadow-sm relative overflow-hidden animate-fadeIn" :class="!n.is_read ? 'border-indigo-100 bg-gradient-to-r from-indigo-50/10 via-white to-white' : 'border-slate-200/80'">
                    <div v-if="!n.is_read" class="absolute top-0 bottom-0 left-0 w-[4px] bg-indigo-600"></div>
                    
                    <div class="flex gap-4 items-start flex-1 min-w-0">
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl flex-shrink-0" :class="!n.is_read ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-50 text-slate-400'">⚡</div>
                        <div class="space-y-0.5 flex-1 min-w-0">
                            <h3 class="text-sm font-bold tracking-tight text-slate-900 truncate" :class="!n.is_read ? 'text-indigo-950' : 'text-slate-700'">{{ n.title }}</h3>
                            <p class="text-xs leading-relaxed text-slate-500 font-medium">{{ n.message }}</p>
                            <div class="text-[10px] font-bold text-slate-400 pt-1 uppercase">{{ formatTime(n.created_at) }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 ml-2 flex-shrink-0 self-center sm:self-start">
                        <span v-if="!n.is_read" class="hidden sm:inline-block text-[9px] font-bold px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded uppercase tracking-wider">
                            Nouveau
                        </span>

                        <button
                            @click.stop="handleDelete(n.id)"
                            class="w-8 h-8 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center active:scale-95 transition-all opacity-100 sm:opacity-0 group-hover:opacity-100 focus:opacity-100 border border-transparent hover:border-rose-100"
                            title="Supprimer la notification"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>
</template>