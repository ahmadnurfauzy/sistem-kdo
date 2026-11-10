<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: [Number, String], default: 0 },
  icon: { type: String, default: 'bi-circle' },
  tone: { type: String, default: 'blue' }, // blue | green | amber | red | teal | violet
})
</script>

<template>
  <div class="stat">
    <div class="stat-icon" :class="`tone-${tone}`"><i :class="['bi', icon]"></i></div>
    <div>
      <div class="stat-value">{{ value }}</div>
      <div class="stat-label">{{ label }}</div>
    </div>
  </div>
</template>
