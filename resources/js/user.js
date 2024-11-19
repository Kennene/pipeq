import { createApp } from "vue";
import PageSwapper from "../../resources/views/subscriber/components/PageSwapper.vue";

const app = createApp({});
app.component("page-swapper", PageSwapper);
app.mount("#app");
