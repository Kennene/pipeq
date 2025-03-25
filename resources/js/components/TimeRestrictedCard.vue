<template>
  <div class="container text-center mt-12">
    <div class="card p-4 shadow-sm rounded-md">

      <span id="time-restricted-title" class="h1">{{ translations.title }}</span>
      
      <hr />

      <p class="large-text mt-2">
        {{ translations.message }}

        <div class="mt-4 bg-gray-100 p-4 rounded-xl shadow-sm">
          <p class="text-lg font-semibold mb-2">
            {{ translations.opens }}
          </p>
          
          <ul class="space-y-2">
            <li
              v-for="destination in destinations"
              :key="destination.id"
              class="destination-opens bg-white p-3 rounded-md shadow-lg flex items-center justify-between border border-gray-200"
            >
              <span class="font-medium"> {{ destination.name }} </span>
              <span class="opens font-bold text-lg"> {{ formatTimestamp(destination.opens) }} </span>
            </li>
          </ul>
        </div>
      </p>

      <div class="mt-4">
        <p>{{ translations.mail }}</p>
        <a href="mailto:email@example.com" class="mt-2 bg-gray-50 p-2 rounded-md border border-gray-200 inline-block">📧 email@example.com</a>
      </div>

      <p class="large-text mt-4 text-sm text-gray-500">
        {{ translations.sorry1 }} <br />
        {{ translations.sorry2 }} <br />
        <span class="italic">{{ translations.company }}</span>
      </p>

    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  destinations: {
    type: Array,
    required: true
  },
  translations: {
    type: Object,
    required: true
  }
});

function formatTimestamp(timestamp) {
  if (!timestamp) return '—';
  const date = new Date(timestamp * 1000);
  return date.toLocaleString(props.translations.locale || 'pl-PL', {
    weekday: 'long',
    hour: '2-digit',
    minute: '2-digit'
  });
}
</script>

<style scoped>
.card {
  border-radius: 12px;
  background-color: white;
  padding: 2rem;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
  max-width: 720px;
  margin: 0 auto;
}

.opens {
  color: var(--green);
}

#time-restricted-title {
  font-weight: 700;
  font-size: 2rem;
  color: var(--red);
}

.large-text {
  font-size: 1.1rem;
}

a {
  color: var(--primary);
}

hr {
  margin-top: 1rem;
  margin-bottom: 1rem;
  border: none;
  border-top: 1px solid var(--black);
}
</style>
