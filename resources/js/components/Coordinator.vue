<template>
    <div class="flex flex-col h-full">
        <!-- Górne menu destynacji -->
        <div
            class="text-white flex justify-center p-4 space-x-4 flex-shrink-0 items-center shadow-md"
            style="
                background: linear-gradient(
                    to right,
                    var(--darkblue),
                    var(--primary)
                );
            "
        >
            <div
                v-for="dest in ticketStore.destinations"
                :key="dest.id"
                class="cursor-pointer flex items-center justify-center text-sm font-semibold px-4 py-2 rounded-full transition-all duration-150 ease-in-out hover:bg-blue-600 focus:outline-none"
                :class="{
                    'bg-blue-800 ring-2 ring-white':
                        dest.id === ticketStore.selectedDestination?.id,
                    'bg-blue-700':
                        dest.id !== ticketStore.selectedDestination?.id,
                }"
                @click="selectDestination(dest)"
                @dragover.prevent
                @drop="onDestinationDrop(dest)"
            >
                {{ dest.name }}
                <span
                    class="ml-2 bg-blue-600 text-white text-xs rounded-full px-2 py-1"
                    v-if="ticketCountByDestination[dest.id]"
                >
                    {{ ticketCountByDestination[dest.id] }}
                </span>
            </div>
        </div>

        <!-- Główna sekcja: kolejka główna + sekcje -->
        <div class="flex-1 overflow-hidden flex flex-col bg-gray-50">
            <!-- Kolejka główna (Main Queue) -->
            <div
                class="p-4 flex-shrink-0"
                data-section-id="0"
                @dragover.prevent
                @drop="onMainAreaDrop"
            >
                <h2 class="text-lg font-semibold mb-2 text-gray-700">
                    Kolejka główna
                </h2>
                <draggable
                    v-model="ticketStore.mainTickets"
                    itemKey="id"
                    class="flex flex-row flex-wrap gap-4"
                    group="tickets"
                    animation="200"
                    @start="ticketStore.onDragStart"
                    @end="ticketStore.onEnd"
                >
                    <template #item="{ element }">
                        <div
                            class="rounded-lg p-3 w-36 shadow-sm cursor-pointer hover:shadow-md transition-shadow duration-200"
                            :class="getTicketTimeClass(element)"
                            @dblclick="onTicketDoubleClick(element)"
                        >
                            <h5
                                class="font-semibold text-sm text-gray-800 mb-1 truncate"
                            >
                                #{{ element.ticket_nr }}
                            </h5>
                            <p class="text-xs text-gray-600 mb-1">
                                {{ element.status }}
                            </p>
                            <h6 class="text-xs text-gray-500 truncate">
                                {{ element.destination }}
                            </h6>
                            <div class="text-xs text-gray-700 mt-1">
                                Czas: {{ getTicketTime(element) }}
                            </div>
                        </div>
                    </template>
                </draggable>
            </div>

            <!-- Sekcje (Workstations) -->
            <main class="flex-1 p-6 overflow-hidden">
                <div class="flex flex-wrap gap-6 overflow-x-auto h-full pb-6">
                    <div
                        v-for="(section, index) in ticketStore.sections"
                        :key="section.id"
                        class="bg-white border border-gray-300 p-4 rounded-lg shadow-sm flex-1 flex flex-col h-full min-w-[300px]"
                        :data-section-id="section.id"
                        @dragover.prevent
                        @drop="() => onSectionDrop(section)"
                    >
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ section.name }}
                            </h3>
                        </div>
                        <draggable
                            v-model="section.tickets"
                            itemKey="id"
                            group="tickets"
                            animation="200"
                            @start="ticketStore.onDragStart"
                            @end="ticketStore.onEnd"
                            :section="section"
                            class="flex flex-row flex-wrap gap-4 overflow-auto h-full"
                            style="align-content: flex-start"
                        >
                            <template #item="{ element }">
                                <div
                                    class="rounded-lg p-3 w-36 shadow-sm cursor-pointer hover:shadow-md transition-shadow duration-200"
                                    :class="getTicketTimeClass(element)"
                                    @dblclick="onTicketDoubleClick(element)"
                                >
                                    <h5
                                        class="font-semibold text-sm text-gray-800 mb-1 truncate"
                                    >
                                        #{{ element.ticket_nr }}
                                    </h5>
                                    <p class="text-xs text-gray-600 mb-1">
                                        {{ element.status }}
                                    </p>
                                    <h6 class="text-xs text-gray-500 truncate">
                                        {{ element.destination }}
                                    </h6>
                                    <div class="text-xs text-gray-700 mt-1">
                                        Czas: {{ getTicketTime(element) }}
                                    </div>
                                </div>
                            </template>
                        </draggable>
                    </div>
                </div>
            </main>

            <!-- Delete Confirmation Modal (pojedyncze bilety) -->
            <div
                v-if="ticketStore.showDeleteConfirmation"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            >
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">
                        Czy na pewno chcesz usunąć bilet #{{
                            ticketStore.tempDraggedTicket?.ticket_nr
                        }}?
                    </h3>
                    <div class="flex justify-end space-x-4">
                        <button
                            @click="cancelDeleteAction"
                            class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors duration-150"
                        >
                            Anuluj
                        </button>
                        <button
                            @click="ticketStore.confirmDelete"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500 transition-colors duration-150"
                        >
                            Usuń
                        </button>
                    </div>
                </div>
            </div>

            <!-- Clear ALL Confirmation Modal (z SELECTEM) -->
            <div
                v-if="ticketStore.showClearAllConfirmation"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            >
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">
                        Wybierz, co chcesz usunąć:
                    </h3>

                    <!-- SELECT do wyboru destynacji lub "Wszystkie" -->
                    <div class="mb-4">
                        <label
                            for="destinationSelect"
                            class="block text-gray-700 font-medium mb-1"
                        >
                            Miejsce docelowe:
                        </label>
                        <select
                            id="destinationSelect"
                            v-model="selectedClearAllDestinationId"
                            class="form-select mt-1 block w-full border-gray-300 rounded-md"
                        >
                            <!-- Opcja na usunięcie wszystkich (null) -->
                            <option :value="null">Wszystkie</option>

                            <!-- Iteracja po available destinations -->
                            <option
                                v-for="dest in ticketStore.destinations"
                                :key="dest.id"
                                :value="dest.id"
                            >
                                {{ dest.name }}
                            </option>
                        </select>
                    </div>

                    <div class="text-gray-600 mb-6">
                        <span v-if="selectedClearAllDestinationId">
                            Czy na pewno chcesz usunąć wszystkie bilety z
                            <strong>{{
                                getDestinationName(
                                    selectedClearAllDestinationId
                                )
                            }}</strong>
                            ?
                        </span>
                        <span v-else>
                            Czy na pewno chcesz usunąć
                            <strong>wszystkie</strong> bilety?
                        </span>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button
                            @click="ticketStore.hideClearAllModal"
                            class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors duration-150"
                        >
                            Anuluj
                        </button>
                        <button
                            @click="confirmClearAll"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500 transition-colors duration-150"
                        >
                            Usuń
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trash Bin (Usuwanie biletów) + kliknięcie DWUKROTNE usuwa wszystkie -->
        <footer
            class="bg-red-600 text-white p-4 flex justify-center items-center flex-shrink-0 shadow-inner"
            @dragover.prevent
            @drop.prevent="ticketStore.handleDeleteDrop"
        >
            <div
                class="bg-red-700 hover:bg-red-800 transition duration-200 p-4 rounded-full cursor-pointer shadow-md text-center"
                @dblclick="openClearAllModal"
            >
                🗑️ Przeciągnij tutaj, aby usunąć bilet
                <br />
                <span class="text-sm">
                    (Kliknij dwa razy, aby usunąć wszystkie)
                </span>
            </div>
        </footer>
    </div>
</template>

<script>
import { onMounted, computed, ref, onBeforeUnmount } from "vue";
import { useTicketStore } from "../stores/ticketStore";
import draggable from "vuedraggable";

export default {
    name: "Coordinator",
    components: { draggable },

    // Przyjmujemy 3 propsy z Blade
    props: {
        initialTickets: {
            type: Array,
            default: () => [],
        },
        translations: {
            type: Object,
            default: () => ({ statuses: {} }),
        },
        destinations: {
            type: Array,
            required: true,
        },
    },

    setup(props) {
        // 1. Pobieramy store
        const ticketStore = useTicketStore();

        // 2. Inicjalizujemy store danymi z props
        ticketStore.initialize(
            props.translations,
            props.initialTickets,
            props.destinations
        );

        // 3. WebSocket (Echo) itp.
        onMounted(() => {
            ticketStore.initializeWebSocket();
        });

        // Obliczamy liczbę biletów w poszczególnych destynacjach
        const ticketCountByDestination = computed(() => {
            const counts = {};
            for (const t of ticketStore.allTickets) {
                counts[t.destination_id] = (counts[t.destination_id] || 0) + 1;
            }
            return counts;
        });

        // REF do przechowywania wybranego ID destynacji w modalu 'usuń wszystkie' (null = wszystkie)
        const selectedClearAllDestinationId = ref(null);

        // Pomocnicza metoda do pobrania nazwy destynacji
        const getDestinationName = (id) => {
            const d = ticketStore.destinations.find((dest) => dest.id === id);
            return d ? d.name : "";
        };

        // Otwarcie modala do usunięcia wszystkich bądź wybranej destynacji
        const openClearAllModal = () => {
            // Domyślnie ustawiamy na null (czyli "Wszystkie")
            selectedClearAllDestinationId.value = null;
            ticketStore.showClearAllConfirmation = true;
        };

        // Potwierdzenie usunięcia
        const confirmClearAll = async () => {
            try {
                // Wywołanie w store, który wywołuje pipeQ._endAll(...)
                await ticketStore._endAll(selectedClearAllDestinationId.value);
            } catch (err) {
                console.error("Błąd przy czyszczeniu biletów:", err);
            } finally {
                ticketStore.hideClearAllModal();
            }
        };

        // Kliknięcie w destynację u góry
        const selectDestination = (destination) => {
            ticketStore.selectDestination(destination);
        };

        // Metody drag & drop
        const onSectionDrop = (section) => ticketStore.onSectionDrop(section);
        const onMainAreaDrop = () => ticketStore.onMainAreaDrop();
        const onDestinationDrop = (dest) => {
            if (!ticketStore.draggedTicket || !ticketStore.draggedTicket.id)
                return;
            ticketStore.changeTicketDestination(
                ticketStore.draggedTicket.id,
                dest.id
            );
        };

        // Dwuklik na bilet -> re-enter do sekcji
        const onTicketDoubleClick = (ticket) => {
            if (ticket.workstation_id) {
                ticketStore.doubleClickToReEnter(ticket);
            }
        };

        // Anulowanie kasowania pojedynczego biletu
        const cancelDeleteAction = () => {
            ticketStore.cancelDeleteAndRestore();
        };

        // Upływ czasu (tykający co 1 sekundę)
        const now = ref(Date.now());
        let interval = null;

        onMounted(() => {
            interval = setInterval(() => {
                now.value = Date.now();
            }, 1000);
        });

        onBeforeUnmount(() => {
            clearInterval(interval);
        });

        // Wyświetlanie czasu od utworzenia biletu
        const getTicketTime = (ticket) => {
            if (!ticket.created_at) return "";
            const createdAt = new Date(ticket.created_at).getTime();
            const diffMs = now.value - createdAt;
            const diffMins = Math.floor(diffMs / 60000);
            const diffSecs = Math.floor((diffMs % 60000) / 1000);
            return `${diffMins}min ${diffSecs}s`;
        };

        // Kolorystyka zależna od "wieku" biletu
        const getTicketTimeClass = (ticket) => {
            if (!ticket.created_at) {
                return "bg-white border border-gray-300";
            }
            const createdAt = new Date(ticket.created_at).getTime();
            const diffMs = now.value - createdAt;
            const diffMins = Math.floor(diffMs / 60000);

            if (diffMins >= 10) {
                return "bg-red-200 border-red-500 animate-pulse";
            } else if (diffMins >= 5) {
                return "bg-yellow-200 border-yellow-400";
            } else {
                return "bg-white border border-gray-300";
            }
        };

        // Zwracamy wszystko, co używamy w szablonie
        return {
            ticketStore,
            ticketCountByDestination,
            selectedClearAllDestinationId,
            getDestinationName,
            openClearAllModal,
            confirmClearAll,
            selectDestination,
            onSectionDrop,
            onMainAreaDrop,
            onDestinationDrop,
            onTicketDoubleClick,
            cancelDeleteAction,
            getTicketTime,
            getTicketTimeClass,
        };
    },
};
</script>
