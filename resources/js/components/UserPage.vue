<template>
    <div class="min-h-screen flex flex-col bg-gray-50 relative">
        <!-- Ekran początkowy / initial -->
        <div
            v-if="currentStatus === 'initial'"
            class="flex-1 flex flex-col justify-center items-center p-4"
        >
            <div class="w-full max-w-lg mx-auto">
                <div class="flex flex-col space-y-8">
                    <!-- Przycisk każdej destynacji -->
                    <button
                        v-for="destination in destinations"
                        :key="destination.id"
                        class="bg-blue-600 text-white font-semibold py-8 px-6 rounded-xl shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 transition transform active:scale-95 text-3xl"
                        @click="register(destination)"
                        :title="destination.description"
                    >
                        {{ destination.name }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Ekran oczekiwania / waiting -->
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
                <div class="bg-white p-4 rounded-lg shadow-md text-center">
                    <p class="text-lg font-medium text-gray-700">
                        {{
                            translations["register.waiting.message"] ||
                            "Proszę czekać..."
                        }}
                    </p>
                </div>

                <!-- Animacja czekania (kropki) -->
                <div class="flex space-x-3 mt-8">
                    <div
                        v-for="n in 5"
                        :key="n"
                        class="w-4 h-4 bg-blue-500 rounded-full waiting-dot"
                        :style="{ 'animation-delay': (n - 1) * 0.2 + 's' }"
                    ></div>
                </div>
                <!-- Przycisk do wybrania (lub zmiany) powodu wizyty -->
                <div class="mt-8">
                    <button
                        @click="openReasonModal"
                        class="inline-flex items-center space-x-2 px-6 py-3 bg-blue-600 text-white rounded-xl shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transform active:scale-95 transition-all text-lg"
                    >
                        <!-- Ikonka np. "tag" / "edit" / "question-mark" -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 6h.008v.008H6V6Z"
                            />
                        </svg>

                        <span>{{
                            translations["register.reason.choose"] ||
                            "Wybierz powód wizyty"
                        }}</span>
                    </button>
                </div>
                <!--  Zakończ kolejkę (otwiera modal) -->
                <button
                    class="bg-red-600 text-white font-semibold py-2 px-4 rounded-xl shadow-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500 transition transform active:scale-95 text-xl mt-6"
                    @click="showEndByUserModal = true"
                >
                    {{ translations["register.end.button"] || "Zakończ" }}
                </button>
            </div>
        </transition>

        <!-- In -->
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

                <!-- Przycisk otwierający modal -->
                <button
                    class="bg-red-600 text-white font-semibold py-2 px-4 rounded-xl shadow-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500 transition transform active:scale-95 text-xl mt-6"
                    @click="showEndByUserModal = true"
                >
                    {{ translations["register.end.button"] || "Zakończ" }}
                </button>
            </div>
        </transition>
        <!-- Modal / bottom sheet wyboru powodu (reason) -->
        <transition name="sheet">
            <div
                v-if="showReasonModal"
                class="fixed inset-0 flex flex-col justify-end bg-black bg-opacity-50 z-50"
            >
                <div
                    class="relative bg-white w-full rounded-t-2xl p-6 shadow-md"
                >
                    <!-- Pasek do "złapania" (typowe w bottom sheet) -->
                    <div
                        class="w-12 h-1 bg-gray-300 rounded-full mx-auto mb-4"
                    ></div>

                    <!-- Nazwa wybranej destynacji -->
                    <h2
                        class="text-xl font-semibold mb-4 text-gray-800 text-center"
                    >
                        {{ selectedDestination?.name }}
                    </h2>
                    <p class="mb-4 text-gray-600 text-center">
                        {{
                            translations["register.reason.message"] ||
                            "Wybierz powód wizyty:"
                        }}
                    </p>

                    <!-- Lista powodów z danej destynacji -->
                    <div class="flex flex-col space-y-2">
                        <button
                            v-for="reason in activeReasons || []"
                            :key="reason.id"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-md text-left transition"
                            @click="updateReason(reason.id)"
                        >
                            {{
                                translations["reason." + reason.id] ||
                                reason.description
                            }}
                        </button>
                    </div>

                    <!-- "Inny" (zamykamy modal bez wysyłania) -->
                    <div class="mt-6 pt-4 border-t flex justify-center">
                        <button
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-md"
                            @click="closeReasonModal"
                        >
                            {{ translations["reason.other"] || "Inne" }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
        <!-- Modal potwierdzenia -->
        <div
            v-if="showEndByUserModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">
                    {{
                        translations["register.end.modal.title"] ||
                        "Czy na pewno chcesz zakończyć oczekiwanie w kolejce?"
                    }}
                </h3>
                <p class="mb-4 text-gray-600">
                    {{
                        translations["register.end.modal.description"] ||
                        "Spowoduje to usunięcie Twojego biletu z kolejki."
                    }}
                </p>
                <div class="flex justify-end space-x-4">
                    <button
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors duration-150"
                        @click="hideEndByUserModal"
                    >
                        {{
                            translations["register.end.modal.cancel"] ||
                            "Anuluj"
                        }}
                    </button>
                    <button
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500 transition-colors duration-150"
                        @click="confirmEndByUser"
                    >
                        {{
                            translations["register.end.modal.confirm"] ||
                            "Zakończ"
                        }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import PipeQ from "../pipeq";

// Zmienne i referencje
const destinations = ref(window.destinations || []);
const translations = window.translations || {};

// Dostępne statusy biletów (np. z window.statuses.WAITING itp.)
const currentStatus = ref("initial");
const ticketNr = ref("00");
const inWorkstationText = ref("");
const inAnimationKey = ref(0);


// Obsługa modala z powodami
const showReasonModal = ref(false);
const selectedDestination = ref(null);

// Inicjalizacja PipeQ
const pipeq = new PipeQ();

// Pobieranie reasonów z globalnego obiektu i przekształcenie na strukturę { destinationId: [...] }
const reasonsByDestination = ref(
    Array.isArray(window.reasons) ? {} : window.reasons || {}
);

// Aktywne reasons dla wybranej destynacji
const activeReasons = computed(() => {
    if (!selectedDestination.value) return [];

    const destinationId = selectedDestination.value.id;
    const reasons = reasonsByDestination.value[destinationId] || [];

    return reasons.filter((reason) => reason.is_active === 1);
});

console.log(activeReasons);

/**
 * Wywoływane po kliknięciu przycisku z konkretną destynacją.
 * 1. Rejestruje użytkownika w kolejce (po stronie backendu).
 * 2. Oczekujemy na zmianę statusu (ticket.status_id) na WAITING w onTicketUpdate.
 */
async function register(destination) {
    try {
        // Zapamiętujemy wybraną destynację
        selectedDestination.value = destination;

        // Rejestracja w kolejce
        await pipeq._register(destination.id);

        // Dalej nic nie robimy - czekamy, aż serwer ustawi status WAITING,
        // co wywoła displayStatusWaiting(ticket).
    } catch (error) {
        console.error(error);
    }
}

/** Otwiera modal z powodami */
function openReasonModal() {
    // Dodatkowa asekuracja, żeby była wybrana destynacja
    if (!selectedDestination.value) return;
    showReasonModal.value = true;
}

/**
 * Ustawienie (zaktualizowanie) powodu wizyty po stronie backendu.
 * Po kliknięciu w powód w modalu.
 */
function updateReason(reasonId) {
    pipeq
        ._updateReason(reasonId)
        .then(() => {
            closeReasonModal();
        })
        .catch((error) => {
            console.error("Error while updating reason:", error);
        });
}

/** Zamknięcie modala bez wysyłania powodu ("Inne") */
function closeReasonModal() {
    showReasonModal.value = false;
}

// Słuchanie wydarzeń z kanału
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

// Funkcje zmieniające widok w zależności od statusu
function register(destination_id) {
    pipeq._register(destination_id);
}

// Zmiana widoku na oczekiwanie
function displayStatusWaiting(ticket) {
    ticketNr.value = ticket.ticket_nr;

    // Znajdź destynację odpowiadającą ticket.destination_id
    const foundDestination = destinations.value.find(
        (dest) => dest.name === ticket.destination
    );
    if (foundDestination) {
        selectedDestination.value = foundDestination;
    }

    currentStatus.value = "waiting";
}

// Zmiana widoku na "zaproszony do stanowiska"
function displayStatusIn(ticket) {
    inWorkstationText.value = ticket.workstation.toLowerCase();

    // Odświeżamy animację w widgecie
    if (currentStatus.value === "in") {
        inAnimationKey.value += 1;
    } else {
        currentStatus.value = "in";
    }

    // Wibracja (jeśli obsługiwana)
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
    if (window.token) {
        pipeq._listen(window.token);
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

/* Animacja kropek oczekiwania */
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

/* Animacja bottom-sheet */
.sheet-enter-active,
.sheet-leave-active {
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}
.sheet-enter-from,
.sheet-leave-to {
    transform: translateY(100%);
    opacity: 0;
}
</style>
