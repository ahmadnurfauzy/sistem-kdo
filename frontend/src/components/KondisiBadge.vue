<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: { type: String, required: true },
  short: { type: Boolean, default: false },
})

const MAP = {
  baik: { label: 'Baik', short: 'Baik' },
  rusak_ringan: { label: 'Rusak ringan', short: 'Ringan' },
  rusak_berat: { label: 'Rusak berat', short: 'Berat' },
}

const item = computed(() => MAP[props.value] ?? { label: props.value, short: props.value })
</script>

<template>
  <span class="badge rounded-pill badge-kondisi" :class="`badge-kondisi-${value}`">
    {{ short ? item.short : item.label }}
  </span>
</template>
