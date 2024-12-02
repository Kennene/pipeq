<template>
    <div class="flex h-screen">
        <!-- Side Menu -->
        <aside
            class="w-64 bg-gray-800 text-white relative"
            v-if="ticketStore.showSideMenu"
        >
            <div class="p-4">
                <h2 class="text-xl font-semibold">Destinations</h2>
                <ul>
                    <li
                        v-for="destination in ticketStore.destinations"
                        :key="destination.id"
                    >
                        <button
                            @click="selectDestination(destination)"
                            :class="{
                                'font-bold underline':
                                    destination.id ===
                                    ticketStore.selectedDestination?.id,
                            }"
                            class="block w-full text-left px-2 py-1 hover:bg-gray-700"
                        >
                            {{ destination.name }}
                        </button>
                    </li>
                </ul>
            </div>
            <!-- Przycisk ukrywania menu na środku z ikoną strzałki -->
            <button
                @click="toggleSideMenu"
                class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded-l"
            >
                <span>&larr;</span>
            </button>
        </aside>

        <!-- Główna zawartość -->
        <div class="flex-1 flex flex-col relative">
            <!-- Przycisk do pokazania menu bocznego na środku z ikoną strzałki -->
            <button
                v-if="!ticketStore.showSideMenu"
                @click="toggleSideMenu"
                class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded-r"
            >
                <span>&rarr;</span>
            </button>

            <!-- Modal wyboru destynacji -->
            <div
                v-if="ticketStore.showDestinationModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            >
                <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                    <h3 class="text-xl font-semibold mb-4">
                        Wybierz dział pracy
                    </h3>
                    <ul>
                        <li
                            v-for="destination in ticketStore.destinations"
                            :key="destination.id"
                            class="mb-2"
                        >
                            <button
                                @click="
                                    selectDestination(destination);
                                    closeDestinationModal();
                                "
                                class="w-full text-left px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded"
                            >
                                {{ destination.name }}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Sekcja główna Bilety -->
            <div
                class="bg-gray-200 p-4 flex flex-col overflow-y-auto"
                data-section-id="0"
                @dragover.prevent
                @drop="onMainAreaDrop"
            >
                <span class="text-lg font-semibold mb-2">Bilety</span>
                <draggable
                    v-model="ticketStore.mainTickets"
                    itemKey="id"
                    class="flex flex-row flex-wrap gap-2"
                    group="tickets"
                    animation="200"
                    @start="ticketStore.onDragStart"
                    @end="ticketStore.onEnd"
                >
                    <template #item="{ element }">
                        <div
                            class="bg-blue-600 text-white rounded-md p-2 shadow-md cursor-pointer hover:bg-blue-500 transition duration-200"
                            style="width: 150px"
                        >
                            <h5 class="font-bold text-sm mb-1">
                                #{{ element.ticket_nr }}
                            </h5>
                            <p class="text-xs">{{ element.status }}</p>
                            <h6 class="text-xs">{{ element.destination }}</h6>
                        </div>
                    </template>
                </draggable>
            </div>

            <!-- Główna zawartość -->
            <div class="flex flex-1 overflow-hidden">
                <main class="flex-1 bg-white p-6 overflow-auto">
                    <div
                        class="flex flex-wrap gap-6 overflow-x-auto pb-6 h-full"
                    >
                        <!-- Sekcje -->
                        <div
                            v-for="(section, index) in ticketStore.sections"
                            :key="section.id"
                            class="bg-gray-100 p-4 rounded-lg shadow-lg flex-1 flex flex-col h-full"
                            :data-section-id="section.id"
                            @dragover.prevent
                            @drop="() => onSectionDrop(section)"
                            style="min-width: 300px; min-height: 200px"
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
                                class="flex flex-row flex-wrap gap-2 overflow-auto h-full"
                                style="align-content: flex-start"
                            >
                                <template #item="{ element }">
                                    <div
                                        class="bg-blue-600 text-white rounded-md p-2 shadow-md cursor-pointer hover:bg-blue-500 transition duration-200"
                                        style="width: 150px"
                                    >
                                        <h5 class="font-bold text-sm mb-1">
                                            #{{ element.ticket_nr }}
                                        </h5>
                                        <p class="text-xs">
                                            {{ element.status }}
                                        </p>
                                        <h6 class="text-xs">
                                            {{ element.destination }}
                                        </h6>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                    <!-- Delete Confirmation Modal -->
                    <div
                        v-if="ticketStore.showDeleteConfirmation"
                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                    >
                        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                            <h3 class="text-xl font-semibold mb-4">
                                Czy na pewno chcesz usunąć bilet #{{
                                    ticketStore.tempDraggedTicket?.ticket_nr
                                }}?
                            </h3>
                            <div class="flex justify-end space-x-4">
                                <button
                                    @click="ticketStore.cancelDelete"
                                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                                >
                                    Anuluj
                                </button>
                                <button
                                    @click="ticketStore.confirmDelete"
                                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500"
                                >
                                    Usuń
                                </button>
                            </div>
                        </div>
                    </div>
                </main>
            </div>

            <!-- Trash Bin -->
            <footer
                class="bg-red-600 text-white p-4 flex justify-center items-center"
                @dragover.prevent
                @drop.prevent="ticketStore.handleDeleteDrop"
            >
                <div
                    class="bg-red-700 hover:bg-red-800 transition duration-200 p-4 rounded-full cursor-pointer"
                >
                    🗑️ Przeciągnij tutaj, aby usunąć bilet
                </div>
            </footer>
        </div>
    </div>
</template>

<script>
import { onMounted } from "vue";
import { useTicketStore } from "../stores/ticketStore";
import draggable from "vuedraggable";

export default {
    name: "Coordinator",
    components: { draggable },
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
        const ticketStore = useTicketStore();

        // Inicjalizacja store'a z danymi z propsów
        ticketStore.initialize(
            props.translations,
            props.initialTickets,
            props.destinations
        );

        // Inicjalizacja WebSocket po zamontowaniu komponentu
        onMounted(() => {
            ticketStore.initializeWebSocket();
        });

        // Metody do użycia w template
        const toggleSideMenu = () => ticketStore.toggleSideMenu();
        const selectDestination = (destination) =>
            ticketStore.selectDestination(destination);
        const closeDestinationModal = () => ticketStore.closeDestinationModal();
        const onSectionDrop = (section) => ticketStore.onSectionDrop(section);
        const onMainAreaDrop = () => ticketStore.onMainAreaDrop();

        return {
            ticketStore,
            toggleSideMenu,
            selectDestination,
            closeDestinationModal,
            onSectionDrop,
            onMainAreaDrop,
        };
    },
};
</script>
