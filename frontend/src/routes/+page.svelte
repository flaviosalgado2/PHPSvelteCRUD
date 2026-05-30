<script>
	import { onMount } from 'svelte';
	import Icon from '@iconify/svelte';
	import { goto } from '$app/navigation';
	import { pessoasService } from '$lib/services/pessoas.js';
	import { toast } from '$lib/stores/toast.js';

	let statistics = $state({
		total: 0,
		totalFisicas: 0,
		totalJuridicas: 0,
		hoje: 0,
		estaSemana: 0,
		esteMes: 0,
		ultimos7Dias: [],
		recentes: []
	});

	let loading = $state(true);
	let error = $state(null);

	async function carregarEstatisticas() {
		try {
			loading = true;
			error = null;
			statistics = await pessoasService.estatisticas();
		} catch (e) {
			error = e.message;
			toast.error('Erro ao carregar estatísticas.');
		} finally {
			loading = false;
		}
	}

	onMount(() => {
		carregarEstatisticas();
	});

	function formatarTipo(pessoa) {
		return pessoa.tipo === 'fisica' ? 'PF' : 'PJ';
	}

	function formatarNome(pessoa) {
		return pessoa.tipo === 'fisica' ? pessoa.nome : pessoa.razao_social;
	}

	function formatarDocumento(pessoa) {
		return pessoa.tipo === 'fisica' ? pessoa.cpf : pessoa.cnpj;
	}

	function formatarData(dataString) {
		const data = new Date(dataString);
		return data.toLocaleDateString('pt-BR', { 
			day: '2-digit', 
			month: '2-digit', 
			year: 'numeric',
			hour: '2-digit',
			minute: '2-digit'
		});
	}
</script>

<svelte:head>
	<title>Dashboard - Sistema de Gestão de Pessoas</title>
</svelte:head>

{#if loading}
	<div class="flex flex-col justify-center items-center min-h-[500px] gap-6">
		<div class="relative">
			<div class="absolute inset-0 bg-primary/30 rounded-full blur-3xl animate-pulse"></div>
			<span class="relative loading loading-spinner loading-lg text-primary w-20 h-20"></span>
		</div>
		<p class="text-lg font-semibold text-base-content/70">Carregando dashboard...</p>
	</div>
{:else if error}
	<div class="alert alert-error shadow-2xl rounded-2xl">
		<Icon icon="heroicons:exclamation-circle" class="h-7 w-7" />
		<div>
			<h3 class="font-bold text-lg">Erro ao carregar dados</h3>
			<div class="text-sm">{error}</div>
		</div>
	</div>
{:else}
	<!-- Header -->
	<div class="mb-8">
		<h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
			Dashboard de Pessoas
		</h1>
		<p class="text-lg text-base-content/70">Visão geral do sistema de gestão de pessoas</p>
	</div>

	<!-- Stats Cards -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
		<!-- Total de Pessoas -->
		<div class="stats shadow-xl bg-gradient-to-br from-primary/10 to-primary/5 border border-primary/20 rounded-2xl">
			<div class="stat">
				<div class="stat-figure text-primary">
					<Icon icon="heroicons:users" class="w-12 h-12" />
				</div>
				<div class="stat-title">Total de Pessoas</div>
				<div class="stat-value text-primary">{statistics.total}</div>
				<div class="stat-desc">Cadastradas no sistema</div>
			</div>
		</div>

		<!-- Pessoas Físicas -->
		<div class="stats shadow-xl bg-gradient-to-br from-blue-500/10 to-blue-500/5 border border-blue-500/20 rounded-2xl">
			<div class="stat">
				<div class="stat-figure text-blue-600">
					<Icon icon="heroicons:user" class="w-12 h-12" />
				</div>
				<div class="stat-title">Pessoas Físicas</div>
				<div class="stat-value text-blue-600">{statistics.totalFisicas}</div>
				<div class="stat-desc">{statistics.total > 0 ? Math.round((statistics.totalFisicas / statistics.total) * 100) : 0}% do total</div>
			</div>
		</div>

		<!-- Pessoas Jurídicas -->
		<div class="stats shadow-xl bg-gradient-to-br from-purple-500/10 to-purple-500/5 border border-purple-500/20 rounded-2xl">
			<div class="stat">
				<div class="stat-figure text-purple-600">
					<Icon icon="heroicons:building-office" class="w-12 h-12" />
				</div>
				<div class="stat-title">Pessoas Jurídicas</div>
				<div class="stat-value text-purple-600">{statistics.totalJuridicas}</div>
				<div class="stat-desc">{statistics.total > 0 ? Math.round((statistics.totalJuridicas / statistics.total) * 100) : 0}% do total</div>
			</div>
		</div>

		<!-- Cadastros Hoje -->
		<div class="stats shadow-xl bg-gradient-to-br from-green-500/10 to-green-500/5 border border-green-500/20 rounded-2xl">
			<div class="stat">
				<div class="stat-figure text-green-600">
					<Icon icon="heroicons:calendar" class="w-12 h-12" />
				</div>
				<div class="stat-title">Cadastros Hoje</div>
				<div class="stat-value text-green-600">{statistics.hoje}</div>
				<div class="stat-desc">Últimas 24 horas</div>
			</div>
		</div>
	</div>

	<!-- Period Stats -->
	<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
		<!-- Weekly Stats -->
		<div class="card bg-base-100 shadow-xl rounded-2xl border border-base-300">
			<div class="card-body">
				<h2 class="card-title flex items-center gap-2">
					<Icon icon="heroicons:calendar-days" class="w-6 h-6 text-primary" />
					Esta Semana
				</h2>
				<div class="flex items-baseline gap-2 mt-2">
					<span class="text-4xl font-bold text-primary">{statistics.estaSemana}</span>
					<span class="text-base-content/60">novos cadastros</span>
				</div>
				<div class="divider my-2"></div>
				<p class="text-sm text-base-content/70">
					Média de {Math.round(statistics.estaSemana / 7)} cadastros por dia
				</p>
			</div>
		</div>

		<!-- Monthly Stats -->
		<div class="card bg-base-100 shadow-xl rounded-2xl border border-base-300">
			<div class="card-body">
				<h2 class="card-title flex items-center gap-2">
					<Icon icon="heroicons:chart-bar" class="w-6 h-6 text-secondary" />
					Este Mês
				</h2>
				<div class="flex items-baseline gap-2 mt-2">
					<span class="text-4xl font-bold text-secondary">{statistics.esteMes}</span>
					<span class="text-base-content/60">novos cadastros</span>
				</div>
				<div class="divider my-2"></div>
				<p class="text-sm text-base-content/70">
					Média de {Math.round(statistics.esteMes / 30)} cadastros por dia
				</p>
			</div>
		</div>
	</div>

	<!-- Charts and Recent Activity -->
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
		<!-- Chart -->
		<div class="lg:col-span-2">
			<div class="card bg-base-100 shadow-xl rounded-2xl border border-base-300">
				<div class="card-body">
					<h2 class="card-title flex items-center gap-2 mb-4">
						<Icon icon="heroicons:chart-bar-square" class="w-6 h-6 text-primary" />
						Cadastros - Últimos 7 Dias
					</h2>
					
					{#if statistics.ultimos7Dias && statistics.ultimos7Dias.length > 0}
						<div class="flex items-end justify-between h-64 gap-2 px-4">
							{#each statistics.ultimos7Dias as dia}
								{@const maxCount = Math.max(...statistics.ultimos7Dias.map(d => d.count), 1)}
								{@const altura = dia.count > 0 ? (dia.count / maxCount) * 100 : 5}
								<div class="flex-1 flex flex-col items-center gap-2">
									<div class="text-sm font-bold text-primary">{dia.count}</div>
									<div 
										class="w-full bg-gradient-to-t from-primary to-primary/50 rounded-lg transition-all duration-500 hover:scale-105 cursor-pointer relative group"
										style="height: {altura}%"
									>
										<div class="absolute inset-0 bg-primary/20 rounded-lg group-hover:bg-primary/30 transition-colors"></div>
									</div>
									<div class="text-xs text-base-content/60 font-medium">{dia.data}</div>
								</div>
							{/each}
						</div>
					{:else}
						<div class="flex items-center justify-center h-64 text-base-content/40">
							<p>Nenhum dado disponível</p>
						</div>
					{/if}
				</div>
			</div>
		</div>

		<!-- Quick Actions -->
		<div class="card bg-gradient-to-br from-primary/10 to-secondary/10 shadow-xl rounded-2xl border border-primary/20">
			<div class="card-body">
				<h2 class="card-title flex items-center gap-2 mb-4">
					<Icon icon="heroicons:bolt" class="w-6 h-6 text-primary" />
					Ações Rápidas
				</h2>
				<div class="space-y-3">
					<button 
						class="btn btn-primary btn-block justify-start gap-3"
						onclick={() => goto('/pessoas/criar')}
					>
						<Icon icon="heroicons:user-plus" class="w-5 h-5" />
						Nova Pessoa
					</button>
					<button 
						class="btn btn-outline btn-primary btn-block justify-start gap-3"
						onclick={() => goto('/pessoas')}
					>
						<Icon icon="heroicons:list-bullet" class="w-5 h-5" />
						Ver Todas
					</button>
					<div class="divider my-2"></div>
					<div class="stats bg-base-100 shadow">
						<div class="stat p-4">
							<div class="stat-title text-xs">Total Cadastros</div>
							<div class="stat-value text-2xl text-primary">{statistics.total}</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Recent Activity -->
	<div class="card bg-base-100 shadow-xl rounded-2xl border border-base-300">
		<div class="card-body">
			<h2 class="card-title flex items-center gap-2 mb-4">
				<Icon icon="heroicons:clock" class="w-6 h-6 text-primary" />
				Cadastros Recentes
			</h2>
			
			{#if statistics.recentes && statistics.recentes.length > 0}
				<div class="overflow-x-auto">
					<table class="table table-zebra">
						<thead>
							<tr>
								<th>Tipo</th>
								<th>Nome</th>
								<th>Documento</th>
								<th>Email</th>
								<th>Data Cadastro</th>
								<th>Ação</th>
							</tr>
						</thead>
						<tbody>
							{#each statistics.recentes as pessoa}
								<tr class="hover">
									<td>
										<div class="badge {pessoa.tipo === 'fisica' ? 'badge-primary' : 'badge-secondary'} gap-2">
											<Icon icon={pessoa.tipo === 'fisica' ? 'heroicons:user' : 'heroicons:building-office'} class="w-3 h-3" />
											{formatarTipo(pessoa)}
										</div>
									</td>
									<td class="font-semibold">{formatarNome(pessoa)}</td>
									<td class="font-mono text-sm">{formatarDocumento(pessoa) || '-'}</td>
									<td>{pessoa.email || '-'}</td>
									<td class="text-sm text-base-content/70">{formatarData(pessoa.created_at)}</td>
									<td>
										<button 
											class="btn btn-ghost btn-sm"
											onclick={() => goto(`/pessoas/${pessoa.id}`)}
										>
											<Icon icon="heroicons:eye" class="w-4 h-4" />
										</button>
									</td>
								</tr>
							{/each}
						</tbody>
					</table>
				</div>
			{:else}
				<div class="flex flex-col items-center justify-center py-12 text-base-content/40">
					<Icon icon="heroicons:inbox" class="w-16 h-16 mb-4" />
					<p>Nenhum cadastro recente</p>
				</div>
			{/if}

			<div class="card-actions justify-end mt-4">
				<button class="btn btn-primary btn-sm" onclick={() => goto('/pessoas')}>
					Ver Todos
					<Icon icon="heroicons:arrow-right" class="w-4 h-4" />
				</button>
			</div>
		</div>
	</div>
{/if}
