<template>
    <div class="flex flex-col h-full">
        <!-- 
            Górne menu destynacji
            Dodano gradientowe tło, lepsze zaokrąglenia i subtelny styl na przyciskach.
        -->
        <div
            class="bg-gradient-to-r from-blue-900 to-blue-700 text-white flex justify-center p-4 space-x-4 flex-shrink-0 items-center shadow-md"
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

        <!-- 
            Główna sekcja: brak bocznego menu, tylko main queue i sekcje 
            Tło delikatne i jasne, karty i sekcje z cieniami i zaokrągleniami.
        -->
        <div class="flex-1 overflow-hidden flex flex-col bg-gray-50">
            <!-- Sekcja główna Bilety (Main Queue) -->
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
                            class="bg-white border border-gray-300 rounded-lg p-3 w-36 shadow-sm cursor-pointer hover:shadow-md transition-shadow duration-200"
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
                                    class="bg-white border border-gray-300 rounded-lg p-3 w-36 shadow-sm cursor-pointer hover:shadow-md transition-shadow duration-200"
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
                                </div>
                            </template>
                        </draggable>
                    </div>
                </div>
            </main>

            <!-- Delete Confirmation Modal -->
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
        </div>

        <!-- Trash Bin (Usuwanie biletów) -->
        <footer
            class="bg-red-600 text-white p-4 flex justify-center items-center flex-shrink-0 shadow-inner"
            @dragover.prevent
            @drop.prevent="ticketStore.handleDeleteDrop"
        >
            <div
                class="bg-red-700 hover:bg-red-800 transition duration-200 p-4 rounded-full cursor-pointer shadow-md"
            >
                🗑️ Przeciągnij tutaj, aby usunąć bilet
            </div>
        </footer>
    </div>
</template>

<script>
import { onMounted, computed } from "vue";
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

        // Inicjalizacja WebSocket
        onMounted(() => {
            ticketStore.initializeWebSocket();
        });

        // Liczenie ticketów per destynacja
        const ticketCountByDestination = computed(() => {
            const counts = {};
            for (const t of ticketStore.allTickets) {
                counts[t.destination_id] = (counts[t.destination_id] || 0) + 1;
            }
            return counts;
        });

        const selectDestination = (destination) =>
            ticketStore.selectDestination(destination);
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

        const onTicketDoubleClick = (ticket) => {
            if (ticket.workstation_id) {
                ticketStore.doubleClickToReEnter(ticket);
            }
        };

        const cancelDeleteAction = () => {
            ticketStore.cancelDeleteAndRestore();
        };

        return {
            ticketStore,
            selectDestination,
            onSectionDrop,
            onMainAreaDrop,
            onDestinationDrop,
            onTicketDoubleClick,
            cancelDeleteAction,
            ticketCountByDestination,
        };
    },
};
</script>
