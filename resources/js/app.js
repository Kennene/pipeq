/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import "./echo";
import { createApp } from "vue";

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

document.addEventListener("DOMContentLoaded", () => {
    const langButton = document.getElementById("lang-button");
    const langMenu = document.getElementById("lang-menu");

    langButton.addEventListener("click", (event) => {
        event.stopPropagation();
        langMenu.classList.toggle("hidden");
    });

    // Kliknięcie poza menu ukryje je
    document.addEventListener("click", (event) => {
        if (
            !langMenu.contains(event.target) &&
            !langButton.contains(event.target)
        ) {
            langMenu.classList.add("hidden");
        }
    });
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */
