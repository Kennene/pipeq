<template>
    <div class="h-screen flex flex-col">
        <!-- Lista biletów pod nagłówkiem -->
        <div
            class="bg-gray-200 p-4 flex items-center space-x-4 overflow-x-auto"
            data-section-id="0"
        >
            <span class="text-lg font-semibold">Bilety</span>
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
                    <!-- Iteracja przez sekcje -->
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

                <!-- Modal potwierdzający usunięcie -->
                <div
                    v-if="showDeleteConfirmation"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                        <h3 class="text-xl font-semibold mb-4">
                            Czy na pewno chcesz usunąć bilet #{{
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
                🗑️ Przeciągnij tutaj, aby usunąć bilet
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
            // Inicjalizacja biletów
            tickets: this.initialTickets
                .filter((ticket) => !ticket.workstation_id)
                .map((ticket) => ({
                    ...ticket,
                    status:
                        this.translations.statuses[ticket.status_id] ||
                        "Oczekiwanie",
                })),
            sections: [
                {
                    id: 1,
                    name: "Biuro Rejestracji",
                    tickets: this.initialTickets
                        .filter((ticket) => ticket.workstation_id === 1)
                        .map((ticket) => ({
                            ...ticket,
                            status:
                                this.translations.statuses[ticket.status_id] ||
                                "Obsługiwany",
                        })),
                    workstationId: 1,
                },
                {
                    id: 2,
                    name: "Płatności",
                    tickets: this.initialTickets
                        .filter((ticket) => ticket.workstation_id === 2)
                        .map((ticket) => ({
                            ...ticket,
                            status:
                                this.translations.statuses[ticket.status_id] ||
                                "Obsługiwany",
                        })),
                    workstationId: 2,
                },
            ],
            draggedTicket: null,
            tempDraggedTicket: null,
            draggedFromSection: null,
            showDeleteConfirmation: false,
            isDeleteDrop: false,
            pipeQ: new PipeQ(),
            statusMap: this.translations.statuses,
        };
    },
    mounted() {
        console.log("Komponent Koordynatora zamontowany");

        window.Echo.private("display")
            .listen(".UpdateDisplayAboutTicket", (e) => {
                console.log("Odebrano zdarzenie UpdateDisplayAboutTicket:", e);
                this.handleNewTicket(e.ticket);
            })
            .listen(".TicketEnded", (e) => {
                console.log("Odebrano zdarzenie TicketEnded:", e);
                this.handleTicketEnd({ id: e.ticket.id });
            });
    },

    methods: {
        // Rozpoczęcie przeciągania
        onDragStart(evt) {
            const fromComponent = evt.from.__draggable_component__;
            const fromList = fromComponent.realList;
            const index = evt.oldIndex;
            const item = fromList[index];

            if (item && item.id) {
                this.draggedTicket = item;
                this.tempDraggedTicket = { ...item };

                this.draggedFromSection = fromComponent.section || null;

                console.log(
                    "Ustawiono `draggedTicket` na:",
                    this.draggedTicket,
                    "z sekcji:",
                    this.draggedFromSection
                        ? this.draggedFromSection.name
                        : "Bilety"
                );
            } else {
                console.error("Błąd: `draggedTicket` nie posiada `id`");
                this.draggedTicket = null;
                this.tempDraggedTicket = null;
                this.draggedFromSection = null;
            }
        },
        // Zakończenie przeciągania
        async onEnd(event) {
            if (this.isDeleteDrop) {
                this.isDeleteDrop = false;
                return;
            }

            if (!this.draggedTicket || !this.draggedTicket.id) {
                console.error(
                    "Brak `ticketId` na koniec przeciągania.",
                    this.draggedTicket
                );
                return;
            }

            const sectionElement = event.to.closest("[data-section-id]");
            const sectionId = sectionElement
                ? Number(sectionElement.getAttribute("data-section-id"))
                : null;

            console.log("sectionId (z atrybutu danych):", sectionId);

            let workstationId;
            let newStatusId;
            let newStatus;

            if (sectionId === 0) {
                workstationId = null;
                newStatusId = 1; // ID dla "Oczekiwanie"
                newStatus = this.statusMap[1] || "Oczekiwanie";
            } else {
                const toSection = this.sections.find(
                    (section) => section.id === sectionId
                );

                if (!toSection) {
                    console.error("Sekcja nie znaleziona z id:", sectionId);
                    return;
                }

                workstationId = toSection.workstationId;
                newStatusId = 3; // "Obsługiwany"
                newStatus = this.statusMap[3] || "Obsługiwany";
            }

            this.draggedTicket.workstation_id = workstationId;
            this.draggedTicket.status_id = newStatusId;
            this.draggedTicket.status = newStatus;

            try {
                let response;
                if (sectionId === 0) {
                    response = await this.pipeQ._moveToMain(
                        this.draggedTicket.id
                    );
                } else {
                    response = await this.pipeQ._moveToSection(
                        this.draggedTicket.id,
                        workstationId,
                        newStatusId
                    );
                }

                const updatedTicket = response.data.ticket;
                if (updatedTicket) {
                    const mainIndex = this.tickets.findIndex(
                        (t) => t.id === updatedTicket.id
                    );
                    if (mainIndex !== -1) {
                        this.$set(this.tickets, mainIndex, updatedTicket);
                    }

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
                        this.tickets = this.tickets.filter(
                            (t) => t.id !== updatedTicket.id
                        );
                    }

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
                alert(
                    "Wystąpił błąd podczas przenoszenia biletu. Spróbuj ponownie."
                );
            }

            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },
        // Obsługa upuszczenia na kosz
        handleDeleteDrop(event) {
            this.isDeleteDrop = true;
            this.showDeleteModal();
        },
        showDeleteModal() {
            if (this.tempDraggedTicket) {
                this.showDeleteConfirmation = true;
                console.log(
                    "Wyświetlanie modalu usuwania dla `tempDraggedTicket`:",
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
            console.log("Próba usunięcia biletu:", this.tempDraggedTicket);

            if (this.tempDraggedTicket && this.tempDraggedTicket.id) {
                const ticketId = this.tempDraggedTicket.id;
                console.log(`Usuwanie biletu ${ticketId}.`);

                try {
                    const response = await this.pipeQ._end(ticketId);
                    console.log("Odpowiedź z _end:", response);
                    console.log("Bilet usunięty pomyślnie.");

                    this.tempDraggedTicket.status_id = 4;
                    this.tempDraggedTicket.status =
                        this.statusMap[4] || "Zwolniony";

                    this.tickets = this.tickets.filter(
                        (ticket) => ticket.id !== ticketId
                    );

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
                    alert(
                        "Wystąpił błąd podczas usuwania biletu. Spróbuj ponownie."
                    );
                }
            } else {
                console.error(
                    "Brak `ticketId` w confirmDelete lub brak `tempDraggedTicket` podczas próby usunięcia:",
                    this.tempDraggedTicket
                );
                this.showDeleteConfirmation = false;
            }
        },
        handleNewTicket(ticket) {
            console.log("handleNewTicket: Odebrano bilet:", ticket);

            if (
                !this.tickets.find((t) => t.id === ticket.id) &&
                !this.isTicketInSections(ticket.id)
            ) {
                if (ticket.workstation_id === null) {
                    ticket.status_id = 1;
                    ticket.status = this.statusMap[1] || "Oczekiwanie";
                    this.tickets.push(ticket);
                    console.log(`Dodano bilet do głównej listy:`, ticket);
                } else {
                    const section = this.sections.find(
                        (s) => s.workstationId === ticket.workstation_id
                    );
                    if (section) {
                        ticket.status =
                            this.statusMap[ticket.status_id] || ticket.status;
                        section.tickets.push(ticket);
                        console.log(
                            `Dodano bilet do sekcji ${section.name}:`,
                            ticket
                        );
                    } else {
                        console.error(
                            `Nie znaleziono sekcji dla workstation_id: ${ticket.workstation_id}`
                        );
                    }
                }
            }
        },
        handleTicketEnd({ id }) {
            console.log("handleTicketEnd: Usuwanie biletu z id:", id);
            this.tickets = this.tickets.filter((ticket) => ticket.id !== id);
            this.sections.forEach((section) => {
                section.tickets = section.tickets.filter(
                    (ticket) => ticket.id !== id
                );
            });
        },
        isTicketInSections(ticketId) {
            return this.sections.some((section) =>
                section.tickets.some((ticket) => ticket.id === ticketId)
            );
        },
    },
};
</script>
