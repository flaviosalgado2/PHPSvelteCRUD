import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
	plugins: [
		sveltekit({
			onwarn: (warning, handler) => {
				// Suprimir warning sobre svelte-fullcalendar não ter exports condition
				if (warning.code === 'MISSING_EXPORTS_CONDITION' && warning.message.includes('svelte-fullcalendar')) {
					return;
				}
				handler(warning);
			}
		})
	]
});
