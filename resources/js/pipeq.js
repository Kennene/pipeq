import "./echo";
import axios from "axios";

class PipeQ {
    constructor() {
        this.channel = null;
        this.onTicketUpdate = null;
    }

    _listen(channel) {
        if (!channel) return;

        this.channel = channel;
        const registerChannel = window.Echo.channel(`register.${this.channel}`);

        registerChannel.listen("UpdateUserAboutHisTicket", (message) => {
            if (
                this.onTicketUpdate &&
                typeof this.onTicketUpdate === "function"
            ) {
                this.onTicketUpdate(message.ticket);
            }
        });

        registerChannel.subscribed(() => {
            axios.post(`/status/${this.channel}`).catch((error) => {
                console.error("Error requesting status update:", error);
            });
        });
    }

    async _register(destination_id) {
        try {
            const response = await axios.post(`/register/${destination_id}`);
            if (response.data.channel) {
                this._listen(response.data.channel);
            } else {
                console.error("No channel received");
            }
        } catch (error) {
            if (
                error.response &&
                error.response.data &&
                error.response.data.error
            ) {
                console.error(error.response.data.error);
            } else {
                console.error("Error in _register:", error);
            }
        }
    }

    async _clear() {
        try {
            const response = await axios.post(window.routes.clear);
            console.log(response.data.message);
        } catch (error) {
            console.log("Error in _clear:", error);
        }
    }

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

    async _endAll(destinationId = null) {
        try {
            const url = destinationId ? `/endAll/${destinationId}` : `/endAll`;
            const response = await axios.post(url);
            console.log(response.data.message);
            return response.data;
        } catch (error) {
            console.error("Error in _endAll:", error);
            throw error;
        }
    }
    async _endByUser(ticketToken = null) {
        try {
            const url = ticketToken
                ? `/endByUser/${ticketToken}`
                : `/endByUser`;

            const response = await axios.post(url);
            console.log(response.data);
            return response;
        } catch (error) {
            console.error("Error in _endByUser:", error);
            throw error;
        }
    }
}

export default PipeQ;
