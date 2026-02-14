<script lang="ts" setup>
import { ref } from 'vue';

const dialogRef = ref<HTMLDialogElement | null>(null);
const showDialog = () => dialogRef.value?.showModal();
const closeDialog = () => dialogRef.value?.close();

defineExpose({
    show: showDialog,
    close: closeDialog
});

</script>

<template>
  <dialog ref="dialogRef">
    <button
      class="close-btn"
      type="button"
      aria-label="Zamknij"
      @click="closeDialog"
    >
      ×
    </button>

    <slot />
  </dialog>
</template>


<style scoped>
dialog[open] {
  position: fixed;
  inset: 0;
  margin: auto;

  border: none;
  border-radius: 12px;
  padding: 24px;
  width: min(90vw, 420px);

  background: #fff;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
}

dialog::backdrop {
  background: rgba(0, 0, 0, 0.5);
}

.close-btn {
  position: absolute;
  top: 12px;
  right: 12px;

  width: 32px;
  height: 32px;

  border: none;
  background: transparent;
  border-radius: 50%;

  font-size: 22px;
  line-height: 1;
  cursor: pointer;
  color: #6b7280;

  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  background: #f3f4f6;
  color: #111827;
}

.close-btn:focus-visible {
  outline: 2px solid #2563eb;
}

</style>

