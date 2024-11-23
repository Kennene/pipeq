<template>
    <div class="flex h-screen">
        <!-- Side Menu -->
        <aside class="w-64 bg-gray-800 text-white relative" v-if="showSideMenu">
            <div class="p-4">
                <h2 class="text-xl font-semibold">Destinations</h2>
                <ul>
                    <li
                        v-for="destination in destinations"
                        :key="destination.id"
                    >
                        <button
                            @click="selectDestination(destination)"
                            :class="{
                                'font-bold underline':
                                    destination.id === selectedDestination?.id,
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
                v-if="!showSideMenu"
                @click="toggleSideMenu"
                class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded-r"
            >
                <span>&rarr;</span>
            </button>

            <!-- Modal wyboru destination -->
            <div
                v-if="showDestinationModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            >
                <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                    <h3 class="text-xl font-semibold mb-4">
                        Wybierz dział pracy
                    </h3>
                    <ul>
                        <li
                            v-for="destination in destinations"
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
                    v-model="tickets"
                    itemKey="id"
                    class="flex flex-row flex-wrap gap-2"
                    group="tickets"
                    animation="200"
                    @start="onDragStart"
                    @end="onEnd"
                >
                    <template #item="{ element }">
                        <div
                            class="bg-blue-600 text-white rounded-md p-2 shadow-md cursor-pointer hover:bg-blue-500 transition duration-200"
                            style="width: 150px"
                        >
                            <h5 class="font-bold text-sm mb-1">
                                {{ element.user }}
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
                            v-for="(section, index) in sections"
                            :key="section.id"
                            class="bg-gray-100 p-4 rounded-lg shadow-lg flex-1 flex flex-col h-full"
                            :data-section-id="section.id"
                            @dragover.prevent
                            @drop="onSectionDrop($event, section)"
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
                                @start="onDragStart"
                                @end="onEnd"
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
                                            {{ element.user }}
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

            <!-- Trash Bin -->
            <footer
                class="bg-red-600 text-white p-4 flex justify-center items-center"
                @dragover.prevent
                @drop.prevent="handleDeleteDrop"
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
        destinations: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            tickets: this.initialTickets,
            sections: [],
            selectedDestination: null,
            showSideMenu: true,
            showDestinationModal: true,
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
        // Inicjalizacja sekcji i biletów na podstawie wybranej destynacji, jeśli istnieje
        console.log("Komponent Koordynatora zamontowany");

        window.Echo.private("display")
            .listen("UpdateDisplayAboutTicket", (e) => {
                console.log("Odebrano zdarzenie UpdateDisplayAboutTicket:", e);
                this.handleNewTicket(e.ticket);
            })
            .listen("TicketEnded", (e) => {
                console.log("Odebrano zdarzenie TicketEnded:", e);
                this.handleTicketEnd({ id: e.ticket.id });
            });

        // Jeśli wybrana destynacja jest już ustawiona, zaktualizuj sekcje i bilety
        if (this.selectedDestination) {
            this.updateSectionsAndTickets();
        }
    },
    watch: {
        selectedDestination(newVal, oldVal) {
            this.updateSectionsAndTickets();
        },
        // Opcjonalnie, jeśli `destinations` mogą się zmieniać dynamicznie
        destinations(newDestinations) {
            // Możesz tutaj zaktualizować sekcje lub inne zależne dane
        },
    },
    methods: {
        toggleSideMenu() {
            this.showSideMenu = !this.showSideMenu;
        },
        selectDestination(destination) {
            this.selectedDestination = destination;
        },
        closeDestinationModal() {
            this.showDestinationModal = false;
            this.updateSectionsAndTickets();
        },
        updateSectionsAndTickets() {
            if (!this.selectedDestination) return;

            const destinationId = this.selectedDestination.id;

            // Filtrowanie biletów dla wybranego destination
            const destinationTickets = this.initialTickets.filter(
                (ticket) => ticket.destination_id === destinationId
            );

            // Bilety nieprzypisane do żadnego workstation (czyli w głównej sekcji)
            this.tickets = destinationTickets
                .filter((ticket) => !ticket.workstation_id)
                .map((ticket) => ({
                    ...ticket,
                    status:
                        this.translations.statuses[ticket.status_id] ||
                        "Oczekiwanie",
                }));

            // Przygotowanie sekcji (workstationów) dla tego destination
            const destinationWorkstations =
                this.selectedDestination.workstations;

            this.sections = destinationWorkstations.map((workstation) => {
                return {
                    id: workstation.id,
                    name: workstation.name,
                    tickets: destinationTickets
                        .filter(
                            (ticket) => ticket.workstation_id === workstation.id
                        )
                        .map((ticket) => ({
                            ...ticket,
                            status:
                                this.translations.statuses[ticket.status_id] ||
                                "Obsługiwany",
                        })),
                    workstationId: workstation.id,
                };
            });
        },
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
        onEnd(event) {
            if (this.isDeleteDrop) {
                this.isDeleteDrop = false;
                return;
            }

            // Resetowanie zmiennych przeciągania
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },
        async onSectionDrop(event, section) {
            if (!this.draggedTicket || !this.draggedTicket.id) return;

            // Sprawdzenie, czy bilet jest przenoszony na to samo workstation
            if (this.draggedTicket.workstation_id === section.workstationId) {
                console.log(
                    "Bilet przeniesiony na to samo workstation, brak akcji."
                );
                return;
            }

            const workstationId = section.workstationId;
            const newStatusId = 2; // "Wpuszczony"
            const newStatus = this.statusMap[newStatusId] || "Wpuszczony";

            // Aktualizacja właściwości biletu
            this.draggedTicket.workstation_id = workstationId;
            this.draggedTicket.status_id = newStatusId;
            this.draggedTicket.status = newStatus;

            // Wywołanie API w celu aktualizacji na backendzie
            await this.moveTicket(
                this.draggedTicket.id,
                workstationId,
                newStatusId
            );

            // Resetowanie zmiennych przeciągania
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },
        async onMainAreaDrop() {
            if (!this.draggedTicket || !this.draggedTicket.id) return;

            // Sprawdzenie, czy bilet jest już w głównej sekcji
            if (this.draggedTicket.workstation_id === null) {
                console.log(
                    "Bilet już znajduje się w głównej sekcji, brak akcji."
                );
                return;
            }

            const workstationId = null;
            const newStatusId = 1; // "Oczekiwanie"
            const newStatus = this.statusMap[newStatusId] || "Oczekiwanie";

            // Aktualizacja właściwości biletu
            this.draggedTicket.workstation_id = workstationId;
            this.draggedTicket.status_id = newStatusId;
            this.draggedTicket.status = newStatus;

            // Wywołanie API w celu aktualizacji na backendzie
            await this.moveTicketToMain(this.draggedTicket.id);

            // Resetowanie zmiennych przeciągania
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },
        removeFromOriginalList(ticketId) {
            if (this.draggedFromSection) {
                const index = this.draggedFromSection.tickets.findIndex(
                    (t) => t.id === ticketId
                );
                if (index !== -1) {
                    this.draggedFromSection.tickets.splice(index, 1);
                }
            } else {
                const index = this.tickets.findIndex((t) => t.id === ticketId);
                if (index !== -1) {
                    this.tickets.splice(index, 1);
                }
            }
        },
        async moveTicket(ticketId, workstationId, statusId) {
            try {
                await this.pipeQ._moveToSection(
                    ticketId,
                    workstationId,
                    statusId
                );
            } catch (error) {
                console.error("Błąd podczas przenoszenia biletu:", error);
                alert(
                    "Wystąpił błąd podczas przenoszenia biletu. Spróbuj ponownie."
                );
            }
        },
        async moveTicketToMain(ticketId) {
            try {
                await this.pipeQ._moveToMain(ticketId);
            } catch (error) {
                console.error(
                    "Błąd podczas przenoszenia biletu do głównej listy:",
                    error
                );
                alert(
                    "Wystąpił błąd podczas przenoszenia biletu. Spróbuj ponownie."
                );
            }
        },
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
            console.log("handleNewTicket: Received ticket:", ticket);

            // Sprawdź, czy bilet już istnieje w głównej liście
            let existingTicket = this.tickets.find((t) => t.id === ticket.id);

            if (existingTicket) {
                // Aktualizuj dane biletu, jeśli to konieczne
                Object.assign(existingTicket, ticket);
                return;
            }

            // Sprawdź, czy bilet istnieje w jakiejś sekcji
            for (const section of this.sections) {
                existingTicket = section.tickets.find(
                    (t) => t.id === ticket.id
                );
                if (existingTicket) {
                    // Aktualizuj dane biletu, jeśli to konieczne
                    Object.assign(existingTicket, ticket);
                    return;
                }
            }

            // Jeśli bilet nie został znaleziony, dodaj go do odpowiedniej listy
            if (ticket.workstation_id === null) {
                ticket.status_id = 1;
                ticket.status = this.statusMap[1] || "Oczekiwanie";
                this.tickets.push(ticket);
                console.log(`Dodano nowy bilet do głównej listy:`, ticket);
            } else {
                const section = this.sections.find(
                    (s) => s.workstationId === ticket.workstation_id
                );
                if (section) {
                    ticket.status =
                        this.statusMap[ticket.status_id] || ticket.status;
                    section.tickets.push(ticket);
                    console.log(
                        `Dodano nowy bilet do sekcji ${section.name}:`,
                        ticket
                    );
                } else {
                    console.error(
                        `Nie znaleziono sekcji dla workstation_id: ${ticket.workstation_id}`
                    );
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
        // Nowa metoda do automatycznego dodawania destynacji
        addDestination(newDestination) {
            this.destinations.push(newDestination);
        },
    },
};
</script>
