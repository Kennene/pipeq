<template>
    <div class="flex flex-col h-full">
        <!-- 
            Górne menu destynacji (draggable destinations)
            Komentarz: Każdy przycisk odpowiada za zmianę destynacji po kliknięciu 
            i zmianę destynacji biletu po przeciągnięciu biletu.
        -->
        <div
            class="bg-gray-900 text-white flex justify-center p-4 space-x-4 flex-shrink-0 items-center"
        >
            <div
                v-for="dest in ticketStore.destinations"
                :key="dest.id"
                class="bg-blue-700 hover:bg-blue-600 transition-colors duration-200 px-6 py-3 rounded-lg cursor-pointer flex items-center justify-center text-lg font-semibold"
                @click="selectDestination(dest)"
                @dragover.prevent
                @drop="onDestinationDrop(dest)"
                :class="{
                    underline: dest.id === ticketStore.selectedDestination?.id,
                }"
            >
                {{ dest.name }} ({{ ticketCountByDestination[dest.id] || 0 }})
            </div>
        </div>

        <!-- 
            Główna sekcja: bez bocznego menu, tylko main queue i sekcje 
        -->
        <div class="flex-1 overflow-hidden flex flex-col">
            <!-- Sekcja główna Bilety (Main Queue) -->
            <div
                class="bg-gray-200 p-4 flex-shrink-0"
                data-section-id="0"
                @dragover.prevent
                @drop="onMainAreaDrop"
            >
                <span class="text-lg font-semibold mb-2 block"
                    >Kolejka główna</span
                >
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
                            @dblclick="onTicketDoubleClick(element)"
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

            <!-- Sekcje (Workstations) 
                 Komentarz: Wprowadzamy poziome przewijanie, aby uniknąć konieczności scrollowania w dół.
            -->
            <main class="flex-1 bg-white p-6 overflow-hidden">
                <div class="flex flex-wrap gap-6 overflow-x-auto h-full pb-6">
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
                                    @dblclick="onTicketDoubleClick(element)"
                                >
                                    <h5 class="font-bold text-sm mb-1">
                                        #{{ element.ticket_nr }}
                                    </h5>
                                    <p class="text-xs">{{ element.status }}</p>
                                    <h6 class="text-xs">
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
                            @click="cancelDeleteAction"
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
        </div>

        <!-- Trash Bin (Usuwanie biletów) -->
        <footer
            class="bg-red-600 text-white p-4 flex justify-center items-center flex-shrink-0"
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
