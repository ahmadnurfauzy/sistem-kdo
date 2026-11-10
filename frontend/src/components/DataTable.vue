<script setup>
import { computed } from 'vue'

const props = defineProps({
  columns: { type: Array, required: true }, // [{ key, label, hideBelow?, class? }]
  rows: { type: Array, default: () => [] },
  loading: Boolean,
  meta: { type: Object, default: null },
  rowKey: { type: String, default: 'id' },
  clickable: Boolean,
  emptyIcon: { type: String, default: 'bi-inbox' },
  emptyTitle: { type: String, default: 'Belum ada data' },
  emptyText: { type: String, default: 'Data akan tampil di sini setelah ditambahkan.' },
})

const emit = defineEmits(['page-change', 'row-click'])

const pages = computed(() => {
  if (!props.meta) return []
  const { current_page: cur, last_page: last } = props.meta
  const start = Math.max(1, Math.min(cur - 2, last - 4))
  const end = Math.min(last, start + 4)
  const list = []
  for (let p = start; p <= end; p++) list.push(p)
  return list
})

const hideClass = (col) => (col.hideBelow ? `d-none d-${col.hideBelow}-table-cell` : '')

function go(page) {
  if (!props.meta || page < 1 || page > props.meta.last_page || page === props.meta.current_page) return
  emit('page-change', page)
}
</script>

<template>
  <div>
    <!-- DESKTOP: tabel -->
    <div class="dt-card d-none d-md-block">
      <div class="table-responsive">
        <table class="table dt-table align-middle mb-0">
          <thead>
            <tr>
              <th v-for="col in columns" :key="col.key" :class="[hideClass(col), col.class]">{{ col.label }}</th>
              <th v-if="$slots.actions" style="width: 130px"></th>
            </tr>
          </thead>
          <tbody>
            <template v-if="loading">
              <tr v-for="n in 5" :key="'sk' + n">
                <td v-for="col in columns" :key="col.key" :class="hideClass(col)"><span class="skel"></span></td>
                <td v-if="$slots.actions"></td>
              </tr>
            </template>
            <template v-else-if="rows.length">
              <tr
                v-for="row in rows"
                :key="row[rowKey]"
                :class="{ 'dt-click': clickable }"
                @click="clickable && emit('row-click', row)"
              >
                <td v-for="col in columns" :key="col.key" :class="[hideClass(col), col.class]">
                  <slot :name="`cell-${col.key}`" :row="row">{{ row[col.key] }}</slot>
                </td>
                <td v-if="$slots.actions" class="text-end" @click.stop>
                  <slot name="actions" :row="row" />
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div v-if="!loading && !rows.length" class="dt-empty">
        <i :class="['bi', emptyIcon]"></i>
        <h6>{{ emptyTitle }}</h6>
        <p>{{ emptyText }}</p>
        <slot name="empty" />
      </div>

      <div v-if="meta && rows.length && meta.last_page > 1" class="dt-footer">
        <span class="small text-secondary">
          Menampilkan {{ meta.from ?? '' }} sampai {{ meta.to ?? '' }} dari {{ meta.total }}
        </span>
        <div class="btn-group btn-group-sm">
          <button class="btn btn-outline-secondary" :disabled="meta.current_page === 1" aria-label="Halaman sebelumnya" @click="go(meta.current_page - 1)">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button
            v-for="p in pages"
            :key="p"
            class="btn"
            :class="p === meta.current_page ? 'btn-primary' : 'btn-outline-secondary'"
            @click="go(p)"
          >{{ p }}</button>
          <button class="btn btn-outline-secondary" :disabled="meta.current_page === meta.last_page" aria-label="Halaman berikutnya" @click="go(meta.current_page + 1)">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- MOBILE: card list -->
    <div class="d-md-none">
      <template v-if="loading">
        <div v-for="n in 4" :key="'msk' + n" class="dt-mcard">
          <span class="skel mb-2" style="width: 55%"></span>
          <span class="skel mb-2" style="width: 80%"></span>
          <span class="skel" style="width: 40%"></span>
        </div>
      </template>

      <template v-else-if="rows.length">
        <div
          v-for="row in rows"
          :key="row[rowKey]"
          class="dt-mcard"
          :class="{ 'dt-click': clickable }"
          @click="clickable && emit('row-click', row)"
        >
          <slot name="card" :row="row" />
          <div v-if="$slots.actions" class="dt-mcard-actions" @click.stop>
            <slot name="actions" :row="row" />
          </div>
        </div>
      </template>

      <div v-else class="dt-card dt-empty">
        <i :class="['bi', emptyIcon]"></i>
        <h6>{{ emptyTitle }}</h6>
        <p>{{ emptyText }}</p>
        <slot name="empty" />
      </div>

      <div v-if="meta && rows.length && meta.last_page > 1" class="d-flex justify-content-between align-items-center mt-3">
        <button class="btn btn-soft btn-sm" :disabled="meta.current_page === 1" @click="go(meta.current_page - 1)">
          <i class="bi bi-chevron-left"></i> Sebelumnya
        </button>
        <span class="small text-secondary">{{ meta.current_page }} / {{ meta.last_page }}</span>
        <button class="btn btn-soft btn-sm" :disabled="meta.current_page === meta.last_page" @click="go(meta.current_page + 1)">
          Berikutnya <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dt-card {
  background: #fff;
  border: 1px solid var(--kdo-border);
  border-radius: var(--kdo-radius);
  box-shadow: var(--kdo-shadow);
  overflow: hidden;
}
.dt-table { --bs-table-bg: transparent; }
.dt-table thead th { background: #fafbfe; }
.dt-table tbody tr:last-child td { border-bottom: 0; }
.dt-click { cursor: pointer; }

.dt-footer {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0.75rem 1rem; border-top: 1px solid var(--kdo-border);
}

.dt-empty { text-align: center; padding: 3rem 1rem; }
.dt-empty i { font-size: 2.5rem; color: var(--kdo-blue); opacity: 0.35; }
.dt-empty h6 { margin: 0.75rem 0 0.25rem; font-weight: 700; }
.dt-empty p { color: var(--kdo-muted); font-size: 0.875rem; margin-bottom: 1rem; }

.dt-mcard {
  background: #fff;
  border: 1px solid var(--kdo-border);
  border-radius: var(--kdo-radius);
  box-shadow: var(--kdo-shadow);
  padding: 0.95rem;
  margin-bottom: 0.7rem;
}
.dt-mcard-actions {
  display: flex; justify-content: flex-end; gap: 0.25rem;
  margin: 0.75rem -0.35rem -0.35rem; padding-top: 0.5rem;
  border-top: 1px solid var(--kdo-border);
}
</style>
