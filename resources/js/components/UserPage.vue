<template>
    <div class="min-h-screen flex flex-col bg-gray-50 relative">
        <!-- Sekcja główna z listą destynacji -->
        <div
            v-if="currentStatus === 'initial'"
            class="flex-1 flex flex-col justify-center items-center p-4"
        >
            <div class="w-full max-w-lg mx-auto">
                <div class="flex flex-col space-y-8">
                    <button
                        v-for="destination in destinations"
                        :key="destination.id"
                        class="bg-blue-600 text-white font-semibold py-8 px-6 rounded-xl shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 transition transform active:scale-95 text-3xl"
                        @click="register(destination.id)"
                        :title="destination.description"
                    >
                        {{ destination.name }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Strona oczekiwania (Waiting) -->
        <transition name="fade">
            <div
                v-if="currentStatus === 'waiting'"
                class="fixed inset-0 flex flex-col items-center justify-center bg-white bg-opacity-95 z-50"
            >
                <!-- Numer biletu -->
                <div
                    class="w-32 h-32 flex items-center justify-center bg-pink-300 rounded-full shadow-lg text-5xl font-bold mb-6"
                >
                    {{ ticketNr }}
                </div>

                <!-- Komunikat oczekiwania -->
                <div class="bg-white p-4 rounded-lg shadow-md text-center mb-4">
                    <p class="text-lg font-medium text-gray-700">
                        {{
                            translations["register.waiting.message"] ||
                            "Proszę czekać..."
                        }}
                    </p>
                </div>

                <!-- Animacja czekania (kropki) -->
                <div class="flex space-x-3 mt-4">
                    <div
                        v-for="n in 5"
                        :key="n"
                        class="w-4 h-4 bg-blue-500 rounded-full waiting-dot"
                        :style="{ 'animation-delay': (n - 1) * 0.2 + 's' }"
                    ></div>
                </div>
            </div>
        </transition>

        <!-- Strona "In" -->
        <transition name="fade">
            <div
                v-if="currentStatus === 'in'"
                class="fixed inset-0 flex flex-col items-center justify-center bg-blue-900 bg-opacity-95 z-50"
            >
                <div
                    :key="inAnimationKey"
                    class="flex flex-col items-center relative"
                >
                    <!-- Ikona i animacja -->
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-75 h-75 text-white mb-6 animate-bounce"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25"
                        />
                    </svg>

                    <!-- Komunikat -->
                    <div
                        class="bg-blue-200 p-4 rounded-lg shadow-md text-center max-w-lg"
                    >
                        <p
                            class="text-xl font-medium text-gray-800 leading-relaxed"
                        >
                            {{
                                translations["register.in.message"] ||
                                "Zapraszamy do stanowiska:"
                            }}
                            <span class="font-bold lowercase ml-2">
                                {{ inWorkstationText }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import PipeQ from "../pipeq";

const destinations = ref(window.destinations || []);
const token = ref(window.token || null);
const translations = window.translations || {};

const currentStatus = ref("initial");
const ticketNr = ref("00");
const inWorkstationText = ref("");
const inAnimationKey = ref(0);

const pipeq = new PipeQ();

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
}

function displayStatusIn(ticket) {
    inWorkstationText.value = ticket.workstation.toLowerCase();

    if (currentStatus.value === "in") {
        inAnimationKey.value += 1;
    } else {
        currentStatus.value = "in";
    }

    if (navigator.vibrate) {
        navigator.vibrate([200, 100, 200]);
    }
}

function displayStatusEnd(ticket) {
    currentStatus.value = "initial";
    console.log("Dziękujemy za skorzystanie z naszych usług");
}

onMounted(() => {
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

@keyframes smooth-bounce {
    0%,
    100% {
        transform: translateY(0) scale(0.9);
        opacity: 0.7;
    }
    50% {
        transform: translateY(-10px) scale(1);
        opacity: 1;
    }
}

.waiting-dot {
    animation: smooth-bounce 1.5s infinite ease-in-out;
}
</style>
