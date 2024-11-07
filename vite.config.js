import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/user.css",
                "resources/css/coordinator.css",
                "resources/css/administrator.css",
                "resources/css/display.css",
                "resources/css/topbar.css",
                "resources/js/coordinator.js",
                "resources/css/tickets-dashboard.css",
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
        },
    },
});
