import { defineStore } from "pinia";
import PipeQ from "../pipeq";

export const useTicketStore = defineStore("ticketStore", {
    state: () => ({
        allTickets: [],
        mainTickets: [],
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
        statusMap: {},
        destinations: [],
    }),
    actions: {
        /**
         * Inicjalizacja store'a z danymi przekazanymi jako propsy.
         * @param {Object} translations - Obiekt tłumaczeń statusów.
         * @param {Array} initialTickets - Początkowa lista biletów.
         * @param {Array} destinations - Lista destynacji.
         */
        initialize(translations, initialTickets, destinations) {
            this.statusMap = translations.statuses;
            this.destinations = destinations.map((dest) => ({
                ...dest,
                workstations: dest.workstations || [], // Upewnij się, że każda destynacja ma workstations
            }));
            this.allTickets = initialTickets;

            // Jeśli destynacje są dostępne, automatycznie wybierz pierwszą lub pozostaw bez wyboru
            if (this.destinations.length > 0) {
                this.selectDestination(this.destinations[0]); // Opcjonalnie automatycznie wybiera pierwszą destynację
            }
        },

        /**
         * Przełącza widoczność menu bocznego.
         */
        toggleSideMenu() {
            this.showSideMenu = !this.showSideMenu;
        },

        /**
         * Ustawia wybraną destynację i aktualizuje listę biletów oraz sekcji.
         * @param {Object} destination - Wybrana destynacja.
         */
        selectDestination(destination) {
            this.selectedDestination = destination;
            this.updateSectionsAndTickets();
        },

        /**
         * Zamknięcie modalu wyboru destynacji.
         */
        closeDestinationModal() {
            this.showDestinationModal = false;
        },

        /**
         * Aktualizuje `mainTickets` i `sections` na podstawie wybranej destynacji.
         */
        updateSectionsAndTickets() {
            if (!this.selectedDestination) return;

            const destinationId = this.selectedDestination.id;

            // Filtrowanie biletów dla wybranej destynacji
            const destinationTickets = this.allTickets.filter(
                (ticket) => ticket.destination_id === destinationId
            );

            // Bilety nieprzypisane do żadnego workstation (czyli w głównej sekcji)
            this.mainTickets = destinationTickets
                .filter((ticket) => !ticket.workstation_id)
                .map((ticket) => ({
                    ...ticket,
                    status: this.statusMap[ticket.status_id] || "Oczekiwanie",
                }));

            // Przygotowanie sekcji (workstationów) dla tej destynacji
            const destinationWorkstations =
                this.selectedDestination.workstations || [];

            this.sections = destinationWorkstations.map((workstation) => {
                const sectionTickets = destinationTickets
                    .filter(
                        (ticket) => ticket.workstation_id === workstation.id
                    )
                    .map((ticket) => ({
                        ...ticket,
                        status:
                            this.statusMap[ticket.status_id] || "Obsługiwany",
                    }));
                return {
                    id: workstation.id,
                    name: workstation.name,
                    tickets: sectionTickets,
                    workstationId: workstation.id,
                };
            });
        },

        /**
         * Obsługuje rozpoczęcie przeciągania biletu.
         * @param {Object} evt - Event przeciągania.
         */
        onDragStart(evt) {
            const fromComponent = evt.from.__draggable_component__;
            const fromList = fromComponent.realList;
            const index = evt.oldIndex;
            const item = fromList[index];

            if (item && item.id) {
                this.draggedTicket = item;
                this.tempDraggedTicket = { ...item };

                this.draggedFromSection = fromComponent.section || null;
            } else {
                this.draggedTicket = null;
                this.tempDraggedTicket = null;
                this.draggedFromSection = null;
            }
        },

        /**
         * Obsługuje zakończenie przeciągania biletu.
         * @param {Object} event - Event przeciągania.
         */
        onEnd(event) {
            if (this.isDeleteDrop) {
                this.isDeleteDrop = false;
                return;
            }

            // Resetowanie zmiennych przeciągania
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },

        /**
         * Obsługuje upuszczenie biletu do sekcji.
         * @param {Object} section - Sekcja, do której upuszczono bilet.
         */
        async onSectionDrop(section) {
            if (!this.draggedTicket || !this.draggedTicket.id) return;

            // Sprawdzenie, czy bilet jest przenoszony na tę samą workstation
            if (this.draggedTicket.workstation_id === section.workstationId) {
                return;
            }

            const previousWorkstationId = this.draggedTicket.workstation_id;
            const previousStatusId = this.draggedTicket.status_id;
            const previousStatus = this.draggedTicket.status;

            const newStatusId = 2; // "Wpuszczony"
            const newStatus = this.statusMap[newStatusId] || "Wpuszczony";

            // Optymistyczna aktualizacja właściwości biletu
            this.draggedTicket.workstation_id = section.workstationId;
            this.draggedTicket.status_id = newStatusId;
            this.draggedTicket.status = newStatus;

            // Aktualizacja UI natychmiast
            this.updateSectionsAndTickets();

            // Wykonanie żądania API w tle
            this.moveTicket(
                this.draggedTicket.id,
                section.workstationId,
                newStatusId
            ).catch((error) => {
                alert(
                    "Wystąpił błąd podczas przenoszenia biletu. Przywracanie poprzedniego stanu."
                );

                // Przywrócenie poprzedniego stanu biletu
                this.draggedTicket.workstation_id = previousWorkstationId;
                this.draggedTicket.status_id = previousStatusId;
                this.draggedTicket.status = previousStatus;
                this.updateSectionsAndTickets();
            });

            // Resetowanie zmiennych przeciągania
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },

        /**
         * Obsługuje upuszczenie biletu do głównej sekcji.
         */
        async onMainAreaDrop() {
            if (!this.draggedTicket || !this.draggedTicket.id) return;

            // Sprawdzenie, czy bilet jest już w głównej sekcji
            if (this.draggedTicket.workstation_id === null) {
                return;
            }

            const previousWorkstationId = this.draggedTicket.workstation_id;
            const previousStatusId = this.draggedTicket.status_id;
            const previousStatus = this.draggedTicket.status;

            const newStatusId = 1; // "Oczekiwanie"
            const newStatus = this.statusMap[newStatusId] || "Oczekiwanie";

            // Optymistyczna aktualizacja właściwości biletu
            this.draggedTicket.workstation_id = null;
            this.draggedTicket.status_id = newStatusId;
            this.draggedTicket.status = newStatus;

            // Aktualizacja UI natychmiast
            this.updateSectionsAndTickets();

            // Wykonanie żądania API w tle
            this.moveTicketToMain(this.draggedTicket.id).catch((error) => {
                alert(
                    "Wystąpił błąd podczas przenoszenia biletu. Przywracanie poprzedniego stanu."
                );

                // Przywrócenie poprzedniego stanu biletu
                this.draggedTicket.workstation_id = previousWorkstationId;
                this.draggedTicket.status_id = previousStatusId;
                this.draggedTicket.status = previousStatus;
                this.updateSectionsAndTickets();
            });

            // Resetowanie zmiennych przeciągania
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },

        /**
         * Usuwa bilet z oryginalnej listy po przeciągnięciu.
         * @param {Number} ticketId - ID biletu do usunięcia.
         */
        removeFromOriginalList(ticketId) {
            if (this.draggedFromSection) {
                const index = this.draggedFromSection.tickets.findIndex(
                    (t) => t.id === ticketId
                );
                if (index !== -1) {
                    this.draggedFromSection.tickets.splice(index, 1);
                }
            } else {
                const index = this.mainTickets.findIndex(
                    (t) => t.id === ticketId
                );
                if (index !== -1) {
                    this.mainTickets.splice(index, 1);
                }
            }
        },

        /**
         * Przenosi bilet do sekcji na backendzie.
         * @param {Number} ticketId - ID biletu.
         * @param {Number} workstationId - ID workstation.
         * @param {Number} statusId - Nowy status ID.
         */
        async moveTicket(ticketId, workstationId, statusId) {
            try {
                await this.pipeQ._moveToSection(
                    ticketId,
                    workstationId,
                    statusId
                );
            } catch (error) {
                // Obsługa błędu jest już wykonana w onSectionDrop/onMainAreaDrop
            }
        },

        /**
         * Przenosi bilet do głównej sekcji na backendzie.
         * @param {Number} ticketId - ID biletu.
         */
        async moveTicketToMain(ticketId) {
            try {
                await this.pipeQ._moveToMain(ticketId);
            } catch (error) {
                // Obsługa błędu jest już wykonana w onMainAreaDrop
            }
        },

        /**
         * Obsługuje przeciągnięcie biletu do kosza.
         */
        handleDeleteDrop() {
            this.isDeleteDrop = true;
            this.showDeleteModal();
        },

        /**
         * Pokazuje modal potwierdzenia usunięcia biletu.
         */
        showDeleteModal() {
            if (this.tempDraggedTicket) {
                this.showDeleteConfirmation = true;
            }
        },

        /**
         * Anuluje usunięcie biletu.
         */
        cancelDelete() {
            this.showDeleteConfirmation = false;
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
        },

        /**
         * Potwierdza usunięcie biletu.
         */
        async confirmDelete() {
            if (this.tempDraggedTicket && this.tempDraggedTicket.id) {
                const ticketId = this.tempDraggedTicket.id;

                // Optymistyczne usunięcie biletu z UI
                const removedTicket = this.allTickets.find(
                    (ticket) => ticket.id === ticketId
                );
                this.allTickets = this.allTickets.filter(
                    (ticket) => ticket.id !== ticketId
                );
                this.updateSectionsAndTickets();
                this.showDeleteConfirmation = false;

                // Wykonanie żądania API w tle
                this.pipeQ
                    ._end(ticketId)
                    .then((response) => {
                        // Obsługa sukcesu może być dodana tutaj, jeśli jest potrzebna
                    })
                    .catch((error) => {
                        alert(
                            "Wystąpił błąd podczas usuwania biletu. Przywracanie biletu."
                        );

                        // Przywrócenie biletu w UI
                        this.allTickets.push(removedTicket);
                        this.updateSectionsAndTickets();
                    });

                this.draggedTicket = null;
                this.tempDraggedTicket = null;
            } else {
                this.showDeleteConfirmation = false;
            }
        },

        /**
         * Obsługuje otrzymanie nowego biletu przez WebSocket.
         * @param {Object} ticket - Nowy bilet.
         */
        handleNewTicket(ticket) {
            // Dodaj lub aktualizuj bilet w allTickets
            const existingIndex = this.allTickets.findIndex(
                (t) => t.id === ticket.id
            );
            if (existingIndex !== -1) {
                // Aktualizacja istniejącego biletu
                this.allTickets[existingIndex] = {
                    ...this.allTickets[existingIndex],
                    ...ticket,
                };
            } else {
                // Dodanie nowego biletu
                this.allTickets.push(ticket);
            }

            // Aktualizacja sekcji i mainTickets
            this.updateSectionsAndTickets();
        },

        /**
         * Obsługuje zakończenie biletu przez WebSocket.
         * @param {Object} payload - Zawiera ID biletu.
         */
        handleTicketEnd({ id }) {
            this.allTickets = this.allTickets.filter(
                (ticket) => ticket.id !== id
            );
            this.updateSectionsAndTickets();
        },

        /**
         * Inicjalizuje połączenie WebSocket.
         */
        initializeWebSocket() {
            window.Echo.private("display")
                .listen("UpdateDisplayAboutTicket", (e) => {
                    this.handleNewTicket(e.ticket);
                })
                .listen("NotifyEndedTicketDisplay", (e) => {
                    this.handleTicketEnd({ id: e.ticket.id });
                });
        },
    },
});
