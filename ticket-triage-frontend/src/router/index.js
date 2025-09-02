import { createRouter, createWebHistory } from 'vue-router';
import Tickets from '../views/Tickets.vue';
import TicketDetail from '../views/TicketDetails.vue';
import Dashboard from '../views/Dashboard.vue';

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/tickets', name: 'tickets', component: Tickets },
  { path: '/tickets/:id', name: 'ticket-detail', component: TicketDetail, props: true },
  { path: '/dashboard', name: 'dashboard', component: Dashboard },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});