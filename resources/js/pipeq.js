import "./echo";
import axios from "axios";

class PipeQ {
    constructor() {}

    /**
     * Moves a ticket to a section with a specified workstation and status.
     * @param {string|number} ticketId - ID of the ticket to move.
     * @param {string|number} workstationId - ID of the target workstation.
     * @param {number} statusId - ID of the ticket status.
     * @returns {Promise} - Promise with the server response.
     */
    async _moveToSection(ticketId, workstationId, statusId = 2) {
        if (!ticketId || !workstationId) {
            throw new Error("Missing ticketId or workstationId");
        }

        try {
            const response = await axios.post(
                `/move/${ticketId}/${workstationId}/${statusId}`
            );
            return response;
        } catch (error) {
            console.error("Error in _moveToSection:", error);
            throw error;
        }
    }

    /**
     * Moves a ticket to the main list (without assigning to a workstation or changing status).
     * @param {string|number} ticketId - ID of the ticket to move.
     * @returns {Promise} - Promise with the server response.
     */
    async _moveToMain(ticketId) {
        if (!ticketId) {
            throw new Error("Missing ticketId");
        }

        try {
            const response = await axios.post(`/move/${ticketId}`);
            return response;
        } catch (error) {
            console.error("Error in _moveToMain:", error);
            throw error;
        }
    }

    /**
     * Ends (deletes) a ticket.
     * @param {string|number} ticketId - ID of the ticket to delete.
     * @returns {Promise} - Promise with the server response.
     */
    async _end(ticketId) {
        if (!ticketId) {
            throw new Error("Missing ticketId");
        }

        try {
            const response = await axios.post(`/end/${ticketId}`);
            return response;
        } catch (error) {
            console.error("Error in _end:", error);
            throw error;
        }
    }
}

export default PipeQ;
