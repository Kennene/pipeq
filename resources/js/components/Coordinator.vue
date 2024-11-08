<template>
    <div class="h-screen flex flex-col">
        <!-- Lista ticketów pod nagłówkiem -->
        <div
            class="bg-gray-200 p-4 flex items-center space-x-4 overflow-x-auto"
            data-section-id="0"
        >
            <span class="text-lg font-semibold">Tickets</span>
            <draggable
                v-model="tickets"
                itemKey="id"
                class="flex space-x-4"
                group="tickets"
                animation="200"
                @start="onDragStart"
                @end="onEnd"
            >
                <template #item="{ element }">
                    <div
                        class="bg-blue-600 text-white rounded-md px-6 py-3 shadow-lg cursor-pointer hover:bg-blue-500 transition duration-200"
                    >
                        <h5 class="text-center font-bold mb-2">
                            {{ element.user }}
                        </h5>
                        <p class="text-center">{{ element.status }}</p>
                        <h6 class="text-center text-sm">
                            {{ element.destination }}
                        </h6>
                    </div>
                </template>
            </draggable>
        </div>

        <!-- Główna zawartość -->
        <div class="flex flex-1 overflow-hidden">
            <main class="flex-1 bg-white p-6 overflow-auto">
                <div
                    class="flex flex-1 space-x-6 overflow-x-auto pb-6 h-full relative"
                >
                    <!-- Iteracja po sekcjach -->
                    <div
                        v-for="(section, index) in sections"
                        :key="section.id"
                        class="bg-gray-100 p-6 rounded-lg shadow-lg min-w-[300px] flex-1 flex flex-col space-y-4 h-full"
                        :data-section-id="section.id"
                    >
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ section.name }}
                            </h3>
                        </div>
                        <div class="flex-1 overflow-auto">
                            <draggable
                                v-model="section.tickets"
                                itemKey="id"
                                group="tickets"
                                animation="200"
                                @start="onDragStart"
                                @end="onEnd"
                                :section="section"
                                class="flex space-x-4 overflow-x-auto"
                            >
                                <template #item="{ element }">
                                    <div
                                        class="bg-blue-600 text-white rounded-md px-4 py-2 shadow-md cursor-pointer hover:bg-blue-500 transition duration-200"
                                    >
                                        <h5 class="text-center font-bold mb-2">
                                            {{ element.user }}
                                        </h5>
                                        <p class="text-center">
                                            {{ element.status }}
                                        </p>
                                        <h6 class="text-center text-sm">
                                            {{ element.destination }}
                                        </h6>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                </div>

                <!-- Modal potwierdzenia usunięcia -->
                <div
                    v-if="showDeleteConfirmation"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                        <h3 class="text-xl font-semibold mb-4">
                            Czy na pewno chcesz usunąć Ticket #{{
                                tempDraggedTicket?.id
                            }}?
                        </h3>
                        <div class="flex justify-end space-x-4">
                            <button
                                @click="cancelDelete"
                                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                            >
                                Anuluj
                            </button>
                            <button
                                @click="confirmDelete"
                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500"
                            >
                                Usuń
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Kosz -->
        <footer
            class="bg-red-600 text-white p-4 flex justify-center items-center"
        >
            <div
                class="bg-red-700 hover:bg-red-800 transition duration-200 p-4 rounded-full cursor-pointer"
                @dragover.prevent
                @drop.prevent="handleDeleteDrop"
            >
                🗑️ Przeciągnij tutaj, aby usunąć ticket
            </div>
        </footer>
    </div>
</template>

<script>
import PipeQ from "../pipeq";
import draggable from "vuedraggable";

export default {
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
    },
    data() {
        return {
            tickets: this.initialTickets
                .filter((ticket) => ![1, 2].includes(ticket.workstation_id))
                .map((ticket, index) => ({
                    ...ticket,
                    id: ticket.id || `temp-id-${index}-${Date.now()}`,
                    workstation_id: null, // Wszystkie tickety na głównej liście mają null
                    status_id: 1, // Ustawienie domyślnego statusu na "Waiting"
                    status: this.translations.statuses[1] || "Waiting",
                })),
            sections: [
                {
                    id: 1,
                    name: "Registrar's Office",
                    tickets: this.initialTickets
                        .filter((ticket) => ticket.workstation_id === 1)
                        .map((ticket) => ({
                            ...ticket,
                            status_id: 3, // "Being Served"
                            status:
                                this.translations.statuses[3] || "Being Served",
                        })),
                    workstationId: 1,
                },
                {
                    id: 2,
                    name: "Payments",
                    tickets: this.initialTickets
                        .filter((ticket) => ticket.workstation_id === 2)
                        .map((ticket) => ({
                            ...ticket,
                            status_id: 3, // "Being Served"
                            status:
                                this.translations.statuses[3] || "Being Served",
                        })),
                    workstationId: 2,
                },
            ],
            draggedTicket: null,
            tempDraggedTicket: null,
            draggedFromSection: null,
            showDeleteConfirmation: false,
            isDeleteDrop: false, // Nowa flaga
            pipeQ: new PipeQ(),
            statusMap: this.translations.statuses,
        };
    },
    mounted() {
        window.addEventListener("ticket-new", this.handleNewTicket);
    },
    beforeUnmount() {
        window.removeEventListener("ticket-new", this.handleNewTicket);
    },
    methods: {
        onDragStart(evt) {
            const fromComponent = evt.from.__draggable_component__;
            const fromList = fromComponent.realList;
            const index = evt.oldIndex;
            const item = fromList[index];

            if (item && item.id) {
                this.draggedTicket = item;
                this.tempDraggedTicket = { ...item }; // Kopia dla modal

                this.draggedFromSection = fromComponent.section || null;

                console.log(
                    "Ustawiono `draggedTicket` na:",
                    this.draggedTicket,
                    "z sekcji:",
                    this.draggedFromSection
                        ? this.draggedFromSection.name
                        : "Tickets"
                );
            } else {
                console.error("Błąd: `draggedTicket` nie posiada `id`");
                this.draggedTicket = null;
                this.tempDraggedTicket = null;
                this.draggedFromSection = null;
            }
        },
        async onEnd(event) {
            if (this.isDeleteDrop) {
                this.isDeleteDrop = false;
                // Modal już jest wyświetlany przez handleDeleteDrop
                return;
            }

            if (!this.draggedTicket || !this.draggedTicket.id) {
                console.error(
                    "Brak `ticketId` podczas zakończenia przeciągania.",
                    this.draggedTicket
                );
                return;
            }

            // Odczytanie sectionId z atrybutu data-section-id
            const sectionElement = event.to.closest("[data-section-id]");
            const sectionId = sectionElement
                ? Number(sectionElement.getAttribute("data-section-id"))
                : null;

            console.log("sectionId (from data attribute):", sectionId);

            let workstationId;
            let newStatusId;
            let newStatus;

            if (sectionId === 0) {
                // Przypisanie workstationId=null dla głównej listy "Tickets"
                workstationId = null;
                newStatusId = 1; // ID dla "Waiting"
                newStatus = this.statusMap[1] || "Waiting";
            } else {
                const toSection = this.sections.find(
                    (section) => section.id === sectionId
                );

                if (!toSection) {
                    console.error("Nie znaleziono sekcji o id:", sectionId);
                    return;
                }

                workstationId = toSection.workstationId;

                // Ustalanie statusu na podstawie workstationId
                newStatusId = 3; // "Being Served"
                newStatus = this.statusMap[3] || "Being Served";
            }

            // Aktualizacja ticketu
            this.draggedTicket.workstation_id = workstationId;
            this.draggedTicket.status_id = newStatusId;
            this.draggedTicket.status = newStatus;

            try {
                const response = await this.pipeQ._move(
                    this.draggedTicket.id,
                    workstationId
                );
                // Aktualizacja lokalnego stanu na podstawie odpowiedzi z backendu
                const updatedTicket = response.data.ticket;
                if (updatedTicket) {
                    // Znajdź i zaktualizuj ticket w głównej liście
                    const mainIndex = this.tickets.findIndex(
                        (t) => t.id === updatedTicket.id
                    );
                    if (mainIndex !== -1) {
                        this.$set(this.tickets, mainIndex, updatedTicket);
                    }

                    // Usuń ticket z poprzedniej sekcji i dodaj do nowej
                    if (this.draggedFromSection) {
                        const oldSection = this.sections.find(
                            (s) => s.id === this.draggedFromSection.id
                        );
                        if (oldSection) {
                            oldSection.tickets = oldSection.tickets.filter(
                                (t) => t.id !== updatedTicket.id
                            );
                        }
                    } else {
                        // Jeśli przeciągany z listy głównej
                        this.tickets = this.tickets.filter(
                            (t) => t.id !== updatedTicket.id
                        );
                    }

                    // Dodanie do nowej sekcji lub listy głównej
                    if (workstationId === null) {
                        this.tickets.push(updatedTicket);
                    } else {
                        const newSection = this.sections.find(
                            (s) => s.id === sectionId
                        );
                        if (newSection) {
                            this.$set(
                                newSection.tickets,
                                newSection.tickets.length,
                                updatedTicket
                            );
                        }
                    }
                }
            } catch (error) {
                console.error("Błąd w _move:", error);
            }

            // Resetowanie zmiennych związanych z przeciąganiem
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },
        handleDeleteDrop(event) {
            this.isDeleteDrop = true;
            this.showDeleteModal();
        },
        showDeleteModal() {
            if (this.tempDraggedTicket) {
                this.showDeleteConfirmation = true;
                console.log(
                    "Wyświetlanie modala usuwania dla `tempDraggedTicket`:",
                    this.tempDraggedTicket
                );
            } else {
                console.error("Brak `draggedTicket` podczas próby usunięcia.");
            }
        },
        cancelDelete() {
            this.showDeleteConfirmation = false;
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },
        async confirmDelete() {
            console.log("this.pipeQ:", this.pipeQ);
            console.log("Próba usunięcia ticketu:", this.tempDraggedTicket);

            if (this.tempDraggedTicket && this.tempDraggedTicket.id) {
                const ticketId = this.tempDraggedTicket.id;
                console.log(`Usuwanie biletu ${ticketId}.`);

                try {
                    const response = await this.pipeQ._end(ticketId);
                    console.log("Response from _end:", response);
                    console.log("Ticket usunięty pomyślnie.");

                    // Ustawienie status_id na 4 (Released)
                    this.tempDraggedTicket.status_id = 4;
                    this.tempDraggedTicket.status =
                        this.statusMap[4] || "Released";

                    // Usunięcie ticketu z listy głównej
                    this.tickets = this.tickets.filter(
                        (ticket) => ticket.id !== ticketId
                    );

                    // Usunięcie ticketu z wszystkich sekcji
                    this.sections.forEach((section) => {
                        section.tickets = section.tickets.filter(
                            (ticket) => ticket.id !== ticketId
                        );
                    });

                    this.draggedTicket = null;
                    this.tempDraggedTicket = null;
                    this.showDeleteConfirmation = false;
                } catch (error) {
                    console.error(
                        "Błąd podczas usuwania biletu:",
                        error.response?.data || error.message
                    );
                }
            } else {
                console.error(
                    "Brak `ticketId` w confirmDelete lub brak obiektu `tempDraggedTicket` podczas próby usunięcia:",
                    this.tempDraggedTicket
                );
                this.showDeleteConfirmation = false;
            }
        },
        handleNewTicket(event) {
            const ticket = event.detail;
            if (!this.tickets.find((t) => t.id === ticket.id)) {
                // Ustawienie workstation_id na null dla nowych ticketów
                ticket.workstation_id = null;
                ticket.status_id = 1; // ID dla "Waiting"
                ticket.status = this.statusMap[1] || "Waiting";

                this.tickets.push(ticket);
            }
        },
    },
};
</script>
