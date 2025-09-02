<template>
  <div v-if="lastPage > 1" class="table__actions" style="justify-content:flex-end; margin-top:10px;">
    <button class="btn" :disabled="page<=1" @click="$emit('change', page-1)">Prev</button>
    <span class="meta">Page {{ page }} of {{ lastPage }}</span>
    <button class="btn" :disabled="page>=lastPage" @click="$emit('change', page+1)">Next</button>
  </div>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    page: { type: Number, required: true },
    lastPage: { type: Number, required: true },
  },
};
</script>