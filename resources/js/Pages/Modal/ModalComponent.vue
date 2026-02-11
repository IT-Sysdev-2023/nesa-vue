<template>
  <transition name="modal">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-md"
    >
      <div class="rounded-lg p-6">
        <h2 class="mb-4 text-center text-white text-xl font-semibold">
          <slot name="title">{{ title }}</slot>
        </h2>

        <div class="mb-6 text-gray-600">
          <slot />
        </div>

        <div class="flex justify-end gap-2">
          <button
            @click="closeModalButton"
            class="px-4 py-2 rounded bg-gray-200"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>




<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    required: true,
  }
})

const emit = defineEmits(['closeModal']);

const closeModalButton = () => {
  emit('closeModal');
}
</script>
<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: backdrop-filter 0.3s ease,
              background-color 0.3s ease,
              opacity 0.25s ease,
              transform 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  backdrop-filter: blur(0px);
  background-color: rgba(0, 0, 0, 0);
  opacity: 0;
  transform: scale(0.9);
}

.modal-enter-to,
.modal-leave-from {
  backdrop-filter: blur(12px);
  background-color: rgba(0, 0, 0, 0.5);
  opacity: 1;
  transform: scale(1);
}
</style>
