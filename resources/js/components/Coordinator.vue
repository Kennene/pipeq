<template>
    <div class="h-screen flex flex-col">
        
        @include('topbar')

        <!-- Tickets list under the header -->
        <div
            class="bg-gray-200 p-4 flex items-center space-x-4 overflow-x-auto"
            @drop.prevent="returnTicket"
            @dragover.prevent
        >
            <span class="text-lg font-semibold">Tickets</span>
            <draggable
                v-model="tickets"
                itemKey="id"
                class="flex space-x-4"
                group="tickets"
                animation="200"
                @end="onEnd"
            >
                <template #item="{ element }">
                    <div
                        class="bg-blue-600 text-white rounded-md px-6 py-3 shadow-lg cursor-pointer hover:bg-blue-500 transition duration-200"
                    >
                        Ticket #{{ element.id }}
                    </div>
                </template>
            </draggable>
        </div>

        <!-- Main layout -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <aside
                :class="[
                    'bg-gray-100 p-4 space-y-4 shadow-lg transition-transform duration-300',
                    menuExpanded ? 'w-64' : 'w-16',
                ]"
            >
                <button
                    @click="toggleMenu"
                    class="bg-blue-600 text-white rounded-md w-full flex items-center justify-center py-3"
                >
                    <span v-if="menuExpanded">Zwiń menu</span>
                    <span v-else class="material-icons text-3xl">menu</span>
                </button>
                <nav v-if="menuExpanded" class="space-y-2">
                    <a href="#" class="block text-gray-700 hover:text-blue-600"
                        >Opcja 1</a
                    >
                    <a href="#" class="block text-gray-700 hover:text-blue-600"
                        >Opcja 2</a
                    >
                    <a href="#" class="block text-gray-700 hover:text-blue-600"
                        >Opcja 3</a
                    >
                </nav>
            </aside>

            <!-- Main content -->
            <main class="flex-1 bg-white p-6 overflow-auto">
                <div
                    class="flex flex-1 space-x-6 overflow-x-auto pb-6 h-full relative"
                >
                    <button
                        @click="showAddSectionModal = true"
                        class="bg-green-600 text-white rounded-full w-16 h-16 shadow-md hover:bg-green-500 transition duration-200 flex items-center justify-center absolute top-1/2 transform -translate-y-1/2 right-4"
                    >
                        <span class="material-icons text-3xl">add</span>
                    </button>
                    <draggable
                        v-for="(section, index) in sections"
                        :key="section.id"
                        itemKey="id"
                        class="bg-gray-100 p-6 rounded-lg shadow-lg min-w-[300px] flex-1 flex flex-col space-y-4 h-full"
                        v-model="section.tickets"
                        group="tickets"
                        animation="200"
                        @end="onEnd"
                    >
                        <template #header>
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    {{ section.name }}
                                </h3>
                                <div class="flex space-x-2">
                                    <button
                                        @click="editSection(section)"
                                        class="text-white bg-blue-600 hover:bg-blue-700 w-12 h-12 rounded-full shadow-md transition duration-200 flex items-center justify-center"
                                    >
                                        <span class="material-icons">edit</span>
                                    </button>
                                    <button
                                        @click="removeSection(section.id)"
                                        class="text-white bg-red-600 hover:bg-red-700 w-12 h-12 rounded-full shadow-md transition duration-200 flex items-center justify-center"
                                    >
                                        <span class="material-icons"
                                            >delete</span
                                        >
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div class="relative mb-4">
                            <div class="edit-delete-buttons">
                                <button
                                    @click="editSection(section)"
                                    class="text-white bg-blue-600 hover:bg-blue-700 p-3 rounded-full shadow-md transition duration-200 flex items-center justify-center"
                                >
                                    <span class="material-icons text-3xl"
                                        >edit</span
                                    >
                                </button>
                                <button
                                    @click="removeSection(section.id)"
                                    class="text-white bg-red-600 hover:bg-red-700 p-3 rounded-full shadow-md transition duration-200 flex items-center justify-center"
                                >
                                    <span class="material-icons text-3xl"
                                        >delete</span
                                    >
                                </button>
                            </div>
                            <input
                                v-model="section.name"
                                class="text-lg font-semibold text-gray-800 mb-2 bg-transparent border-b-2 border-gray-300 focus:outline-none focus:border-blue-600 w-full"
                                placeholder="Wpisz nazwę sekcji"
                            />
                        </div>
                        <template #item="{ element }">
                            <div
                                class="bg-blue-600 text-white rounded-md px-4 py-2 shadow-md cursor-pointer hover:bg-blue-500 transition duration-200"
                                draggable="true"
                                @dragstart="dragStart(element)"
                            >
                                Ticket #{{ element.id }}
                            </div>
                        </template>
                    </draggable>
                </div>
                <!-- Edit Section Modal -->
                <div
                    v-if="showEditSectionModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
                >
                    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                        <h3 class="text-xl font-semibold mb-4">
                            Edytuj nazwę sekcji
                        </h3>
                        <input
                            v-model="editedSectionName"
                            class="w-full p-3 border-2 border-gray-300 rounded-lg mb-6 focus:outline-none focus:border-blue-600 text-lg"
                            placeholder="Wpisz nową nazwę sekcji"
                        />
                        <div class="flex justify-end space-x-4">
                            <button
                                @click="showEditSectionModal = false"
                                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                            >
                                Anuluj
                            </button>
                            <button
                                @click="saveSectionName"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-500"
                            >
                                Zapisz
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Confirm Deletion Modal -->
                <div
                    v-if="showDeleteConfirmation"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                        <h3 class="text-xl font-semibold mb-4">
                            Czy na pewno chcesz usunąć Ticket #{{
                                draggedTicket?.id
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

        <!-- Add Section Modal -->
        <div
            v-if="showAddSectionModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
        >
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                <h3 class="text-xl font-semibold mb-4">Dodaj nową sekcję</h3>
                <input
                    v-model="newSectionName"
                    class="w-full p-3 border-2 border-gray-300 rounded-lg mb-6 focus:outline-none focus:border-blue-600 text-lg"
                    placeholder="Wpisz nazwę sekcji"
                />
                <div class="flex justify-end space-x-4">
                    <button
                        @click="showAddSectionModal = false"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                    >
                        Anuluj
                    </button>
                    <button
                        @click="addSection"
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-500"
                    >
                        Dodaj
                    </button>
                </div>
            </div>
        </div>

        <!-- Trash bin -->
        <footer
            class="bg-red-600 text-white p-4 flex justify-center items-center"
        >
            <div
                class="bg-red-700 hover:bg-red-800 transition duration-200 p-4 rounded-full cursor-pointer"
                @drop.prevent="showDeleteModal"
                @dragover.prevent
            >
                🗑️ Przeciągnij tutaj, aby usunąć ticket
            </div>
        </footer>
    </div>
</template>

<script>
import draggable from "vuedraggable";

export default {
    components: { draggable },
    data() {
        return {
            showEditSectionModal: false,
            editedSection: null,
            editedSectionName: "",
            menuExpanded: true,
            draggedTicket: null,
            showDeleteConfirmation: false,
            showEditSectionModal: false,
            editedSection: null,
            editedSectionName: "",
            menuExpanded: true,
            draggedTicket: null,
            tickets: [{ id: 1 }, { id: 2 }, { id: 3 }],
            sections: [
                { id: 1, name: "Sekcja 1", tickets: [] },
                { id: 2, name: "Sekcja 2", tickets: [] },
            ],
            showAddSectionModal: false,
            newSectionName: "",
        };
    },
    methods: {
        cancelDelete() {
            this.showDeleteConfirmation = false;
            this.draggedTicket = null;
        },
        confirmDelete() {
            if (this.draggedTicket) {
                const ticketId = this.draggedTicket.id;

                // Usuwamy ticket z górnej listy
                this.tickets = this.tickets.filter(
                    (ticket) => ticket.id !== ticketId
                );

                // Usuwamy ticket z sekcji
                this.sections.forEach((section) => {
                    section.tickets = section.tickets.filter(
                        (ticket) => ticket.id !== ticketId
                    );
                });

                // Zerowanie draggedTicket
                this.draggedTicket = null;
                this.showDeleteConfirmation = false;
            }
        },
        editSection(section) {
            this.editedSection = section;
            this.editedSectionName = section.name;
            this.showEditSectionModal = true;
        },
        saveSectionName() {
            if (!this.editedSectionName) {
                alert("Nazwa sekcji jest wymagana!");
                return;
            }
            this.editedSection.name = this.editedSectionName;
            this.showEditSectionModal = false;
        },
        toggleMenu() {
            this.menuExpanded = !this.menuExpanded;
        },
        addSection() {
            if (!this.newSectionName) {
                alert("Nazwa sekcji jest wymagana!");
                return;
            }
            const newSectionId = this.sections.length + 1;
            this.sections.push({
                id: newSectionId,
                name: this.newSectionName,
                tickets: [],
            });
            this.newSectionName = "";
            this.showAddSectionModal = false;
        },
        removeSection(sectionId) {
            this.sections = this.sections.filter(
                (section) => section.id !== sectionId
            );
        },
        dragStart(ticket) {
            this.draggedTicket = ticket;
        },
        showDeleteModal() {
            if (this.draggedTicket) {
                this.showDeleteConfirmation = true;
            }
        },
        removeTicket() {
            if (this.draggedTicket) {
                const ticketId = this.draggedTicket.id;

                // Usuwamy ticket z górnej listy
                this.tickets = this.tickets.filter(
                    (ticket) => ticket.id !== ticketId
                );

                // Usuwamy ticket z sekcji
                this.sections.forEach((section) => {
                    section.tickets = section.tickets.filter(
                        (ticket) => ticket.id !== ticketId
                    );
                });

                // Zerowanie draggedTicket
                this.draggedTicket = null;
            }
        },
        returnTicket() {
            if (this.draggedTicket) {
                // Dodaj ticket z powrotem do głównej listy ticketów
                this.tickets.push(this.draggedTicket);

                // Usuń ticket z sekcji
                this.sections.forEach((section) => {
                    section.tickets = section.tickets.filter(
                        (ticket) => ticket.id !== this.draggedTicket.id
                    );
                });

                // Zerowanie draggedTicket
                this.draggedTicket = null;
            }
        },
        onEnd() {
            // Aktualizowanie po przeniesieniu ticketów
        },
        logout() {
            // Wywołanie wylogowania z serwera
            axios
                .post("/logout")
                .then(() => {
                    // Usuń token lub dane autoryzacji z localStorage
                    localStorage.removeItem("token");

                    // Przekierowanie na stronę logowania
                    window.location.href = "/login";
                })
                .catch((error) => {
                    console.error("Błąd podczas wylogowywania:", error);
                });
        },
    },
};
</script>

<style scoped>
.edit-delete-buttons {
    position: absolute;
    top: -10px;
    right: -10px;
    display: flex;
    gap: 8px;
}
.edit-delete-buttons button {
    width: 40px;
    height: 40px;
}
.edit-delete-buttons {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    gap: 8px;
}
.edit-delete-buttons button {
    width: 40px;
    height: 40px;
}
.material-icons {
    font-size: 24px;
}
</style>
