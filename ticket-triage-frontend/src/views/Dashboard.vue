<template>
  <section class="panel">
    <h2 class="panel__title">Dashboard</h2>

    <div class="grid grid--3">
      <!-- Status Table -->
      <div class="panel">
        <h3 class="panel__title">By Status</h3>
        <table class="stats-table">
          <thead>
            <tr>
              <th>Status</th>
              <th>Count</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(v,k) in stats.status_counts" :key="k">
              <td>{{ k }}</td>
              <td><strong>{{ v }}</strong></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Category Table -->
      <div class="panel">
        <h3 class="panel__title">By Category</h3>
        <table class="stats-table" v-if="Object.keys(stats.category_counts || {}).length">
          <thead>
            <tr>
              <th>Category</th>
              <th>Count</th>
            </tr>
          </thead>
          <tbody>
           <tr v-for="(v,k) in stats.category_counts" :key="k">
              <td>{{ formatLabel(k) }}</td>
              <td><strong>{{ v }}</strong></td>
            </tr>
          </tbody>
        </table>
        <div v-else class="meta">No categories yet</div>
      </div>

      <!-- Chart -->
      <div class="panel">
        <h3 class="panel__title">Category Chart</h3>
        <canvas id="catChart"></canvas>
      </div>
    </div>
  </section>
</template>

<script>
import api from '../api';
import { Chart } from 'chart.js/auto';

export default {
  name: 'Dashboard',
  data: () => ({ stats: { status_counts:{}, category_counts:{} }, chart: null }),
  async mounted() {
    await this.load();
    this.draw();
  },
  methods: {
    async load() {
      const { data } = await api.getStats();
      this.stats = data;
    },
    draw() {
      const ctx = document.getElementById('catChart');
      if (this.chart) { this.chart.destroy(); }
      const labels = Object.keys(this.stats.category_counts || {}).map(this.formatLabel);
      const values = Object.values(this.stats.category_counts || {});
      this.chart = new Chart(ctx, {
        type: 'pie',
        data: { labels, datasets: [{ data: values }] },
        options: { responsive: true }
      });
    },
    formatLabel(key) {
      if (!key) return 'Uncategorized';
      if (key.toLowerCase() === 'uncategorized') return 'Uncategorized';
      return key.charAt(0).toUpperCase() + key.slice(1);
    }
  },
  watch: {
    stats() { this.$nextTick(() => this.draw()); }
  }
};
</script>

<style scoped>
.stats-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 0.5rem;
}
.stats-table th,
.stats-table td {
  border: 1px solid #ddd;
  padding: 6px 10px;
  text-align: left;
}
.stats-table th {
  background: #f5f5f5;
}
.meta {
  color: #666;
  font-style: italic;
  margin-top: 0.5rem;
}
</style>