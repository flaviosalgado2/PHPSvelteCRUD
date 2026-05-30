<script>
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import { page } from '$app/stores';
	import DashboardLayout from '$lib/components/DashboardLayout.svelte';
	import ToastContainer from '$lib/components/ToastContainer.svelte';
	import { themeActions } from '$lib/stores/theme.js';
	import { i18nReadyPromise } from '$lib/i18n/index.js';
	import LoadingSpinner from '$lib/components/LoadingSpinner.svelte';
	import '../app.css';

	let { children } = $props();
	let appReady = $state(false);
	let loading = $state(true);

	// Check if current route is auth page
	let isAuthPage = $derived($page.url.pathname.startsWith('/auth/'));

	// Async logic for app readiness
	onMount(async () => {
		if (browser) {
			// Initialize theme system
			themeActions.init();

			// Wait for i18n to be ready (with timeout fallback)
			try {
				await Promise.race([
					i18nReadyPromise,
					new Promise(resolve => setTimeout(resolve, 1000))
				]);
			} catch (error) {
				console.warn('i18n initialization timeout or error:', error);
			}

			// Mark app as ready
			appReady = true;
			loading = false;
		}
	});
</script>

<svelte:head>
	<title>Sistema de Pessoas</title>
</svelte:head>

{#if loading || !appReady}
	<LoadingSpinner />
{:else if isAuthPage}
	<!-- Auth pages without dashboard layout -->
	{@render children()}
{:else}
	<!-- Regular pages with dashboard layout -->
	<DashboardLayout>
		{@render children()}
	</DashboardLayout>
{/if}

<ToastContainer />
