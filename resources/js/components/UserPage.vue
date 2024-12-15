<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <!-- Główna sekcja z przyciskami destynacji -->
        <div
            v-if="currentStatus === 'initial'"
            class="flex-1 flex flex-col justify-center items-center p-4"
        >
            <div class="w-full max-w-md mx-auto">
                <div class="flex flex-col space-y-4">
                    <button
                        v-for="destination in destinations"
                        :key="destination.id"
                        class="bg-blue-600 text-white font-semibold py-4 rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition transform active:scale-95"
                        @click="register(destination.id)"
                        :title="destination.description"
                    >
                        {{ destination.name }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Waiting Page -->
        <transition name="fade">
            <div
                v-if="currentStatus === 'waiting'"
                class="fixed inset-0 flex flex-col items-center justify-center bg-white bg-opacity-95 z-50"
            >
                <!-- Ticket number -->
                <div
                    class="w-32 h-32 flex items-center justify-center bg-pink-300 rounded-full shadow-lg text-5xl font-bold mb-6"
                >
                    {{ ticketNr }}
                </div>

                <!-- Waiting message -->
                <div class="bg-white p-4 rounded-lg shadow-md text-center mb-4">
                    <p class="text-lg font-medium text-gray-700">
                        Proszę czekać na swoją kolej...
                    </p>
                </div>

                <!-- Waiting animation -->
                <div class="flex space-x-3">
                    <div
                        v-for="(dot, index) in 3"
                        :key="index"
                        class="w-4 h-4 bg-blue-500 rounded-full animate-bounce"
                    ></div>
                </div>
            </div>
        </transition>

        <!-- In Page -->
        <transition name="fade">
            <div
                v-if="currentStatus === 'in'"
                class="fixed inset-0 flex flex-col items-center justify-center bg-blue-900 bg-opacity-95 z-50"
            >
                <!-- In animation -->
                <div class="text-white text-6xl mb-6 animate-pulse">
                    <i class="bi bi-box-arrow-in-right"></i>
                </div>

                <!-- In message -->
                <div class="bg-blue-200 p-4 rounded-lg shadow-md text-center">
                    <p class="text-lg font-medium text-gray-800">
                        Wywołujemy Cię do stanowiska:
                        <span class="font-bold lowercase">{{
                            inWorkstationText
                        }}</span>
                    </p>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import PipeQ from "../pipeq";

// Inicjalizacja danych z Blade
const destinations = ref(window.destinations || []);
const token = ref(window.token || null);

const currentStatus = ref("initial");
const ticketNr = ref("00");
const inWorkstationText = ref("");
const showWaitingDots = ref(false);

const pipeq = new PipeQ();

// Definiujemy callback, który otrzyma aktualizację biletu
pipeq.onTicketUpdate = (ticket) => {
    switch (ticket.status_id) {
        case parseInt(window.statuses.WAITING):
            displayStatusWaiting(ticket);
            break;
        case parseInt(window.statuses.IN):
            displayStatusIn(ticket);
            break;
        case parseInt(window.statuses.END):
            pipeq._clear();
            displayStatusEnd(ticket);
            break;
        default:
            console.error("Unknown status", ticket.status_id);
            break;
    }
};

function register(destination_id) {
    pipeq._register(destination_id);
}

function displayStatusWaiting(ticket) {
    ticketNr.value = ticket.ticket_nr;
    currentStatus.value = "waiting";
    setTimeout(() => {
        showWaitingDots.value = true;
    }, 2000);
}

function displayStatusIn(ticket) {
    inWorkstationText.value = ticket.workstation.toLowerCase();
    currentStatus.value = "in";
}

function displayStatusEnd(ticket) {
    currentStatus.value = "initial";
    console.log("Dziękujemy za skorzystanie z naszych usług");
}

onMounted(() => {
    // Jeśli mamy już token, to nasłuchuj od razu
    if (token.value) {
        pipeq._listen(token.value);
    }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Animacja dla kropek */
@keyframes bounce {
    0%,
    100% {
        transform: translateY(0);
        opacity: 0.6;
    }
    50% {
        transform: translateY(-10px);
        opacity: 1;
    }
}

.animate-bounce {
    animation: bounce 1s infinite;
}
</style>
