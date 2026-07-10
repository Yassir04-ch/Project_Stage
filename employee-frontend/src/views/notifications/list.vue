<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { getNotifications, markAsRead, deleteNotification, markAllAsRead } from "@/services/notificationService";
import { useRouter } from "vue-router";

const router = useRouter();

const notifications = ref([]);
const loading = ref(false);
const activeTab = ref("all"); 

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
                id: backendUser.id,
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
    listenToNotifications();
});

onUnmounted(() => {
    stopListening(); 
});
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 <div class="min-h-screen bg-[#f8fafc] font-sans antialiased pb-12">
    
    <nav :class="isDark ? 'bg-slate-950/40 border-slate-900/80 text-slate-200' : 'bg-white/60 border-slate-200 text-slate-800'"
     class="backdrop-blur-xl px-6 py-3 flex justify-between items-center shadow-2xl border-b shrink-0 sticky top-0 z-50 w-full transition-colors duration-300">

         <div class="flex items-center gap-3.5 cursor-pointer" @click="router.push('/')">
             <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-indigo-500/20">
                D
            </div>
            <div>
                <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-black tracking-wider uppercase italic leading-none">DataXpress</h1>
                <p class="text-[10px] text-indigo-500 font-bold tracking-widest uppercase mt-1">Workspace Hub</p>
            </div>
         </div>

         <div class="flex items-center gap-3">
            
             <div :class="isDark ? 'bg-slate-900/40 border-slate-800/60' : 'bg-slate-100 border-slate-200'"
                class="flex items-center gap-1 p-1.5 rounded-xl border">

                 <button @click="router.push('/notifications')"
                        class="px-4 py-2 rounded-lg text-xs font-black bg-indigo-600 text-white shadow-[0_4px_12px_rgba(79,70,229,0.25)] hover:bg-indigo-700 transition-all flex items-center gap-2">
                    <i class="fas fa-bell text-sm opacity-60"></i>
                    <span class="hidden lg:inline">Notifications</span>
                    <span v-if="unreadCount > 0"
                        class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border border-white dark:border-slate-950">
                        {{ unreadCount > 9 ? '9+' : unreadCount }}
                    </span>
                </button>

                 <button @click="router.push('/myabsences')"
                        class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-slate-500 hover:text-slate-900 hover:bg-white/80">
                    <i class="fa-regular fa-calendar-minus text-sm opacity-60"></i>
                    <span class="hidden lg:inline">Mes Absences</span>
                </button>

                 <button @click="router.push('/Myassignement')"
                        class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-slate-500 hover:text-slate-900 hover:bg-white/80">
                    <i class="fas fa-folder text-sm opacity-60"></i>
                    <span class="hidden lg:inline">Assignement</span>
                </button>

                 <button @click="router.push('/allprojects')"
                        class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-slate-500 hover:text-slate-900 hover:bg-white/80">
                    <i class="fas fa-diagram-project text-sm opacity-60"></i>
                    <span class="hidden lg:inline">Projects</span>
                </button>

                 <button v-if="currentUser?.role === 'Administrateur' || user?.role?.name === 'Administrateur'"
                        @click="router.push('/users')"
                        class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-slate-500 hover:text-slate-900 hover:bg-white/80">
                    <i class="fas fa-gauge text-sm opacity-60"></i>
                    <span class="hidden lg:inline">Dashboard</span>
                </button>

                 <button @click="router.push('/emploiyee')"
                        class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-slate-500 hover:text-slate-900 hover:bg-white/80">
                    <i class="fas fa-users text-sm"></i>
                    <span class="hidden lg:inline">Employés</span>
                </button>

                 <button @click="router.push('/profile')"
                        class="px-2 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2.5 group text-left">
                    
                     <div :class="isDark ? 'bg-slate-950 border-slate-800' : 'bg-white border-slate-200'" 
                        class="w-7 h-7 rounded-full border flex items-center justify-center font-black text-[10px] text-indigo-500 overflow-hidden shadow-sm group-hover:border-indigo-500 transition-colors uppercase shrink-0">
                        <img v-if="photoUrl" :src="photoUrl" alt="User Avatar" class="w-full h-full object-cover" />
                        <span v-else>{{ currentUser?.name ? currentUser.name.charAt(0) : 'U' }}</span>
                    </div>

                     <div class="hidden xl:flex flex-col leading-tight pr-1">
                        <span :class="isDark ? 'text-slate-200 group-hover:text-indigo-400' : 'text-slate-800 group-hover:text-indigo-600'" class="text-[11px] font-bold transition-colors">{{ currentUser?.name || 'User' }}</span>
                        <span class="text-[9px] font-medium text-slate-400">{{ currentUser?.role || 'Member' }}</span>
                    </div>
                </button>

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