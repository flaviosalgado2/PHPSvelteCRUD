<script>
	import PessoaDetalhePage from '$lib/pages/PessoaDetalhePage.svelte';
	import { page } from '$app/stores';
	
	let pessoaId = $derived($page.params.id);
</script>

<svelte:head>
	<title>Detalhes da Pessoa</title>
</svelte:head>

<PessoaDetalhePage {pessoaId} />
