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
                    class="w-32 h-32 flex items-center justify-center rounded-full shadow-lg text-5xl font-bold mb-6"
                    style="background-color: var(--yellow)"
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

                <!--  Zakończ kolejkę (otwiera modal) -->
                <button
                    class="bg-red-600 text-white font-semibold py-2 px-4 rounded-xl shadow-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500 transition transform active:scale-95 text-xl mt-6"
                    @click="showEndByUserModal = true"
                >
                    Zakończ
                </button>
            </div>
        </transition>

        <!-- Strona "In" -->
        <transition name="fade">
            <div
                v-if="currentStatus === 'in'"
                class="fixed inset-0 flex flex-col items-center justify-center bg-blue-900 bg-opacity-95 z-50"
                style="background-color: var(--green)"
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
                        class="w-75 h-75 mb-6 animate-bounce"
                        style="color: var(--white)"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25"
                        />
                    </svg>

                    <!-- Komunikat -->
                    <div
                        class="p-4 rounded-lg shadow-md text-center max-w-lg"
                        style="background-color: var(--primary)"
                    >
                        <p
                            class="text-xl font-bold leading-relaxed"
                            style="color: var(--white)"
                        >
                            {{
                                translations["register.in.message"] ||
                                "Zapraszamy do stanowiska:"
                            }}
                            {{ inWorkstationText }}
                        </p>
                    </div>
                </div>

                <!-- Zakończ kolejkę (otwiera modal) -->
                <button
                    class="bg-red-600 text-white font-semibold py-2 px-4 rounded-xl shadow-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500 transition transform active:scale-95 text-xl mt-6"
                    @click="showEndByUserModal = true"
                >
                    Zakończ
                </button>
            </div>
        </transition>

        <!-- Potwierdzenie zakończenia -->
        <div
            v-if="showEndByUserModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">
                    Czy na pewno chcesz zakończyć oczekiwanie w kolejce?
                </h3>
                <p class="mb-4 text-gray-600">
                    Spowoduje to usunięcie Twojego zgłoszenia z kolejki.
                </p>
                <div class="flex justify-end space-x-4">
                    <button
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors duration-150"
                        @click="hideEndByUserModal"
                    >
                        Anuluj
                    </button>
                    <button
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500 transition-colors duration-150"
                        @click="confirmEndByUser"
                    >
                        Zakończ
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import PipeQ from "../pipeq";

const destinations = ref(window.destinations || []);
const token = ref(window.token || null);
const translations = window.translations || {};

// Dostępne statusy biletów (np. z window.statuses.WAITING itp.)
const currentStatus = ref("initial");
const ticketNr = ref("00");
const inWorkstationText = ref("");
const inAnimationKey = ref(0);

const showEndByUserModal = ref(false);

const pipeq = new PipeQ();

// Obsługa aktualizacji biletu
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

// Zmiana widoku na oczekiwanie
function displayStatusWaiting(ticket) {
    ticketNr.value = ticket.ticket_nr;
    currentStatus.value = "waiting";
}

// Zmiana widoku na "zaproszony do stanowiska"
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

// Zmiana widoku na "koniec" (powrót do initial)
function displayStatusEnd(ticket) {
    currentStatus.value = "initial";
    console.log("Dziękujemy za skorzystanie z naszych usług");
}

function hideEndByUserModal() {
    showEndByUserModal.value = false;
}

function confirmEndByUser() {
    pipeq
        ._endByUser()
        .then(() => {
            console.log("Ticket ended by user.");
            displayStatusEnd({});
        })
        .catch((err) => {
            console.error("Error in endByUser:", err);
        })
        .finally(() => {
            // Niezależnie od wyniku wywołania chowamy modal
            hideEndByUserModal();
        });
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
