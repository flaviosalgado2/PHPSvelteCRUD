<script>
	import { onMount } from 'svelte';
	import { page } from '$app/stores';
	import PessoasForm from '$lib/components/PessoasForm.svelte';
	import { pessoasService } from '$lib/services/pessoas.js';
	import { toast } from '$lib/stores/toast.js';
	
	let pessoa = $state(null);
	let carregando = $state(true);
	let erro = $state(null);
	let pessoaId = $derived($page.params.id);

	async function carregarPessoa() {
		try {
			carregando = true;
			erro = null;
			pessoa = await pessoasService.buscar(pessoaId);
		} catch (e) {
			erro = e.message;
			toast.error('Pessoa não encontrada.');
		} finally {
			carregando = false;
		}
	}

	onMount(() => {
		carregarPessoa();
	});
</script>

<svelte:head>
	<title>Editar Pessoa</title>
</svelte:head>

{#if carregando}
	<div class="card bg-base-100 shadow-lg border border-base-300/50">
		<div class="card-body items-center justify-center min-h-[400px]">
			<span class="loading loading-spinner loading-lg text-primary"></span>
			<p class="mt-4 text-base-content/70">Carregando...</p>
		</div>
	</div>
{:else if erro}
	<div class="alert alert-error">
		<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
		</svg>
		<div>
			<h3 class="font-bold">Erro ao carregar pessoa</h3>
			<div class="text-sm">{erro}</div>
		</div>
	</div>
{:else if pessoa}
	<PessoasForm modo="editar" {pessoa} />
{/if}
