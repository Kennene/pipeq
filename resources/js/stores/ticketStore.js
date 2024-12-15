import { defineStore } from "pinia";
import PipeQ from "../pipeq";

export const useTicketStore = defineStore("ticketStore", {
    state: () => ({
        allTickets: [],
        mainTickets: [],
        sections: [],
        selectedDestination: null,
        showSideMenu: false, // boczne menu nie jest używane, możemy zostawić to false
        draggedTicket: null,
        tempDraggedTicket: null,
        draggedFromSection: null,
        showDeleteConfirmation: false,
        isDeleteDrop: false,
        pipeQ: new PipeQ(),
        statusMap: {},
        destinations: [],
        originalWorkstationId: null,
    }),
    actions: {
        initialize(translations, initialTickets, destinations) {
            this.statusMap = translations.statuses;
            this.destinations = destinations.map((dest) => ({
                ...dest,
                workstations: dest.workstations || [],
            }));
            this.allTickets = initialTickets;

            if (this.destinations.length > 0) {
                this.selectDestination(this.destinations[0]);
            }
        },

        selectDestination(destination) {
            this.selectedDestination = destination;
            this.updateSectionsAndTickets();
        },

        updateSectionsAndTickets() {
            if (!this.selectedDestination) return;

            const destinationId = this.selectedDestination.id;
            const destinationTickets = this.allTickets.filter(
                (ticket) => ticket.destination_id === destinationId
            );

            this.mainTickets = destinationTickets
                .filter((ticket) => !ticket.workstation_id)
                .map((ticket) => ({
                    ...ticket,
                    status: this.statusMap[ticket.status_id] || "Oczekiwanie",
                }));

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

        onDragStart(evt) {
            const fromComponent = evt.from.__draggable_component__;
            const fromList = fromComponent.realList;
            const index = evt.oldIndex;
            const item = fromList[index];

            if (item && item.id) {
                this.draggedTicket = item;
                this.tempDraggedTicket = { ...item };
                this.draggedFromSection = fromComponent.section || null;
                this.originalWorkstationId = item.workstation_id;
            } else {
                this.draggedTicket = null;
                this.tempDraggedTicket = null;
                this.draggedFromSection = null;
                this.originalWorkstationId = null;
            }
        },

        onEnd(event) {
            if (this.isDeleteDrop) {
                this.isDeleteDrop = false;
                return;
            }
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
            this.originalWorkstationId = null;
        },

        async onSectionDrop(section) {
            if (!this.draggedTicket || !this.draggedTicket.id) return;

            if (this.draggedTicket.workstation_id === section.workstationId) {
                return;
            }

            const previousWorkstationId = this.draggedTicket.workstation_id;
            const previousStatusId = this.draggedTicket.status_id;
            const previousStatus = this.draggedTicket.status;

            const newStatusId = 2; // Wpuszczony
            const newStatus = this.statusMap[newStatusId] || "Wpuszczony";

            this.draggedTicket.workstation_id = section.workstationId;
            this.draggedTicket.status_id = newStatusId;
            this.draggedTicket.status = newStatus;

            this.updateSectionsAndTickets();

            this.moveTicket(
                this.draggedTicket.id,
                section.workstationId,
                newStatusId
            ).catch((error) => {
                alert(
                    "Wystąpił błąd podczas przenoszenia biletu. Przywracanie poprzedniego stanu."
                );
                this.draggedTicket.workstation_id = previousWorkstationId;
                this.draggedTicket.status_id = previousStatusId;
                this.draggedTicket.status = previousStatus;
                this.updateSectionsAndTickets();
            });

            this.draggedTicket = null;
            this.tempDraggedTicket = null;
            this.originalWorkstationId = null;
        },

        async onMainAreaDrop() {
            if (!this.draggedTicket || !this.draggedTicket.id) return;

            if (this.draggedTicket.workstation_id === null) {
                return;
            }

            const previousWorkstationId = this.draggedTicket.workstation_id;
            const previousStatusId = this.draggedTicket.status_id;
            const previousStatus = this.draggedTicket.status;

            const newStatusId = 1; // Oczekiwanie
            const newStatus = this.statusMap[newStatusId] || "Oczekiwanie";

            this.draggedTicket.workstation_id = null;
            this.draggedTicket.status_id = newStatusId;
            this.draggedTicket.status = newStatus;

            this.updateSectionsAndTickets();

            this.moveTicketToMain(this.draggedTicket.id).catch((error) => {
                alert(
                    "Wystąpił błąd podczas przenoszenia biletu. Przywracanie poprzedniego stanu."
                );
                this.draggedTicket.workstation_id = previousWorkstationId;
                this.draggedTicket.status_id = previousStatusId;
                this.draggedTicket.status = previousStatus;
                this.updateSectionsAndTickets();
            });

            this.draggedTicket = null;
            this.tempDraggedTicket = null;
            this.originalWorkstationId = null;
        },

        removeFromOriginalList(ticketId) {
            // Usuwanie z oryginalnej listy nie jest już tak istotne, ale pozostawiamy oryginalną logikę
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

        async moveTicket(ticketId, workstationId, statusId) {
            try {
                await this.pipeQ._moveToSection(
                    ticketId,
                    workstationId,
                    statusId
                );
            } catch (error) {
                // Obsługa błędu w onSectionDrop
            }
        },

        async moveTicketToMain(ticketId) {
            try {
                await this.pipeQ._moveToMain(ticketId);
            } catch (error) {
                // Obsługa błędu w onMainAreaDrop
            }
        },

        handleDeleteDrop() {
            this.isDeleteDrop = true;
            this.showDeleteModal();
        },

        showDeleteModal() {
            if (this.tempDraggedTicket) {
                this.showDeleteConfirmation = true;
            }
        },

        cancelDeleteAndRestore() {
            this.showDeleteConfirmation = false;

            // Przywracamy oryginalne położenie biletu
            if (this.tempDraggedTicket && this.tempDraggedTicket.id != null) {
                const ticketId = this.tempDraggedTicket.id;
                const ticket = this.allTickets.find((t) => t.id === ticketId);
                if (ticket) {
                    ticket.workstation_id = this.originalWorkstationId;
                    this.updateSectionsAndTickets();
                }
            }

            this.draggedTicket = null;
            this.tempDraggedTicket = null;
            this.originalWorkstationId = null;
        },

        cancelDelete() {
            this.showDeleteConfirmation = false;
            this.draggedTicket = null;
            this.tempDraggedTicket = null;
            this.originalWorkstationId = null;
        },

        async confirmDelete() {
            if (this.tempDraggedTicket && this.tempDraggedTicket.id) {
                const ticketId = this.tempDraggedTicket.id;
                const removedTicket = this.allTickets.find(
                    (ticket) => ticket.id === ticketId
                );
                this.allTickets = this.allTickets.filter(
                    (ticket) => ticket.id !== ticketId
                );
                this.updateSectionsAndTickets();
                this.showDeleteConfirmation = false;

                this.pipeQ._end(ticketId).catch((error) => {
                    alert(
                        "Wystąpił błąd podczas usuwania biletu. Przywracanie biletu."
                    );
                    this.allTickets.push(removedTicket);
                    this.updateSectionsAndTickets();
                });

                this.draggedTicket = null;
                this.tempDraggedTicket = null;
                this.originalWorkstationId = null;
            } else {
                this.showDeleteConfirmation = false;
            }
        },

        handleNewTicket(ticket) {
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

            this.updateSectionsAndTickets();
        },

        handleTicketEnd({ id }) {
            this.allTickets = this.allTickets.filter(
                (ticket) => ticket.id !== id
            );
            this.updateSectionsAndTickets();
        },

        initializeWebSocket() {
            window.Echo.private("display")
                .listen("UpdateDisplayAboutTicket", (e) => {
                    this.handleNewTicket(e.ticket);
                })
                .listen("NotifyEndedTicketDisplay", (e) => {
                    this.handleTicketEnd({ id: e.ticket.id });
                });
        },

        async changeTicketDestination(ticketId, destinationId) {
            const ticketIndex = this.allTickets.findIndex(
                (t) => t.id === ticketId
            );
            if (ticketIndex === -1) return;
            const oldDestinationId =
                this.allTickets[ticketIndex].destination_id;
            const oldWorkstationId =
                this.allTickets[ticketIndex].workstation_id;

            this.allTickets[ticketIndex].destination_id = destinationId;
            this.allTickets[ticketIndex].workstation_id = null; // wraca do kolejki głównej
            this.updateSectionsAndTickets();

            try {
                await this.pipeQ._changeDestination(ticketId, destinationId);
            } catch (error) {
                alert(
                    "Wystąpił błąd podczas zmiany destynacji. Przywracanie poprzedniego stanu."
                );
                this.allTickets[ticketIndex].destination_id = oldDestinationId;
                this.allTickets[ticketIndex].workstation_id = oldWorkstationId;
                this.updateSectionsAndTickets();
            }

            this.draggedTicket = null;
            this.tempDraggedTicket = null;
            this.originalWorkstationId = null;
        },

        async doubleClickToReEnter(ticket) {
            if (!ticket.workstation_id) return;
            const workstationId = ticket.workstation_id;
            const statusId = 2; // Wpuszczony
            const previousStatusId = ticket.status_id;
            const previousStatus = ticket.status;

            ticket.status_id = statusId;
            ticket.status = this.statusMap[statusId] || "Wpuszczony";

            this.updateSectionsAndTickets();

            try {
                await this.pipeQ._moveToSection(
                    ticket.id,
                    workstationId,
                    statusId
                );
            } catch (error) {
                alert(
                    "Wystąpił błąd podczas ponownego wywołania wejścia. Przywracanie poprzedniego stanu."
                );
                ticket.status_id = previousStatusId;
                ticket.status = previousStatus;
                this.updateSectionsAndTickets();
            }
        },
    },
});
