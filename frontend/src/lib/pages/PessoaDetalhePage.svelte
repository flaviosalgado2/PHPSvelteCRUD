<script>
  import Icon from "@iconify/svelte";
  import { _ } from "svelte-i18n";
  import { goto } from "$app/navigation";

  let { pessoaId } = $props();

  const API_URL = "http://localhost:8000/api";

  let pessoa = $state(null);
  let carregando = $state(true);
  let erro = $state(null);

  async function carregarPessoa() {
    try {
      carregando = true;
      erro = null;
      const response = await fetch(`${API_URL}/pessoas/${pessoaId}`);
      if (!response.ok) throw new Error("Erro ao carregar pessoa");
      pessoa = await response.json();
    } catch (e) {
      erro = e.message;
      console.error("Erro:", e);
    } finally {
      carregando = false;
    }
  }

  async function excluirPessoa() {
    if (!confirm("Tem certeza que deseja excluir esta pessoa?")) return;

    try {
      const response = await fetch(`${API_URL}/pessoas/${pessoaId}`, {
        method: "DELETE",
      });
      if (!response.ok) throw new Error("Erro ao excluir pessoa");
      goto("/pessoas");
    } catch (e) {
      alert("Erro ao excluir pessoa: " + e.message);
    }
  }

  function obterNome() {
    if (!pessoa) return "";
    return pessoa.tipo === "fisica" ? pessoa.nome : pessoa.razao_social;
  }

  function obterDocumento() {
    if (!pessoa) return "";
    return pessoa.tipo === "fisica" ? pessoa.cpf : pessoa.cnpj;
  }

  $effect(() => {
    if (pessoaId) {
      carregarPessoa();
    }
  });
</script>

<div class="space-y-6">
  <!-- Cabeçalho -->
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="/pessoas" class="btn btn-ghost btn-sm gap-2">
        <Icon icon="heroicons:arrow-left" class="w-5 h-5" />
        {$_("common.back")}
      </a>
      {#if pessoa}
        <div>
          <h1 class="text-3xl font-bold text-primary">{obterNome()}</h1>
          <p class="text-sm text-base-content/70 mt-1">
            <span class="badge {pessoa.tipo === 'fisica' ? 'badge-primary' : 'badge-secondary'} gap-2">
              <Icon icon={pessoa.tipo === 'fisica' ? 'heroicons:user' : 'heroicons:building-office-2'} class="w-4 h-4" />
              {pessoa.tipo === "fisica" ? $_("pessoas.fisica") : $_("pessoas.juridica")}
            </span>
          </p>
        </div>
      {/if}
    </div>
    {#if pessoa}
      <div class="flex gap-2">
        <a href="/pessoas/{pessoaId}/editar" class="btn btn-primary gap-2">
          <Icon icon="heroicons:pencil-square" class="w-5 h-5" />
          {$_("common.edit")}
        </a>
        <button onclick={excluirPessoa} class="btn btn-error gap-2">
          <Icon icon="heroicons:trash" class="w-5 h-5" />
          {$_("common.delete")}
        </button>
      </div>
    {/if}
  </div>

  {#if carregando}
    <div class="card bg-base-100 shadow-lg border border-base-300/50">
      <div class="card-body items-center justify-center min-h-[400px]">
        <span class="loading loading-spinner loading-lg text-primary"></span>
        <p class="mt-4 text-base-content/70">{$_("common.loading")}</p>
      </div>
    </div>
  {:else if erro}
    <div class="alert alert-error">
      <Icon icon="heroicons:exclamation-circle" class="w-6 h-6" />
      <div>
        <h3 class="font-bold">{$_("pessoas.erro_carregar")}</h3>
        <div class="text-sm">{erro}</div>
      </div>
    </div>
  {:else if pessoa}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Coluna principal -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Dados principais -->
        <div class="card bg-base-100 shadow-lg border border-base-300/50">
          <div class="card-body p-6">
            <h2 class="card-title text-xl mb-4 flex items-center gap-2">
              <Icon icon="heroicons:document-text" class="w-6 h-6 text-primary" />
              {pessoa.tipo === "fisica" ? $_("pessoas.dados_pessoais") : $_("pessoas.dados_empresa")}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              {#if pessoa.tipo === "fisica"}
                <div>
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.nome_completo")}</span>
                  </label>
                  <p class="text-lg">{pessoa.nome || "-"}</p>
                </div>

                <div>
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.cpf")}</span>
                  </label>
                  <p class="text-lg font-mono">{pessoa.cpf || "-"}</p>
                </div>

                <div>
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.rg")}</span>
                  </label>
                  <p class="text-lg">{pessoa.rg || "-"}</p>
                </div>

                <div>
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.data_nascimento")}</span>
                  </label>
                  <p class="text-lg">{pessoa.data_nascimento || "-"}</p>
                </div>

                <div>
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.sexo")}</span>
                  </label>
                  <p class="text-lg">{pessoa.sexo || "-"}</p>
                </div>
              {:else}
                <div class="md:col-span-2">
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.razao_social")}</span>
                  </label>
                  <p class="text-lg">{pessoa.razao_social || "-"}</p>
                </div>

                <div>
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.nome_fantasia")}</span>
                  </label>
                  <p class="text-lg">{pessoa.nome_fantasia || "-"}</p>
                </div>

                <div>
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.cnpj")}</span>
                  </label>
                  <p class="text-lg font-mono">{pessoa.cnpj || "-"}</p>
                </div>

                <div>
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.inscricao_estadual")}</span>
                  </label>
                  <p class="text-lg">{pessoa.inscricao_estadual || "-"}</p>
                </div>

                <div>
                  <label class="label">
                    <span class="label-text font-semibold">{$_("pessoas.inscricao_municipal")}</span>
                  </label>
                  <p class="text-lg">{pessoa.inscricao_municipal || "-"}</p>
                </div>
              {/if}
            </div>
          </div>
        </div>

        <!-- Contato -->
        <div class="card bg-base-100 shadow-lg border border-base-300/50">
          <div class="card-body p-6">
            <h2 class="card-title text-xl mb-4 flex items-center gap-2">
              <Icon icon="heroicons:phone" class="w-6 h-6 text-primary" />
              {$_("pessoas.contato")}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.email")}</span>
                </label>
                <p class="text-lg">{pessoa.email || "-"}</p>
              </div>

              <div>
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.telefone")}</span>
                </label>
                <p class="text-lg">{pessoa.telefone || "-"}</p>
              </div>

              <div>
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.celular")}</span>
                </label>
                <p class="text-lg">{pessoa.celular || "-"}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Endereço -->
        <div class="card bg-base-100 shadow-lg border border-base-300/50">
          <div class="card-body p-6">
            <h2 class="card-title text-xl mb-4 flex items-center gap-2">
              <Icon icon="heroicons:map-pin" class="w-6 h-6 text-primary" />
              {$_("pessoas.endereco")}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.cep")}</span>
                </label>
                <p class="text-lg">{pessoa.cep || "-"}</p>
              </div>

              <div class="md:col-span-2">
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.logradouro")}</span>
                </label>
                <p class="text-lg">{pessoa.logradouro || "-"}</p>
              </div>

              <div>
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.numero")}</span>
                </label>
                <p class="text-lg">{pessoa.numero || "-"}</p>
              </div>

              <div>
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.complemento")}</span>
                </label>
                <p class="text-lg">{pessoa.complemento || "-"}</p>
              </div>

              <div>
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.bairro")}</span>
                </label>
                <p class="text-lg">{pessoa.bairro || "-"}</p>
              </div>

              <div>
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.cidade")}</span>
                </label>
                <p class="text-lg">{pessoa.cidade || "-"}</p>
              </div>

              <div>
                <label class="label">
                  <span class="label-text font-semibold">{$_("pessoas.estado")}</span>
                </label>
                <p class="text-lg">{pessoa.estado || "-"}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Coluna lateral -->
      <div class="space-y-6">
        <!-- Card do Avatar -->
        <div class="card bg-base-100 shadow-lg border border-base-300/50">
          <div class="card-body items-center p-6">
            <div class="avatar placeholder">
              <div class="bg-{pessoa.tipo === 'fisica' ? 'primary' : 'secondary'}/20 text-{pessoa.tipo === 'fisica' ? 'primary' : 'secondary'} rounded-full w-32 h-32">
                <span class="text-5xl font-bold">{obterNome().charAt(0).toUpperCase()}</span>
              </div>
            </div>
            <h3 class="text-xl font-bold mt-4 text-center">{obterNome()}</h3>
            <p class="text-sm text-base-content/70 font-mono">{obterDocumento()}</p>
          </div>
        </div>

        <!-- Card de Ações Rápidas -->
        <div class="card bg-base-100 shadow-lg border border-base-300/50">
          <div class="card-body p-6">
            <h3 class="card-title text-lg mb-4">{$_("common.quick_actions")}</h3>
            <div class="space-y-2">
              <a href="/pessoas/{pessoaId}/editar" class="btn btn-outline btn-block justify-start gap-2">
                <Icon icon="heroicons:pencil-square" class="w-5 h-5" />
                {$_("common.edit")}
              </a>
              <button onclick={excluirPessoa} class="btn btn-outline btn-error btn-block justify-start gap-2">
                <Icon icon="heroicons:trash" class="w-5 h-5" />
                {$_("common.delete")}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  {/if}
</div>
