<template>
  <section class="ticket-details" v-if="ticket">
    <h2 class="ticket-details__title">Ticket Details</h2>

    <div class="ticket-details__form">
      <!-- Subject editable -->
      <div class="form-row">
        <label>Subject</label>
        <input
          type="text"
          v-model="ticket.subject"
          @blur="saveField('subject', ticket.subject)"
          placeholder="Enter subject"
        />
      </div>

      <!-- Body (read-only, but could make editable if required) -->
      <div class="form-row">
        <label>Body</label>
        <div class="readonly">{{ ticket.body }}</div>
      </div>

      <!-- Status dropdown -->
      <div class="form-row">
        <label>Status</label>
        <select v-model="ticket.status" @change="saveField('status', ticket.status)">
          <option value="open">Open</option>
          <option value="in_progress">In Progress</option>
          <option value="closed">Closed</option>
        </select>
      </div>

      <!-- Category dropdown -->
      <div class="form-row">
        <label>Category</label>
        <select v-model="ticket.category" @change="saveField('category', ticket.category)">
          <option value="">Uncategorized</option>
          <option value="billing">Billing</option>
          <option value="technical">Technical</option>
          <option value="general">General</option>
        </select>
      </div>

      <!-- Confidence -->
      <div class="form-row">
        <label>Confidence</label>
        <div>{{ formatConfidence(ticket.confidence) }}</div>
      </div>

      <!-- Explanation -->
      <div class="form-row">
        <label>Explanation</label>
        <div>{{ ticket.explanation || '—' }}</div>
      </div>

      <!-- Note textarea -->
      <div class="form-row">
        <label>Note</label>
        <textarea
          v-model="ticket.note"
          @blur="saveField('note', ticket.note)"
          placeholder="Add internal note…"
        />
      </div>
    </div>

    <div class="ticket-details__actions">
   
      <button class="btn" @click="$router.push('/tickets')">Back to Tickets</button>
         <button class="btn" @click="classify" :disabled="running">
        <span v-if="running">Classifying…</span>
        <span v-else>Run Classification</span>
      </button>
    </div>
  </section>

  <div v-else>Loading ticket…</div>
</template>

<script>
import axios from "axios";
const API = "http://127.0.0.1:8000";

export default {
  name: "TicketDetails",
  props: ["id"],
  data() {
    return {
      ticket: null,
      loading: false,
      running: false 
    };
  },
  async mounted() {
    await this.fetchTicket();
  },
  methods: {
    async fetchTicket() {
      try {
        const { data } = await axios.get(`${API}/api/tickets/${this.id}`);
        this.ticket = data;
      } catch (e) {
        console.error(e);
        alert("Could not load ticket.");
      }
    },
    async saveField(field, value) {
      try {
        await axios.patch(`${API}/api/tickets/${this.id}`, { [field]: value });
      } catch (e) {
        console.error(e);
        alert(`Could not update ${field}.`);
      }
    },
    async classify() { 
  try {
    this.running = true;
    await axios.post(`${API}/api/tickets/${this.ticket.id}/classify`); 
    await this.fetchTicket();
  } catch (e) {
    console.error(e);
    alert("Classification failed.");
  } finally {
    this.running = false;
  }
    },
    formatConfidence(val) {
      const n = Number(val);
      if (Number.isNaN(n)) return "—";
      return n <= 1 ? n.toFixed(2) : (n / 100).toFixed(2);
    },
  },
};
</script>

<style scoped>
.ticket-details {
  padding: 20px;
  max-width: 700px;
  margin: 0 auto;
}

.ticket-details__title {
  margin-bottom: 1.5rem;
}

.ticket-details__form {
  display: grid;
  gap: 1rem;
}

.form-row {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.form-row label {
  flex: 0 0 120px;
  font-weight: 600;
  text-align: right;
}

.form-row input,
.form-row select,
.form-row textarea,
.form-row .readonly {
  flex: 1;
  padding: 6px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.readonly {
  background: #f9f9f9;
}

textarea {
  min-height: 100px;
  resize: vertical;
}

.ticket-details__actions {
  margin-top: 20px;
  display: flex;
  gap: 10px;
}

button {
  border: 1px solid #ccc;
  padding: 6px 12px;
  cursor: pointer;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn {
  background: #f2f2f2;
}
</style>