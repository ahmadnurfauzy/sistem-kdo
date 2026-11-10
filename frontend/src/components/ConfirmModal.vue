<script setup>
/**
 * Modal konfirmasi sederhana (dikelola Vue, cukup class CSS Bootstrap).
 */
defineProps({
  visible: { type: Boolean, default: false },
  title: { type: String, default: 'Konfirmasi' },
  message: { type: String, default: 'Yakin melanjutkan aksi ini?' },
  confirmLabel: { type: String, default: 'Ya, lanjutkan' },
  busy: { type: Boolean, default: false },
})

defineEmits(['confirm', 'cancel'])
</script>

<template>
  <div v-if="visible">
    <div class="modal-backdrop-custom" @click="$emit('cancel')"></div>
    <div class="modal d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ title }}</h5>
            <button type="button" class="btn-close" @click="$emit('cancel')"></button>
          </div>
          <div class="modal-body">
            <p class="mb-0">{{ message }}</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" :disabled="busy" @click="$emit('cancel')">
              Batal
            </button>
            <button class="btn btn-danger" :disabled="busy" @click="$emit('confirm')">
              <span v-if="busy" class="spinner-border spinner-border-sm me-1"></span>
              {{ confirmLabel }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
