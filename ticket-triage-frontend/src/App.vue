<template>
  <div class="app">
    <nav class="app__nav">
      <!-- Logo -->
      <div class="app__logo">Ticket Triage</div>

      <!-- Nav Menu -->
      <div class="app__menu">
        <router-link to="/dashboard">Dashboard</router-link>
        <router-link to="/tickets">Tickets</router-link>
      </div>
    </nav>

    <main class="app__content">
      <router-view />
    </main>
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>

<style scoped>
.app__nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 20px;
  background: #111;
  color: #fff;
}

.app__logo {
  font-weight: bold;
  font-size: 1.2rem;
}

.app__menu {
  display: flex;
  gap: 20px;
}

.app__menu a {
  color: #fff;
  text-decoration: none;
}

.app__menu a.router-link-exact-active {
  text-decoration: underline;
}
</style>