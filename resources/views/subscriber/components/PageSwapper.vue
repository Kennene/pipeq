<!-- <template>
    <div>
         Navigation Buttons 
        <div>
            <button @click="setPage('home')">Go to Home</button>
            <button @click="setPage('about')">Go to About</button>
            <button @click="setPage('contact')">Go to Contact</button>
        </div>

       Page Content
        <div v-if="currentPage === 'home'">
            <h1>Home Page</h1>
            <p>Welcome to the home page!</p>
        </div>

        <div v-if="currentPage === 'about'">
            <h1>About Page</h1>
            <p>Learn more about us here.</p>
        </div>

        <div v-if="currentPage === 'contact'">
            <h1>Contact Page</h1>
            <p>Get in touch with us!</p>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";

export default {
    setup() {
        const currentPage = ref("home"); // Tracks the current page

        const setPage = (page) => {
            currentPage.value = page; // Change the current page
        };

        return { currentPage, setPage };
    },
};
</script>

<style scoped>
button {
    font-size: 16px;
    margin: 5px;
    padding: 10px 20px;
    cursor: pointer;
}
h1 {
    color: #333;
}
</style> -->

<!-- ######################################################################### -->

<!-- <template>
    <div>
      <h1>Available Pages</h1>
      <ul>
        <li v-for="title in titles" :key="title">{{ title }}</li>
      </ul>
  
      < Navigation Buttons 
      <div>
        <button @click="setPage('home')">Go to Home</button>
        <button @click="setPage('about')">Go to About</button>
        <button @click="setPage('contact')">Go to Contact</button>
      </div>
  
       Page Content
      <div v-if="currentPage === 'home'">
        <h1>Home Page</h1>
        <p>Welcome to the home page!</p>
      </div>
  
      <div v-if="currentPage === 'about'">
        <h1>About Page</h1>
        <p>Learn more about us here.</p>
      </div>
  
      <div v-if="currentPage === 'contact'">
        <h1>Contact Page</h1>
        <p>Get in touch with us!</p>
      </div>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue';
  
  export default {
    props: {
      titles: {
        type: Array,
        required: true,
      },
    },
    setup() {
      const currentPage = ref('home');
  
      const setPage = (page) => {
        currentPage.value = page;
      };
  
      return { currentPage, setPage };
    },
  };
  </script> -->

<!-- ######################################################################### -->

<template>
    <div class="container">
        <!-- Navigation Buttons -->
        <div class="navigation">
            <button @click="setPage('home')">Go to Home</button>
            <button @click="setPage('about')">Go to About</button>
            <button @click="setPage('contact')">Go to Contact</button>
        </div>

        <!-- Animated Page Content -->
        <transition name="slide-up" mode="out-in">
            <div :key="currentPage" class="page">
                <div v-if="currentPage === 'home'" class="page-content home">
                    <h1>Home Page</h1>
                    <p>Welcome to the home page!</p>
                </div>

                <div v-if="currentPage === 'about'" class="page-content about">
                    <h1>About Page</h1>
                    <p>Learn more about us here.</p>
                </div>

                <div
                    v-if="currentPage === 'contact'"
                    class="page-content contact"
                >
                    <h1>Contact Page</h1>
                    <p>Get in touch with us!</p>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import { ref } from "vue";

export default {
    setup() {
        const currentPage = ref("home"); // Tracks the current page

        const setPage = (page) => {
            currentPage.value = page; // Change the current page
        };

        return { currentPage, setPage };
    },
};
</script>

<style scoped>
/* General Styling */
.container {
    text-align: center;
    font-family: Arial, sans-serif;
}

.navigation {
    margin-bottom: 20px;
}

button {
    font-size: 16px;
    margin: 5px;
    padding: 10px 20px;
    cursor: pointer;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    transition: background 0.3s;
}

button:hover {
    background: #0056b3;
}

/* Page Styling */
.page {
    position: relative;
    height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    margin: 20px;
}

.page-content {
    text-align: center;
}

.home {
    background: linear-gradient(to bottom right, #6dd5ed, #2193b0);
    color: white;
}

.about {
    background: linear-gradient(to bottom right, #ff7e5f, #feb47b);
    color: white;
}

.contact {
    background: linear-gradient(to bottom right, #43cea2, #185a9d);
    color: white;
}

/* Slide-Up Animation */
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.5s ease-in-out;
}

.slide-up-enter-from {
    transform: translateY(100%);
    opacity: 0;
}

.slide-up-leave-to {
    transform: translateY(-100%);
    opacity: 0;
}
</style>
