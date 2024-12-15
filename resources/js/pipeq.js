import "./echo";
import axios from "axios";

class PipeQ {
    constructor() {}

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

    // Nowa metoda zmiany destynacji
    async _changeDestination(ticketId, destinationId) {
        if (!ticketId || !destinationId) {
            throw new Error("Missing ticketId or destinationId");
        }

        try {
            const response = await axios.post(
                `/changeDestination/${ticketId}/${destinationId}`
            );
            return response;
        } catch (error) {
            console.error("Error in _changeDestination:", error);
            throw error;
        }
    }
}

export default PipeQ;
