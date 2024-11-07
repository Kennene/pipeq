import { createApp } from "vue";
import Coordinator from "@/components/Coordinator.vue";
const app = createApp({
    components: {
        Coordinator,
    },
});

app.mount("#app");
