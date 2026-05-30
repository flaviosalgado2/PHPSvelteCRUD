<script>
  import { _ } from 'svelte-i18n';
  import Icon from '@iconify/svelte';

  let {
    isOpen = false,
    title = '',
    message = '',
    itemName = '',
    confirmText = '',
    cancelText = '',
    onConfirm = () => {},
    onCancel = () => {},
  } = $props();

  function handleConfirm() {
    onConfirm();
  }

  function handleCancel() {
    onCancel();
  }

  function handleBackdropClick(event) {
    if (event.target === event.currentTarget) {
      handleCancel();
    }
  }

  function handleKeydown(event) {
    if (event.key === 'Escape') handleCancel();
  }
</script>

<svelte:window onkeydown={handleKeydown} />

{#if isOpen}
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    onclick={handleBackdropClick}
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title"
    tabindex="-1"
  >
    <div class="card bg-base-100 shadow-2xl max-w-md w-full mx-4 rounded-2xl">
      <!-- Header -->
      <div class="card-body p-6">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 bg-error/15 rounded-full flex items-center justify-center flex-shrink-0">
            <Icon icon="heroicons:exclamation-triangle" class="w-6 h-6 text-error" />
          </div>
          <div class="flex-1">
            <h3 id="modal-title" class="text-lg font-bold text-base-content">
              {title || $_('deleteConfirmation.title')}
            </h3>
            <p class="mt-1 text-sm text-base-content/70">
              {message || $_('deleteConfirmation.message')}
              {#if itemName}
                <span class="font-semibold text-base-content">"{itemName}"</span>
              {/if}
            </p>
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button type="button" class="btn btn-ghost" onclick={handleCancel}>
            {cancelText || $_('common.cancel')}
          </button>
          <button type="button" class="btn btn-error" onclick={handleConfirm}>
            <Icon icon="heroicons:trash" class="w-4 h-4" />
            {confirmText || $_('common.delete')}
          </button>
        </div>
      </div>
    </div>
  </div>
{/if}
