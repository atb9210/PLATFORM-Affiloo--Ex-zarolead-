<template>
  <div class="card-container">

    <div class="card">
      <h2 class="mb-2">{{ title }}</h2>
      <h3>{{ body }}</h3>
    </div>

  </div>
</template>

<script>
export default {
  name: 'stats-card',
  props: {
    title: { type: [String], required: true },
    body:  { type: [String], required: true },
  },
  data () {
    return {
    }
  },
}
</script>

<style scoped>
.card-container {
  padding: 0.5rem;
}
.card {
  padding: 1.0rem;
  background: #ffffff;
  border-radius: 0.5rem;
  box-shadow: 0px 4px 25px 0px rgb(0 0 0 / 10%);
}
</style>
