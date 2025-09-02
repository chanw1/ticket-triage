<template>
  <div class="tickets">
<div class="tickets__header">
  <h1>Tickets</h1>
  <div class="tickets__actions">
    <button class="btn btn--primary" @click="showNew = true">New Ticket</button>
    <button class="btn" @click="exportCsv">Export CSV</button>
  </div>
</div>
    <!-- Filters -->
    <div class="filters">
      <input
        v-model="search"
        @input="fetchTickets()"
        placeholder="Search tickets..."
      />

      <select v-model="status" @change="fetchTickets()">
        <option value="">All Statuses</option>
        <option value="open">Open</option>
        <option value="in_progress">In Progress</option>
        <option value="closed">Closed</option>
      </select>

      <select v-model="category" @change="fetchTickets()">
        <option value="">All Categories</option>
        <option value="billing">Billing</option>
        <option value="technical">Technical</option>
        <option value="general">General</option>
      </select>
    </div>

    <!-- Ticket List -->
    <table class="ticket-table">
      <thead>
        <tr>
          <th>Subject</th>
          <th>Status</th>
          <th>Category</th>
          <th>Confidence</th>
          <th>Note</th>
          <th style="width: 1%">Actions</th>
        </tr>
      </thead>
      <tbody>
  <tr
    v-for="ticket in tickets"
    :key="ticket.id"
    class="ticket-table__row"
  >
    <!-- Subject (clickable to details) -->
    <td class="clickable" @click="goTo(ticket.id)">
      {{ ticket.subject }}
    </td>

    <!-- Status dropdown -->
    <td>
      <select v-model="ticket.status" @change="updateField(ticket.id, 'status', ticket.status)">
        <option value="open">Open</option>
        <option value="in_progress">In Progress</option>
        <option value="closed">Closed</option>
      </select>
    </td>

    <!-- Category dropdown -->
    <td>
      <select v-model="ticket.category" @change="updateField(ticket.id, 'category', ticket.category)">
        <option value="">Uncategorized</option>
        <option value="billing">Billing</option>
        <option value="technical">Technical</option>
        <option value="general">General</option>
      </select>
    </td>

    <!-- Confidence with tooltip for explanation -->
    <td :title="ticket.explanation || ''">
      <span v-if="ticket.confidence !== null && ticket.confidence !== undefined">
        {{ formatConfidence(ticket.confidence) }}
      </span>
      <span v-else>—</span>
    </td>

    <!-- Note badge if present -->
    <td>
      <span v-if="ticket.note" class="badge">Note</span>
      <span v-else>—</span>
    </td>

    <!-- Classify button -->
    <td>
      <button 
        @click="classifyTicket(ticket.id)" 
        :disabled="loadingId === ticket.id"
      >
        <span v-if="loadingId === ticket.id">Classifying...</span>
        <span v-else>Classify</span>
      </button>
    </td>
  </tr>
</tbody>
    </table>

    <!-- Pagination Info + Controls -->
    <div class="pagination-bar" v-if="meta">
      <div>Page {{ meta.current_page }} of {{ meta.last_page }}</div>
      <div class="pagination-actions">
        <button class="btn btn--sm" :disabled="meta.current_page <= 1" @click="changePage(meta.current_page - 1)">
          ◀ Prev
        </button>
        <button class="btn btn--sm" :disabled="meta.current_page >= meta.last_page" @click="changePage(meta.current_page + 1)">
          Next ▶
        </button>
      </div>
    </div>

    <!-- New Ticket Modal -->
    <div class="modal" v-if="showNew">
      <div class="modal__backdrop" @click="closeNew()"></div>
      <div class="modal__dialog">
        <div class="modal__header">
          <h3>New Ticket</h3>
          <button class="modal__close" @click="closeNew()">×</button>
        </div>
        <form class="modal__body" @submit.prevent="createTicket">
          <label class="form__label">Subject</label>
          <input class="form__input" v-model.trim="form.subject" required placeholder="Short summary" />

          <label class="form__label">Body</label>
          <textarea class="form__textarea" v-model.trim="form.body" required placeholder="Describe the issue…"></textarea>

          <div class="modal__footer">
            <button type="button" class="btn" @click="closeNew()">Cancel</button>
            <button type="submit" class="btn btn--primary" :disabled="creating">
              <span v-if="creating">Saving…</span>
              <span v-else>Create</span>
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script>
import axios from "axios";

const API = "http://127.0.0.1:8000";

export default {
  name: "Tickets",
data() {
  return {
    tickets: [],
    meta: null,
    search: "",
    status: "",
    category: "",
    loadingId: null,    
    // new ticket modal
    showNew: false,
    form: { subject: "", body: "" },
    creating: false,
  };
},
  methods: {
    async fetchTickets(page = 1) {
      try {
        const response = await axios.get(`${API}/api/tickets`, {
          params: {
            search: this.search,
            status: this.status,
            category: this.category,
            page,
          },
        });
        this.tickets = response.data.data;
        this.meta = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
        };
      } catch (error) {
        console.error("Error fetching tickets:", error);
        alert("Failed to load tickets.");
      }
    },
    changePage(page) {
      this.fetchTickets(page);
    },
    goTo(id) {
      // Navigate to /tickets/:id (assumes router has that route)
      if (this.$router) {
        this.$router.push(`/tickets/${id}`);
      }
    },
    formatConfidence(value) {
      const n = Number(value);
      if (Number.isNaN(n)) return "—";
      // value may already be 0–1 or 0–100; we’ll standardize to 0–1 with 2 decimals
      return n <= 1 ? n.toFixed(2) : (n / 100).toFixed(2);
    },

async exportCsv() {
  try {
    const response = await axios.get("http://127.0.0.1:8000/api/tickets/export/csv", {
      responseType: 'blob'
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'tickets.csv');
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error("Export failed:", error);
    alert("Failed to export CSV.");
  }
},

    async updateField(id, field, value) {
  try {
    await axios.patch(`${API}/api/tickets/${id}`, { [field]: value });
  } catch (error) {
    console.error(`Error updating ${field}:`, error);
    alert(`Failed to update ${field}.`);
  }
},


async classifyTicket(id) {
  this.loadingId = id; // lock this row
  try {
    await axios.post(`http://127.0.0.1:8000/api/tickets/${id}/classify`);
    await this.fetchTickets(this.meta?.current_page || 1); // stay on same page
  } catch (error) {
    console.error("Error classifying ticket:", error);
    alert("Classification failed.");
  } finally {
    this.loadingId = null; // 
  }
    },
    // modal helpers
    closeNew() {
      this.showNew = false;
      this.form.subject = "";
      this.form.body = "";
    },
    async createTicket() {
      if (!this.form.subject || !this.form.body) return;
      try {
        this.creating = true;
        await axios.post(`${API}/api/tickets`, {
          subject: this.form.subject,
          body: this.form.body,
        });
        this.closeNew();
        await this.fetchTickets(1);
      } catch (e) {
        console.error(e);
        alert("Could not create ticket.");
      } finally {
        this.creating = false;
      }
    },
  },
  mounted() {
    this.fetchTickets();
  },
};
</script>

<style scoped>
.tickets { padding: 20px; }

/* Header */
.tickets__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Filters */
.filters {
  display: flex;
  gap: 1rem;
  margin: 1rem 0;
}

/* Table */
.ticket-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}
.ticket-table th,
.ticket-table td {
  border: 1px solid #ddd;
  padding: 8px;
}
.ticket-table__row:hover {
  background: #f8f8f8;
}
.clickable { cursor: pointer; text-decoration: underline; }

/* Pagination */
.pagination-bar {
  margin-top: 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.pagination-actions { display: flex; gap: 8px; }

/* Buttons */
.btn {
  border: 1px solid #ccc;
  background: #fff;
  padding: 6px 10px;
  cursor: pointer;
}
.btn:disabled { opacity: 0.6; cursor: not-allowed; }
.btn--primary {
  background: #111;
  color: #fff;
  border-color: #111;
}
.btn--sm { padding: 4px 8px; font-size: 0.9rem; }

/* Badge */
.badge {
  display: inline-block;
  padding: 2px 6px;
  font-size: 0.75rem;
  border-radius: 4px;
  background: #eee;
}

/* Modal (simple, framework-free) */
.modal { position: fixed; inset: 0; z-index: 1000; }
.modal__backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.4); }
.modal__dialog {
  position: relative;
  max-width: 520px;
  margin: 10vh auto;
  background: #fff;
  color: #000;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 12px 30px rgba(0,0,0,0.2);
}
.modal__header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px 16px; border-bottom: 1px solid #eee;
}
.modal__close {
  background: transparent; border: none; font-size: 20px; cursor: pointer;
}
.modal__body { padding: 16px; display: grid; gap: 10px; }
.modal__footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 8px; }

/* Form */
.form__label { font-weight: 600; }
.form__input, .form__textarea {
  width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;
}
.form__textarea { min-height: 120px; resize: vertical; }
</style>