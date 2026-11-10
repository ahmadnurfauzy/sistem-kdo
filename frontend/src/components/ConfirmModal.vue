<script setup>
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
    <div class="modal d-block" tabindex="-1" role="dialog" aria-modal="true">
      <div class="modal-dialog modal-dialog-centered px-2">
        <div class="modal-content">
          <div class="modal-body text-center p-4">
            <div class="confirm-icon"><i class="bi bi-trash3"></i></div>
            <h5 class="mb-2">{{ title }}</h5>
            <p class="text-secondary mb-4">{{ message }}</p>
            <div class="d-flex gap-2">
              <button class="btn btn-soft flex-fill" :disabled="busy" @click="$emit('cancel')">Batal</button>
              <button class="btn btn-danger flex-fill" :disabled="busy" @click="$emit('confirm')">
                <span v-if="busy" class="spinner-border spinner-border-sm me-1"></span>
                {{ confirmLabel }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
