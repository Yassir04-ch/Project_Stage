<template>
  <div>
    <button
      @click="toggleChat"
      class="fixed bottom-6 right-6 w-14 h-14 rounded-full bg-gradient-to-tr from-indigo-600 to-blue-600 text-white shadow-xl hover:shadow-indigo-500/30 hover:scale-110 active:scale-95 transition-all duration-300 z-50 flex items-center justify-center group"
      aria-label="Toggle Chat"
    >
      <!-- Icon dynamic: kaytbeddel 3la 7sab wach chat ma7loul wla msdoud -->
      <svg v-if="!open" class="w-6 h-6 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
      </svg>
      <svg v-else class="w-6 h-6 transition-transform duration-300 rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <!-- Chat Window -->
    <transition name="fade">
      <div
        v-if="open"
        class="fixed bottom-24 right-6 w-[calc(100%-2rem)] max-w-[380px] h-[560px] bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] flex flex-col z-50 border border-slate-100 overflow-hidden"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 text-white p-4 flex items-center justify-between shadow-sm">
          <div class="flex items-center gap-3">
            <!-- Icon/Avatar -->
            <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center font-black text-white text-base border border-white/20">
              D
            </div>
            <div>
              <h2 class="font-bold text-sm tracking-wide">DataXpress Assistant</h2>
              <div class="flex items-center gap-1.5 mt-0.5">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[10px] text-blue-100 font-semibold uppercase tracking-wider">En ligne</span>
              </div>
            </div>
          </div>

          <!-- Close button -->
          <button
            @click="toggleChat"
            class="w-8 h-8 rounded-lg hover:bg-white/10 active:scale-95 flex items-center justify-center transition-all text-white/80 hover:text-white"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Messages Area -->
        <div
          ref="messagesContainer"
          class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 scrollbar-custom"
        >
          <div
            v-for="(msg, index) in messages"
            :key="index"
            class="flex"
            :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
          >
            <!-- Bubble styling -->
            <div
              :class="
                msg.role === 'user'
                  ? 'bg-blue-600 text-white rounded-2xl rounded-tr-none shadow-md shadow-blue-500/10'
                  : 'bg-white text-slate-800 border border-slate-100 rounded-2xl rounded-tl-none shadow-xs'
              "
              class="px-4 py-2.5 max-w-[85%] text-[13px] font-medium leading-relaxed whitespace-pre-wrap"
            >
              {{ msg.content }}
            </div>
          </div>

          <!-- Modern Bouncing Dots Typing Indicator -->
          <div v-if="loading" class="flex justify-start">
            <div class="bg-white border border-slate-100 rounded-2xl rounded-tl-none px-4 py-3 shadow-xs flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: -0.3s"></span>
              <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: -0.15s"></span>
              <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></span>
            </div>
          </div>
        </div>

        <!-- Input Box -->
        <div class="p-3 bg-white border-t border-slate-100 flex items-center gap-2">
          <div class="flex-1 flex items-center border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus-within:bg-white focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all duration-200">
            <input
              v-model="input"
              @keyup.enter="send"
              type="text"
              placeholder="Écrivez votre message..."
              class="flex-1 bg-transparent border-none outline-none text-sm text-slate-800 placeholder-slate-400"
            />
          </div>

          <button
            @click="send"
            :disabled="loading || !input.trim()"
            class="w-10 h-10 rounded-xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-all duration-200 disabled:opacity-40 disabled:hover:bg-blue-600 disabled:scale-100 active:scale-95 shadow-md shadow-blue-500/10"
          >
            <!-- Send Icon (Paper Plane) -->
            <svg class="w-5 h-5 transform rotate-45 -translate-x-0.5 translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
            </svg>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted } from "vue";
import { sendMessage } from "@/services/chatbotService";

const open = ref(false);
const input = ref("");
const loading = ref(false);
const messagesContainer = ref(null);

const messages = ref([]);

onMounted(() => {
  const saved = localStorage.getItem("chat_messages");

  if (saved) {
    messages.value = JSON.parse(saved);
  } else {
    messages.value = [
      {
        role: "assistant",
        content:
          "Bonjour 👋\nJe suis le chatbot officiel de DataXpress.\nComment puis-je vous aider ?",
      },
    ];
  }

  nextTick(scroll);
});

function toggleChat() {
  open.value = !open.value;
}

function saveHistory() {
  localStorage.setItem(
    "chat_messages",
    JSON.stringify(messages.value)
  );
}

async function send() {
  if (!input.value.trim() || loading.value) return;

  const question = input.value;

  messages.value.push({
    role: "user",
    content: question,
  });

  input.value = "";

  saveHistory();

  loading.value = true;

  nextTick(scroll);

  try {
    const response = await sendMessage(question);

    messages.value.push({
      role: "assistant",
      content: response.reply,
    });
  } catch (error) {
    console.error(error);

    messages.value.push({
      role: "assistant",
      content:
        error.response?.data?.error ||
        error.response?.data?.message ||
        "Une erreur est survenue. Veuillez réessayer.",
    });
  }

  loading.value = false;

  saveHistory();

  nextTick(scroll);
}

function scroll() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop =
      messagesContainer.value.scrollHeight;
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(15px) scale(0.95);
}

.scrollbar-custom::-webkit-scrollbar {
  width: 5px;
}
.scrollbar-custom::-webkit-scrollbar-track {
  background: transparent;
}
.scrollbar-custom::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 99px;
}
.scrollbar-custom::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>