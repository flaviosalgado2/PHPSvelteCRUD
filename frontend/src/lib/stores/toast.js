import { writable } from 'svelte/store';

let nextId = 0;

function createToastStore() {
  const { subscribe, update } = writable([]);

  function add(type, message, duration = 4000) {
    const id = ++nextId;
    update((toasts) => [...toasts, { id, type, message }]);

    if (duration > 0) {
      setTimeout(() => dismiss(id), duration);
    }

    return id;
  }

  function dismiss(id) {
    update((toasts) => toasts.filter((t) => t.id !== id));
  }

  return {
    subscribe,
    success: (msg, duration) => add('success', msg, duration),
    error: (msg, duration) => add('error', msg, duration),
    info: (msg, duration) => add('info', msg, duration),
    warning: (msg, duration) => add('warning', msg, duration),
    dismiss,
  };
}

export const toast = createToastStore();
