<script>
  import { goto } from "$app/navigation";
  import Icon from "@iconify/svelte";
  import { _ } from "svelte-i18n";

  let { pessoa = null, modo = "criar" } = $props();

  const API_URL = "http://localhost:8000/api";

  let enviando = $state(false);
  let tipo = $state(pessoa?.tipo || "fisica");

  // Campos Pessoa Física
  let cpf = $state(pessoa?.cpf || "");
  let nome = $state(pessoa?.nome || "");
  let dataNascimento = $state(pessoa?.data_nascimento || "");
  let rg = $state(pessoa?.rg || "");
  let sexo = $state(pessoa?.sexo || "");

  // Campos Pessoa Jurídica
  let cnpj = $state(pessoa?.cnpj || "");
  let razaoSocial = $state(pessoa?.razao_social || "");
  let nomeFantasia = $state(pessoa?.nome_fantasia || "");
  let inscricaoEstadual = $state(pessoa?.inscricao_estadual || "");
  let inscricaoMunicipal = $state(pessoa?.inscricao_municipal || "");

  // Campos comuns
  let email = $state(pessoa?.email || "");
  let telefone = $state(pessoa?.telefone || "");
  let celular = $state(pessoa?.celular || "");
  let cep = $state(pessoa?.cep || "");
  let logradouro = $state(pessoa?.logradouro || "");
  let numero = $state(pessoa?.numero || "");
  let complemento = $state(pessoa?.complemento || "");
  let bairro = $state(pessoa?.bairro || "");
  let cidade = $state(pessoa?.cidade || "");
  let estado = $state(pessoa?.estado || "");

  async function submeterFormulario(event) {
    event.preventDefault();
    enviando = true;

    const dados = {
      tipo,
      cpf: tipo === "fisica" ? cpf : null,
      nome: tipo === "fisica" ? nome : null,
      data_nascimento: tipo === "fisica" ? dataNascimento : null,
      rg: tipo === "fisica" ? rg : null,
      sexo: tipo === "fisica" ? sexo : null,
      cnpj: tipo === "juridica" ? cnpj : null,
      razao_social: tipo === "juridica" ? razaoSocial : null,
      nome_fantasia: tipo === "juridica" ? nomeFantasia : null,
      inscricao_estadual: tipo === "juridica" ? inscricaoEstadual : null,
      inscricao_municipal: tipo === "juridica" ? inscricaoMunicipal : null,
      email,
      telefone,
      celular,
      cep,
      logradouro,
      numero,
      complemento,
      bairro,
      cidade,
      estado,
    };

    try {
      const url = modo === "criar" ? `${API_URL}/pessoas` : `${API_URL}/pessoas/${pessoa.id}`;
      const metodo = modo === "criar" ? "POST" : "PUT";

      const response = await fetch(url, {
        method: metodo,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados),
      });

      if (!response.ok) throw new Error("Erro ao salvar pessoa");

      goto("/pessoas");
    } catch (e) {
      alert("Erro ao salvar pessoa: " + e.message);
    } finally {
      enviando = false;
    }
  }
</script>

<div class="space-y-6">
  <!-- Cabeçalho -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-primary">
        {modo === "criar" ? $_("pessoas.cadastrar_nova") : $_("pessoas.atualizar")}
      </h1>
      <p class="mt-2 text-sm text-base-content/70">
        {modo === "criar" ? $_("pessoas.preencher_dados") : "Atualize as informações da pessoa"}
      </p>
    </div>
    <a href="/pessoas" class="btn btn-ghost gap-2">
      <Icon icon="heroicons:arrow-left" class="w-5 h-5" />
      {$_("common.back")}
    </a>
  </div>

  <form onsubmit={submeterFormulario} class="space-y-6">
    <!-- Tipo de Pessoa -->
    <div class="card bg-base-100 shadow-lg border border-base-300/50">
      <div class="card-body p-6">
        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
          <Icon icon="heroicons:tag" class="w-6 h-6 text-primary" />
          {$_("pessoas.tipo_pessoa")}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <label class="relative cursor-pointer group">
            <input type="radio" name="tipo" class="peer sr-only" value="fisica" bind:group={tipo} disabled={modo === "editar"} />
            <div class="flex items-start gap-4 p-6 border-2 rounded-xl transition-all peer-checked:border-primary peer-checked:bg-primary/5 border-base-300 hover:border-primary/50">
              <div class="flex-shrink-0">
                <div class="w-5 h-5 rounded-full border-2 transition-all peer-checked:border-8 peer-checked:border-primary border-base-300"></div>
              </div>
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <Icon icon="heroicons:user" class="w-5 h-5 text-primary" />
                  <span class="font-bold">{$_("pessoas.pessoa_fisica")}</span>
                </div>
                <p class="text-sm text-base-content/70">{$_("pessoas.pessoa_fisica_desc")}</p>
              </div>
            </div>
          </label>

          <label class="relative cursor-pointer group">
            <input type="radio" name="tipo" class="peer sr-only" value="juridica" bind:group={tipo} disabled={modo === "editar"} />
            <div class="flex items-start gap-4 p-6 border-2 rounded-xl transition-all peer-checked:border-secondary peer-checked:bg-secondary/5 border-base-300 hover:border-secondary/50">
              <div class="flex-shrink-0">
                <div class="w-5 h-5 rounded-full border-2 transition-all peer-checked:border-8 peer-checked:border-secondary border-base-300"></div>
              </div>
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <Icon icon="heroicons:building-office-2" class="w-5 h-5 text-secondary" />
                  <span class="font-bold">{$_("pessoas.pessoa_juridica")}</span>
                </div>
                <p class="text-sm text-base-content/70">{$_("pessoas.pessoa_juridica_desc")}</p>
              </div>
            </div>
          </label>
        </div>
      </div>
    </div>

    <!-- Dados Específicos -->
    <div class="card bg-base-100 shadow-lg border border-base-300/50">
      <div class="card-body p-6">
        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
          <Icon icon="heroicons:document-text" class="w-6 h-6 text-primary" />
          {tipo === "fisica" ? $_("pessoas.dados_pessoais") : $_("pessoas.dados_empresa")}
        </h2>

        {#if tipo === "fisica"}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control md:col-span-2">
              <label class="label">
                <span class="label-text font-semibold flex items-center gap-1">
                  <span class="text-error">*</span>
                  {$_("pessoas.nome_completo")}
                </span>
              </label>
              <input
                type="text"
                bind:value={nome}
                class="input input-bordered"
                required
                placeholder={$_("pessoas.nome_completo_placeholder")}
              />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold flex items-center gap-1">
                  <span class="text-error">*</span>
                  {$_("pessoas.cpf")}
                </span>
              </label>
              <input
                type="text"
                bind:value={cpf}
                class="input input-bordered"
                required
                placeholder="000.000.000-00"
              />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">{$_("pessoas.rg")}</span>
              </label>
              <input type="text" bind:value={rg} class="input input-bordered" placeholder="00.000.000-0" />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">{$_("pessoas.data_nascimento")}</span>
              </label>
              <input type="date" bind:value={dataNascimento} class="input input-bordered" />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">{$_("pessoas.sexo")}</span>
              </label>
              <select bind:value={sexo} class="select select-bordered">
                <option value="">Selecione...</option>
                <option value="masculino">{$_("pessoas.masculino")}</option>
                <option value="feminino">{$_("pessoas.feminino")}</option>
                <option value="outro">{$_("pessoas.outro")}</option>
              </select>
            </div>
          </div>
        {:else}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control md:col-span-2">
              <label class="label">
                <span class="label-text font-semibold flex items-center gap-1">
                  <span class="text-error">*</span>
                  {$_("pessoas.razao_social")}
                </span>
              </label>
              <input
                type="text"
                bind:value={razaoSocial}
                class="input input-bordered"
                required
                placeholder="Nome empresarial completo"
              />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">{$_("pessoas.nome_fantasia")}</span>
              </label>
              <input type="text" bind:value={nomeFantasia} class="input input-bordered" placeholder="Nome comercial" />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold flex items-center gap-1">
                  <span class="text-error">*</span>
                  {$_("pessoas.cnpj")}
                </span>
              </label>
              <input
                type="text"
                bind:value={cnpj}
                class="input input-bordered"
                required
                placeholder="00.000.000/0000-00"
              />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">{$_("pessoas.inscricao_estadual")}</span>
              </label>
              <input type="text" bind:value={inscricaoEstadual} class="input input-bordered" />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">{$_("pessoas.inscricao_municipal")}</span>
              </label>
              <input type="text" bind:value={inscricaoMunicipal} class="input input-bordered" />
            </div>
          </div>
        {/if}
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
          <div class="form-control md:col-span-2">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.email")}</span>
            </label>
            <input type="email" bind:value={email} class="input input-bordered" placeholder="email@exemplo.com" />
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.telefone")}</span>
            </label>
            <input type="tel" bind:value={telefone} class="input input-bordered" placeholder="(00) 0000-0000" />
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.celular")}</span>
            </label>
            <input type="tel" bind:value={celular} class="input input-bordered" placeholder="(00) 00000-0000" />
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.cep")}</span>
            </label>
            <input type="text" bind:value={cep} class="input input-bordered" placeholder="00000-000" />
          </div>

          <div class="form-control md:col-span-2 lg:col-span-3">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.logradouro")}</span>
            </label>
            <input type="text" bind:value={logradouro} class="input input-bordered" placeholder="Rua, Avenida, etc." />
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.numero")}</span>
            </label>
            <input type="text" bind:value={numero} class="input input-bordered" placeholder="123" />
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.complemento")}</span>
            </label>
            <input type="text" bind:value={complemento} class="input input-bordered" placeholder="Apto, Sala, etc." />
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.bairro")}</span>
            </label>
            <input type="text" bind:value={bairro} class="input input-bordered" />
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.cidade")}</span>
            </label>
            <input type="text" bind:value={cidade} class="input input-bordered" />
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">{$_("pessoas.estado")}</span>
            </label>
            <select bind:value={estado} class="select select-bordered">
              <option value="">Selecione...</option>
              <option value="AC">AC</option>
              <option value="AL">AL</option>
              <option value="AP">AP</option>
              <option value="AM">AM</option>
              <option value="BA">BA</option>
              <option value="CE">CE</option>
              <option value="DF">DF</option>
              <option value="ES">ES</option>
              <option value="GO">GO</option>
              <option value="MA">MA</option>
              <option value="MT">MT</option>
              <option value="MS">MS</option>
              <option value="MG">MG</option>
              <option value="PA">PA</option>
              <option value="PB">PB</option>
              <option value="PR">PR</option>
              <option value="PE">PE</option>
              <option value="PI">PI</option>
              <option value="RJ">RJ</option>
              <option value="RN">RN</option>
              <option value="RS">RS</option>
              <option value="RO">RO</option>
              <option value="RR">RR</option>
              <option value="SC">SC</option>
              <option value="SP">SP</option>
              <option value="SE">SE</option>
              <option value="TO">TO</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Botões de ação -->
    <div class="flex gap-4 justify-end">
      <a href="/pessoas" class="btn btn-ghost">{$_("common.cancel")}</a>
      <button type="submit" class="btn btn-primary" disabled={enviando}>
        {#if enviando}
          <span class="loading loading-spinner"></span>
          Salvando...
        {:else}
          <Icon icon="heroicons:check" class="w-5 h-5" />
          {$_("common.save")}
        {/if}
      </button>
    </div>
  </form>
</div>
