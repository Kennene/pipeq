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
    async _moveToSection(ticketId, workstationId, statusId = 3) {
        if (!ticketId || !workstationId) {
            console.error(
                "Missing ticketId or workstationId for _moveToSection"
            );
            throw new Error("Missing ticketId or workstationId");
        }

        console.log(
            `Moving ticket ${ticketId} to workstation ${workstationId} with status ${statusId}`
        );

        try {
            const response = await axios.post(
                `/move/${ticketId}/${workstationId}/${statusId}`
            );
            console.log("Response from _moveToSection:", response);
            return response;
        } catch (error) {
            console.error(
                "Error during _moveToSection:",
                error.response?.data || error.message
            );
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
            console.error("Missing ticketId for _moveToMain");
            throw new Error("Missing ticketId");
        }

        console.log(`Moving ticket ${ticketId} to the main list`);

        try {
            const response = await axios.post(`/move/${ticketId}`);
            console.log("Response from _moveToMain:", response);
            return response;
        } catch (error) {
            console.error(
                "Error during _moveToMain:",
                error.response?.data || error.message
            );
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
            console.error("Missing ticketId for _end");
            throw new Error("Missing ticketId");
        }

        console.log(`Deleting ticket ${ticketId} using _end`);

        try {
            const response = await axios.post(`/end/${ticketId}`);
            console.log("Response from _end:", response);
            return response;
        } catch (error) {
            console.error(
                "Error during _end:",
                error.response?.data || error.message
            );
            throw error;
        }
    }
}

export default PipeQ;
