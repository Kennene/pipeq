import "./echo";
import axios from "axios";

class PipeQ {
    constructor() {
        const channel = window.Echo.private("display");
        this.display = channel;
        this._listen();
    }

    _listen() {
        this.display.listen("TicketNew", (e) => {
            console.log(e);

            let ticket = e.message;
            window.dispatchEvent(
                new CustomEvent("ticket-new", { detail: ticket })
            );
        });
    }

    async _move(ticketId, workstationId, statusId = 3) {
        if (!ticketId) {
            console.error("Brak ticketId dla _move");
            throw new Error("Missing ticketId");
        }

        console.log(
            `Przenoszenie biletu ${ticketId} na stanowisko ${workstationId} ze statusem ${statusId}`
        );

        try {
            const response = await axios.post(
                `/move/${ticketId}/${workstationId}/${statusId}`
            );
            console.log("Odpowiedź na _move:", response);
            return response;
        } catch (error) {
            console.error(
                "Błąd podczas _move:",
                error.response?.data || error.message
            );
            throw error;
        }
    }

    _end(ticketId) {
        if (!ticketId) {
            console.error("Brak ticketId dla _end");
            return;
        }

        axios
            .post(`/end/${ticketId}`)
            .then((response) => {
                console.log("Odpowiedź na _end:", response);
                window.dispatchEvent(
                    new CustomEvent("ticket-end", { detail: { id: ticketId } })
                );
            })
            .catch((error) => {
                console.error(
                    "Błąd podczas _end:",
                    error.response?.data || error.message
                );
            });
    }
}

export default PipeQ;
