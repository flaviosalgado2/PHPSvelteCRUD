<script>
  import { onMount } from "svelte";
  import Icon from "@iconify/svelte";
  import { _ } from "svelte-i18n";
  import { motionInView, staggerAnimate } from "../utils/motion.js";
  import DeleteConfirmationModal from "../components/DeleteConfirmationModal.svelte";

  const API_URL = "http://localhost:8000/api";

  let pessoas = $state([]);
  let filteredPessoas = $state([]);
  let loading = $state(true);
  let error = $state(null);
  let searchTerm = $state("");
  let filterType = $state("all");
  let sortBy = $state("nome");
  let sortDirection = $state("asc");
  let showDeleteModal = $state(false);
  let pessoaParaExcluir = $state(null);

  let statsElements = [];
  let pessoasRowElements = [];

  const iconColors = {
    "heroicons:users": "from-blue-500 to-indigo-600",
    "heroicons:user": "from-green-500 to-emerald-600",
    "heroicons:building-office-2": "from-purple-500 to-pink-600",
    "heroicons:user-plus": "from-orange-500 to-red-600",
  };

  async function carregarPessoas() {
    try {
      loading = true;
      error = null;
      const response = await fetch(`${API_URL}/pessoas`);
      if (!response.ok) throw new Error("Erro ao carregar pessoas");
      pessoas = await response.json();
    } catch (e) {
      error = e.message;
      console.error("Erro:", e);
    } finally {
      loading = false;
    }
  }

  async function confirmarExclusao() {
    if (!pessoaParaExcluir) return;

    try {
      const response = await fetch(`${API_URL}/pessoas/${pessoaParaExcluir.id}`, {
        method: "DELETE",
      });
      if (!response.ok) throw new Error("Erro ao excluir pessoa");
      await carregarPessoas();
      pessoaParaExcluir = null;
      showDeleteModal = false;
    } catch (e) {
      alert("Erro ao excluir pessoa: " + e.message);
    }
  }

  function cancelarExclusao() {
    pessoaParaExcluir = null;
    showDeleteModal = false;
  }

  function prepararExclusao(pessoa) {
    pessoaParaExcluir = pessoa;
    showDeleteModal = true;
  }

  // Estatísticas calculadas
  let estatisticas = $derived.by(() => {
    const total = pessoas.length;
    const fisicas = pessoas.filter((p) => p.tipo === "fisica").length;
    const juridicas = pessoas.filter((p) => p.tipo === "juridica").length;
    const mesAtual = new Date().getMonth();
    const cadastrosMes = pessoas.filter((p) => {
      if (!p.created_at) return false;
      const dataCriacao = new Date(p.created_at);
      return dataCriacao.getMonth() === mesAtual;
    }).length;

    return [
      {
        nome: $_("pessoas.total_pessoas"),
        valor: total.toString(),
        mudanca: "+12%",
        tipoMudanca: "positive",
        icone: "heroicons:users",
      },
      {
        nome: $_("pessoas.pessoas_fisicas"),
        valor: fisicas.toString(),
        mudanca: "+8%",
        tipoMudanca: "positive",
        icone: "heroicons:user",
      },
      {
        nome: $_("pessoas.pessoas_juridicas"),
        valor: juridicas.toString(),
        mudanca: "+5%",
        tipoMudanca: "positive",
        icone: "heroicons:building-office-2",
      },
      {
        nome: $_("pessoas.cadastros_mes"),
        valor: cadastrosMes.toString(),
        mudanca: "+15%",
        tipoMudanca: "positive",
        icone: "heroicons:user-plus",
      },
    ];
  });

  // Filtros e ordenação
  $effect(() => {
    let resultado = [...pessoas];

    // Filtro por tipo
    if (filterType !== "all") {
      resultado = resultado.filter((p) => p.tipo === filterType);
    }

    // Filtro por busca
    if (searchTerm) {
      const termoBusca = searchTerm.toLowerCase();
      resultado = resultado.filter((p) => {
        const nome = (p.tipo === "fisica" ? p.nome : p.razao_social)?.toLowerCase() || "";
        const doc = (p.tipo === "fisica" ? p.cpf : p.cnpj)?.toLowerCase() || "";
        const email = p.email?.toLowerCase() || "";
        return nome.includes(termoBusca) || doc.includes(termoBusca) || email.includes(termoBusca);
      });
    }

    // Ordenação
    resultado.sort((a, b) => {
      let valorA = a[sortBy];
      let valorB = b[sortBy];

      if (sortBy === "nome") {
        valorA = a.tipo === "fisica" ? a.nome : a.razao_social;
        valorB = b.tipo === "fisica" ? b.nome : b.razao_social;
      }

      if (!valorA) return 1;
      if (!valorB) return -1;

      const comparacao = valorA > valorB ? 1 : -1;
      return sortDirection === "asc" ? comparacao : -comparacao;
    });

    filteredPessoas = resultado;
  });

  function ordenarPor(campo) {
    if (sortBy === campo) {
      sortDirection = sortDirection === "asc" ? "desc" : "asc";
    } else {
      sortBy = campo;
      sortDirection = "asc";
    }
  }

  function limparFiltros() {
    searchTerm = "";
    filterType = "all";
  }

  function obterNome(pessoa) {
    return pessoa.tipo === "fisica" ? pessoa.nome : pessoa.razao_social;
  }

  function obterDocumento(pessoa) {
    return pessoa.tipo === "fisica" ? pessoa.cpf : pessoa.cnpj;
  }

  function obterIniciais(pessoa) {
    const nome = obterNome(pessoa);
    return nome?.charAt(0).toUpperCase() || "?";
  }

  onMount(() => {
    carregarPessoas();

    // Animações
    if (statsElements.length > 0) {
      staggerAnimate(statsElements, "fadeInUp", { delay: 0.1 });
    }
    if (pessoasRowElements.length > 0) {
      staggerAnimate(pessoasRowElements, "fadeInUp", { delay: 0.05 });
    }
  });
</script>

<div class="space-y-8">
  <!-- Cabeçalho da página -->
  <div
    class="relative overflow-hidden bg-gradient-to-r from-primary/5 via-secondary/5 to-accent/5 rounded-2xl p-6 border border-base-300/50"
    use:motionInView={{ animation: "fadeInDown" }}
  >
    <div class="absolute top-0 right-0 w-16 h-16 opacity-10">
      <Icon icon="heroicons:users" class="w-full h-full text-primary" />
    </div>
    <div class="absolute bottom-0 left-0 w-24 h-24 opacity-10">
      <Icon icon="heroicons:user-group" class="w-full h-full text-secondary" />
    </div>

    <div class="relative flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-primary">
          {$_("pessoas.title")}
        </h1>
        <p class="mt-2 text-sm text-base-content/70 max-w-md">
          {$_("pessoas.description")}
        </p>
      </div>
      <a
        href="/pessoas/criar"
        class="btn btn-primary shadow-xl hover:shadow-2xl group relative overflow-hidden"
      >
        <span class="relative flex items-center gap-2">
          <Icon icon="heroicons:plus" class="w-5 h-5" />
          {$_("pessoas.adicionar_pessoa")}
        </span>
      </a>
    </div>
  </div>

  <!-- Estatísticas -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    {#each estatisticas as stat, i}
      <div
        bind:this={statsElements[i]}
        class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 border border-base-300/50"
      >
        <div class="card-body p-6">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <p class="text-sm text-base-content/60 mb-1">{stat.nome}</p>
              <p class="text-3xl font-bold text-base-content">{stat.valor}</p>
              <p class="text-xs mt-1 {stat.tipoMudanca === 'positive' ? 'text-success' : 'text-error'}">
                {stat.mudanca} vs mês anterior
              </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br {iconColors[stat.icone]} flex items-center justify-center">
              <Icon icon={stat.icone} class="w-6 h-6 text-white" />
            </div>
          </div>
        </div>
      </div>
    {/each}
  </div>

  {#if loading}
    <div class="card bg-base-100 shadow-lg border border-base-300/50">
      <div class="card-body items-center justify-center min-h-[400px]">
        <span class="loading loading-spinner loading-lg text-primary"></span>
        <p class="mt-4 text-base-content/70">{$_("common.loading")}</p>
      </div>
    </div>
  {:else if error}
    <div class="alert alert-error">
      <Icon icon="heroicons:exclamation-circle" class="w-6 h-6" />
      <div>
        <h3 class="font-bold">{$_("pessoas.erro_carregar")}</h3>
        <div class="text-sm">{error}</div>
      </div>
    </div>
  {:else}
    <!-- Filtros e busca -->
    <div class="card bg-base-100 shadow-lg border border-base-300/50">
      <div class="card-body p-6">
        <div class="flex flex-col lg:flex-row gap-4">
          <!-- Campo de busca -->
          <div class="flex-1">
            <div class="relative">
              <input
                type="text"
                bind:value={searchTerm}
                placeholder={$_("pessoas.buscar_placeholder")}
                class="input input-bordered w-full pl-10"
              />
              <Icon icon="heroicons:magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-base-content/40" />
            </div>
          </div>

          <!-- Filtro por tipo -->
          <div class="flex gap-2">
            <button
              onclick={() => (filterType = "all")}
              class="btn {filterType === 'all' ? 'btn-primary' : 'btn-ghost'}"
            >
              {$_("pessoas.todos_tipos")}
            </button>
            <button
              onclick={() => (filterType = "fisica")}
              class="btn {filterType === 'fisica' ? 'btn-primary' : 'btn-ghost'}"
            >
              <Icon icon="heroicons:user" class="w-5 h-5" />
              {$_("pessoas.pessoa_fisica")}
            </button>
            <button
              onclick={() => (filterType = "juridica")}
              class="btn {filterType === 'juridica' ? 'btn-primary' : 'btn-ghost'}"
            >
              <Icon icon="heroicons:building-office-2" class="w-5 h-5" />
              {$_("pessoas.pessoa_juridica")}
            </button>
          </div>

          {#if searchTerm || filterType !== "all"}
            <button onclick={limparFiltros} class="btn btn-ghost">
              <Icon icon="heroicons:x-mark" class="w-5 h-5" />
              {$_("common.clear_filters")}
            </button>
          {/if}
        </div>
      </div>
    </div>

    <!-- Tabela de pessoas -->
    {#if filteredPessoas.length === 0}
      <div class="card bg-base-100 shadow-lg border border-base-300/50">
        <div class="card-body items-center justify-center min-h-[400px]">
          <Icon icon="heroicons:users" class="w-24 h-24 text-base-content/20" />
          <h3 class="text-2xl font-bold mt-4">{$_("pessoas.sem_resultados")}</h3>
          <p class="text-base-content/60 mt-2">{$_("pessoas.ajustar_filtros")}</p>
          {#if searchTerm || filterType !== "all"}
            <button onclick={limparFiltros} class="btn btn-primary mt-4">
              {$_("common.clear_filters")}
            </button>
          {/if}
        </div>
      </div>
    {:else}
      <div class="card bg-base-100 shadow-lg border border-base-300/50">
        <div class="overflow-x-auto">
          <table class="table table-zebra">
            <thead>
              <tr>
                <th>
                  <button onclick={() => ordenarPor("tipo")} class="flex items-center gap-1 hover:text-primary">
                    {$_("pessoas.tipo")}
                    <Icon icon="heroicons:arrows-up-down" class="w-4 h-4" />
                  </button>
                </th>
                <th>
                  <button onclick={() => ordenarPor("nome")} class="flex items-center gap-1 hover:text-primary">
                    {$_("pessoas.nome")}
                    <Icon icon="heroicons:arrows-up-down" class="w-4 h-4" />
                  </button>
                </th>
                <th>CPF/CNPJ</th>
                <th>{$_("pessoas.email")}</th>
                <th>{$_("pessoas.telefone")}</th>
                <th class="text-right">{$_("pessoas.acoes")}</th>
              </tr>
            </thead>
            <tbody>
              {#each filteredPessoas as pessoa, i}
                <tr bind:this={pessoasRowElements[i]} class="hover">
                  <td>
                    <div class="badge {pessoa.tipo === 'fisica' ? 'badge-primary' : 'badge-secondary'} gap-2">
                      <Icon icon={pessoa.tipo === 'fisica' ? 'heroicons:user' : 'heroicons:building-office-2'} class="w-4 h-4" />
                      {pessoa.tipo === "fisica" ? $_("pessoas.fisica") : $_("pessoas.juridica")}
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center gap-3">
                      <div class="avatar placeholder">
                        <div class="bg-{pessoa.tipo === 'fisica' ? 'primary' : 'secondary'}/20 text-{pessoa.tipo === 'fisica' ? 'primary' : 'secondary'} rounded-full w-10 h-10">
                          <span class="text-sm font-semibold">{obterIniciais(pessoa)}</span>
                        </div>
                      </div>
                      <span class="font-semibold">{obterNome(pessoa)}</span>
                    </div>
                  </td>
                  <td>
                    <span class="font-mono text-sm">{obterDocumento(pessoa)}</span>
                  </td>
                  <td>{pessoa.email || "-"}</td>
                  <td>{pessoa.telefone || pessoa.celular || "-"}</td>
                  <td>
                    <div class="flex gap-2 justify-end">
                      <a href="/pessoas/{pessoa.id}" class="btn btn-ghost btn-sm" title={$_("pessoas.visualizar")}>
                        <Icon icon="heroicons:eye" class="w-5 h-5" />
                      </a>
                      <a href="/pessoas/{pessoa.id}/editar" class="btn btn-ghost btn-sm" title={$_("pessoas.editar")}>
                        <Icon icon="heroicons:pencil-square" class="w-5 h-5" />
                      </a>
                      <button
                        onclick={() => prepararExclusao(pessoa)}
                        class="btn btn-ghost btn-sm text-error"
                        title={$_("pessoas.excluir")}
                      >
                        <Icon icon="heroicons:trash" class="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </div>
      </div>

      <!-- Contador de resultados -->
      <div class="text-center text-sm text-base-content/60">
        {$_("pessoas.exibindo")} {filteredPessoas.length} {$_("pessoas.de")} {pessoas.length} {$_("pessoas.pessoas")}
      </div>
    {/if}
  {/if}
</div>

<!-- Modal de confirmação de exclusão -->
{#if showDeleteModal && pessoaParaExcluir}
  <DeleteConfirmationModal
    itemName={obterNome(pessoaParaExcluir)}
    onConfirm={confirmarExclusao}
    onCancel={cancelarExclusao}
  />
{/if}
