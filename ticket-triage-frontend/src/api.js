// src/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api'  
});

export default {
  getStats: () => api.get('/stats'),
  getTickets: (params) => api.get('/tickets', { params }),
  getTicket: (id) => api.get(`/tickets/${id}`),
  createTicket: (data) => api.post('/tickets', data),
  updateTicket: (id, data) => api.patch(`/tickets/${id}`, data),
  classifyTicket: (id) => api.post(`/tickets/${id}/classify`)
};