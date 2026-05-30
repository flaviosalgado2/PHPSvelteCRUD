<script>
  import { toast } from '$lib/stores/toast.js';
  import Icon from '@iconify/svelte';

  const icons = {
    success: 'heroicons:check-circle',
    error: 'heroicons:x-circle',
    info: 'heroicons:information-circle',
    warning: 'heroicons:exclamation-triangle',
  };

  const alertClass = {
    success: 'alert-success',
    error: 'alert-error',
    info: 'alert-info',
    warning: 'alert-warning',
  };
</script>

<div class="toast toast-end toast-top z-[9999] max-w-sm w-full pr-4 pt-4 gap-2">
  {#each $toast as t (t.id)}
    <div
      class="alert {alertClass[t.type]} shadow-lg rounded-xl flex items-center gap-3 animate-in slide-in-from-right-5 duration-300"
      role="alert"
    >
      <Icon icon={icons[t.type]} class="w-6 h-6 flex-shrink-0" />
      <span class="text-sm font-medium flex-1">{t.message}</span>
      <button
        type="button"
        class="btn btn-ghost btn-xs btn-circle"
        onclick={() => toast.dismiss(t.id)}
        aria-label="Fechar"
      >
        <Icon icon="heroicons:x-mark" class="w-4 h-4" />
      </button>
    </div>
  {/each}
</div>
