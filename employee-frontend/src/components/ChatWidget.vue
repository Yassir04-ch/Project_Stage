<template>
    <div>

        <!-- Floating Button -->

        <button
            @click="toggleChat"
            class="fixed bottom-6 right-6 w-16 h-16 rounded-full bg-blue-600 text-white shadow-xl hover:bg-blue-700 transition z-50">

            💬

        </button>

        <!-- Chat -->

        <div
            v-if="open"
            class="fixed bottom-24 right-6 w-96 h-[600px] bg-white rounded-xl shadow-2xl flex flex-col z-50">

            <!-- Header -->

            <div class="bg-blue-600 text-white p-4 rounded-t-xl flex justify-between">

                <h2 class="font-bold">

                    DataXpress Assistant

                </h2>

                <button @click="toggleChat">

                    ✕

                </button>

            </div>

            <!-- Messages -->

            <div
                ref="messagesContainer"
                class="flex-1 overflow-y-auto p-4 space-y-3">

                <div
                    v-for="(msg,index) in messages"
                    :key="index">

                    <div
                        :class="msg.role==='user'
                        ?'bg-blue-600 text-white ml-auto'
                        :'bg-gray-100 text-gray-800'"
                        class="p-3 rounded-xl max-w-[85%] whitespace-pre-wrap">

                        {{ msg.content }}

                    </div>

                </div>

                <div v-if="loading">

                    <div class="bg-gray-100 p-3 rounded-xl">

                        Typing...

                    </div>

                </div>

            </div>

            <!-- Input -->

            <div class="border-t p-3 flex gap-2">

                <input

                    v-model="input"

                    @keyup.enter="send"

                    placeholder="Votre message..."

                    class="flex-1 border rounded-lg px-3 py-2"/>

                <button

                    @click="send"

                    class="bg-blue-600 text-white px-4 rounded-lg">

                    Envoyer

                </button>

            </div>

        </div>

    </div>
</template>

<script setup>

import { ref,nextTick } from "vue";
import { sendMessage } from "@/services/chatbotService";

const open=ref(false);

const input=ref("");

const loading=ref(false);

const messages=ref([

{
role:"assistant",
content:"Bonjour 👋\nJe suis le chatbot de DataXpress.\nComment puis-je vous aider ?"
}

]);

const messagesContainer=ref(null);

function toggleChat(){

open.value=!open.value;

}

async function send(){

if(!input.value.trim()) return;

messages.value.push({

role:"user",
content:input.value

});

const question=input.value;

input.value="";

loading.value=true;

nextTick(scroll);

try{

const response=await sendMessage(question);

messages.value.push({

role:"assistant",
content:response.reply

});

}catch(e){

messages.value.push({

role:"assistant",
content:"Erreur de connexion."

});

}

loading.value=false;

nextTick(scroll);

}

function scroll(){

if(messagesContainer.value){

messagesContainer.value.scrollTop=messagesContainer.value.scrollHeight;

}

}

</script>