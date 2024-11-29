import { createApp } from "vue";
import { createPinia } from "pinia";
import Coordinator from "@/components/Coordinator.vue";
const app = createApp({
    components: {
        Coordinator,
    },
});

const pinia = createPinia();

app.use(pinia);
app.mount("#app");
