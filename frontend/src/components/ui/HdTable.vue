<script setup>
defineProps({
  columns: {
    type: Array,
    required: true,
    // each column: { key: string, label: string }
  },
  items: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false
  }
});
</script>

<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-subtle">
      <thead class="bg-surface">
        <tr>
          <th 
            v-for="col in columns" 
            :key="col.key" 
            scope="col" 
            class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider font-brand"
          >
            {{ col.label }}
          </th>
        </tr>
      </thead>
      <tbody class="bg-surface divide-y divide-subtle font-body">
        <tr v-if="loading">
          <td
            :colspan="columns.length"
            class="px-6 py-4 text-center text-sm text-muted"
          >
            Cargando...
          </td>
        </tr>
        <tr v-else-if="items.length === 0">
          <td
            :colspan="columns.length"
            class="px-6 py-4 text-center text-sm text-muted"
          >
            No hay datos disponibles.
          </td>
        </tr>
        <tr 
          v-for="(item, index) in items" 
          v-else 
          :key="item.id || index" 
          class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
        >
          <td 
            v-for="col in columns" 
            :key="col.key" 
            class="px-6 py-4 whitespace-nowrap text-sm text-content"
          >
            <slot
              :name="'cell(' + col.key + ')'"
              :item="item"
              :value="item[col.key]"
            >
              {{ item[col.key] }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
