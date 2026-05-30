<script>
  import { goto } from "$app/navigation";
  import Icon from "@iconify/svelte";
  import { _ } from "svelte-i18n";
  import { pessoasService } from "$lib/services/pessoas.js";
  import { toast } from "$lib/stores/toast.js";
  import { ApiError } from "$lib/services/api.js";

  let { pessoa = null, modo = "criar" } = $props();

  // ── Estado do formulário ────────────────────────────────────────────────────
  let form = $state({
    tipo: "fisica",
    cpf: "", nome: "", data_nascimento: "", rg: "", sexo: "",
    cnpj: "", razao_social: "", nome_fantasia: "",
    inscricao_estadual: "", inscricao_municipal: "",
    email: "", telefone: "", celular: "",
    cep: "", logradouro: "", numero: "",
    complemento: "", bairro: "", cidade: "", estado: "",
  });

  let enviando = $state(false);
  let buscandoCEP = $state(false);
  let erros = $state({});

  // ── Sincronizar com prop ────────────────────────────────────────────────────
  $effect(() => {
    if (pessoa) {
      form = {
        tipo:                pessoa.tipo              || "fisica",
        cpf:                 pessoa.cpf               || "",
        nome:                pessoa.nome              || "",
        data_nascimento:     pessoa.data_nascimento   || "",
        rg:                  pessoa.rg                || "",
        sexo:                pessoa.sexo              || "",
        cnpj:                pessoa.cnpj              || "",
        razao_social:        pessoa.razao_social      || "",
        nome_fantasia:       pessoa.nome_fantasia     || "",
        inscricao_estadual:  pessoa.inscricao_estadual  || "",
        inscricao_municipal: pessoa.inscricao_municipal || "",
        email:               pessoa.email             || "",
        telefone:            pessoa.telefone          || "",
        celular:             pessoa.celular           || "",
        cep:                 pessoa.cep               || "",
        logradouro:          pessoa.logradouro        || "",
        numero:              pessoa.numero            || "",
        complemento:         pessoa.complemento       || "",
        bairro:              pessoa.bairro            || "",
        cidade:              pessoa.cidade            || "",
        estado:              pessoa.estado            || "",
      };
    }
  });

  // ── Máscaras ────────────────────────────────────────────────────────────────
  function maskCPF(v) {
    return v.replace(/\D/g, "").slice(0, 11)
      .replace(/(\d{3})(\d)/, "$1.$2")
      .replace(/(\d{3})\.(\d{3})(\d)/, "$1.$2.$3")
      .replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, "$1.$2.$3-$4");
  }

  function maskCNPJ(v) {
    return v.replace(/\D/g, "").slice(0, 14)
      .replace(/(\d{2})(\d)/, "$1.$2")
      .replace(/(\d{2})\.(\d{3})(\d)/, "$1.$2.$3")
      .replace(/(\d{2})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3/$4")
      .replace(/(\d{4})(\d{1,2})$/, "$1-$2");
  }

  function maskCEP(v) {
    return v.replace(/\D/g, "").slice(0, 8).replace(/(\d{5})(\d{1,3})$/, "$1-$2");
  }

  function maskPhone(v) {
    const d = v.replace(/\D/g, "").slice(0, 11);
    if (d.length <= 10) return d.replace(/(\d{2})(\d{4})(\d{0,4})/, "($1) $2-$3").replace(/-$/, "");
    return d.replace(/(\d{2})(\d{5})(\d{0,4})/, "($1) $2-$3").replace(/-$/, "");
  }

  function onCPF(e) {
    form.cpf = maskCPF(e.target.value);
    e.target.value = form.cpf;
  }
  function onCNPJ(e) {
    form.cnpj = maskCNPJ(e.target.value);
    e.target.value = form.cnpj;
  }
  function onCEP(e) {
    form.cep = maskCEP(e.target.value);
    e.target.value = form.cep;
  }
  function onTelefone(e) {
    form.telefone = maskPhone(e.target.value);
    e.target.value = form.telefone;
  }
  function onCelular(e) {
    form.celular = maskPhone(e.target.value);
    e.target.value = form.celular;
  }

  // ── Busca CEP (ViaCEP) ──────────────────────────────────────────────────────
  async function buscarCEP() {
    const cepLimpo = form.cep.replace(/\D/g, "");
    if (cepLimpo.length !== 8) return;
    try {
      buscandoCEP = true;
      const res = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`);
      const dados = await res.json();
      if (dados.erro) {
        erros.cep = "CEP não encontrado.";
        return;
      }
      form.logradouro = dados.logradouro || form.logradouro;
      form.bairro     = dados.bairro     || form.bairro;
      form.cidade     = dados.localidade || form.cidade;
      form.estado     = dados.uf         || form.estado;
      delete erros.cep;
    } catch {
      // Falha silenciosa — ViaCEP indisponível
    } finally {
      buscandoCEP = false;
    }
  }

  // ── Validação client-side ───────────────────────────────────────────────────
  function validar() {
    const e = {};

    if (form.tipo === "fisica") {
      if (!form.nome.trim())                              e.nome = "Nome é obrigatório.";
      if (!form.cpf.trim())                              e.cpf  = "CPF é obrigatório.";
      else if (form.cpf.replace(/\D/g, "").length !== 11) e.cpf  = "CPF deve ter 11 dígitos.";
    } else {
      if (!form.razao_social.trim())                         e.razao_social = "Razão social é obrigatória.";
      if (!form.cnpj.trim())                                 e.cnpj         = "CNPJ é obrigatório.";
      else if (form.cnpj.replace(/\D/g, "").length !== 14)   e.cnpj         = "CNPJ deve ter 14 dígitos.";
    }

    if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
      e.email = "Informe um e-mail válido.";
    }

    if (form.cep && form.cep.replace(/\D/g, "").length !== 8) {
      e.cep = "CEP deve ter 8 dígitos.";
    }

    return e;
  }

  // ── Submit ──────────────────────────────────────────────────────────────────
  async function submeterFormulario(event) {
    event.preventDefault();

    erros = validar();
    if (Object.keys(erros).length > 0) return;

    enviando = true;

    const dados = {
      ...form,
      cpf:                 form.tipo === "fisica"   ? form.cpf               : null,
      nome:                form.tipo === "fisica"   ? form.nome              : null,
      data_nascimento:     form.tipo === "fisica"   ? form.data_nascimento   : null,
      rg:                  form.tipo === "fisica"   ? form.rg                : null,
      sexo:                form.tipo === "fisica"   ? form.sexo              : null,
      cnpj:                form.tipo === "juridica" ? form.cnpj              : null,
      razao_social:        form.tipo === "juridica" ? form.razao_social      : null,
      nome_fantasia:       form.tipo === "juridica" ? form.nome_fantasia     : null,
      inscricao_estadual:  form.tipo === "juridica" ? form.inscricao_estadual  : null,
      inscricao_municipal: form.tipo === "juridica" ? form.inscricao_municipal : null,
    };

    try {
      if (modo === "criar") {
        await pessoasService.criar(dados);
        toast.success($_("pessoas.sucesso_criar"));
      } else {
        await pessoasService.atualizar(pessoa.id, dados);
        toast.success($_("pessoas.sucesso_atualizar"));
      }
      goto("/pessoas");
    } catch (e) {
      if (e instanceof ApiError && e.errors) {
        // Mapear erros do backend campo a campo
        const mapa = {
          nome: "nome", cpf: "cpf", razao_social: "razao_social",
          cnpj: "cnpj", email: "email", cep: "cep",
        };
        const novosErros = {};
        for (const [campo, msgs] of Object.entries(e.errors)) {
          novosErros[mapa[campo] ?? campo] = Array.isArray(msgs) ? msgs[0] : msgs;
        }
        erros = novosErros;
        toast.error("Verifique os campos em destaque.");
      } else {
        toast.error(e.message ?? "Erro ao salvar pessoa.");
      }
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

  <form onsubmit={submeterFormulario} class="space-y-6" novalidate>
    <!-- Tipo de Pessoa -->
    <div class="card bg-base-100 shadow-lg border border-base-300/50">
      <div class="card-body p-6">
        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
          <Icon icon="heroicons:tag" class="w-6 h-6 text-primary" />
          {$_("pessoas.tipo_pessoa")}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <label class="relative cursor-pointer {modo === 'editar' ? 'opacity-60 cursor-not-allowed' : ''}">
            <input type="radio" name="tipo" class="peer sr-only" value="fisica"
              bind:group={form.tipo} disabled={modo === "editar"} />
            <div class="flex items-start gap-4 p-6 border-2 rounded-xl transition-all
              peer-checked:border-primary peer-checked:bg-primary/5
              border-base-300 {modo !== 'editar' ? 'hover:border-primary/50' : ''}">
              <div class="mt-0.5 w-5 h-5 rounded-full border-2 flex-shrink-0 transition-all
                {form.tipo === 'fisica' ? 'border-primary bg-primary scale-110' : 'border-base-300'}">
              </div>
              <div>
                <div class="flex items-center gap-2 mb-1">
                  <Icon icon="heroicons:user" class="w-5 h-5 text-primary" />
                  <span class="font-bold">{$_("pessoas.pessoa_fisica")}</span>
                </div>
                <p class="text-sm text-base-content/70">{$_("pessoas.pessoa_fisica_desc")}</p>
              </div>
            </div>
          </label>

          <label class="relative cursor-pointer {modo === 'editar' ? 'opacity-60 cursor-not-allowed' : ''}">
            <input type="radio" name="tipo" class="peer sr-only" value="juridica"
              bind:group={form.tipo} disabled={modo === "editar"} />
            <div class="flex items-start gap-4 p-6 border-2 rounded-xl transition-all
              peer-checked:border-secondary peer-checked:bg-secondary/5
              border-base-300 {modo !== 'editar' ? 'hover:border-secondary/50' : ''}">
              <div class="mt-0.5 w-5 h-5 rounded-full border-2 flex-shrink-0 transition-all
                {form.tipo === 'juridica' ? 'border-secondary bg-secondary scale-110' : 'border-base-300'}">
              </div>
              <div>
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
          {form.tipo === "fisica" ? $_("pessoas.dados_pessoais") : $_("pessoas.dados_empresa")}
        </h2>

        {#if form.tipo === "fisica"}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Nome -->
            <div class="form-control md:col-span-2">
              <label class="label" for="nome">
                <span class="label-text font-semibold">
                  <span class="text-error">*</span> {$_("pessoas.nome_completo")}
                </span>
              </label>
              <input id="nome" type="text" bind:value={form.nome}
                class="input input-bordered {erros.nome ? 'input-error' : ''}"
                placeholder={$_("pessoas.nome_completo_placeholder")} autocomplete="name" />
              {#if erros.nome}<p class="text-error text-xs mt-1">{erros.nome}</p>{/if}
            </div>

            <!-- CPF -->
            <div class="form-control">
              <label class="label" for="cpf">
                <span class="label-text font-semibold">
                  <span class="text-error">*</span> {$_("pessoas.cpf")}
                </span>
              </label>
              <input id="cpf" type="text" inputmode="numeric"
                value={form.cpf} oninput={onCPF}
                class="input input-bordered font-mono {erros.cpf ? 'input-error' : ''}"
                placeholder="000.000.000-00" autocomplete="off" />
              {#if erros.cpf}<p class="text-error text-xs mt-1">{erros.cpf}</p>{/if}
            </div>

            <!-- RG -->
            <div class="form-control">
              <label class="label" for="rg">
                <span class="label-text font-semibold">{$_("pessoas.rg")}</span>
              </label>
              <input id="rg" type="text" bind:value={form.rg}
                class="input input-bordered font-mono" placeholder="00.000.000-0" />
            </div>

            <!-- Data Nascimento -->
            <div class="form-control">
              <label class="label" for="dataNascimento">
                <span class="label-text font-semibold">{$_("pessoas.data_nascimento")}</span>
              </label>
              <input id="dataNascimento" type="date" bind:value={form.data_nascimento}
                class="input input-bordered" />
            </div>

            <!-- Sexo -->
            <div class="form-control">
              <label class="label" for="sexo">
                <span class="label-text font-semibold">{$_("pessoas.sexo")}</span>
              </label>
              <select id="sexo" bind:value={form.sexo} class="select select-bordered">
                <option value="">Selecione...</option>
                <option value="masculino">{$_("pessoas.masculino")}</option>
                <option value="feminino">{$_("pessoas.feminino")}</option>
                <option value="outro">{$_("pessoas.outro")}</option>
              </select>
            </div>
          </div>

        {:else}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Razão Social -->
            <div class="form-control md:col-span-2">
              <label class="label" for="razaoSocial">
                <span class="label-text font-semibold">
                  <span class="text-error">*</span> {$_("pessoas.razao_social")}
                </span>
              </label>
              <input id="razaoSocial" type="text" bind:value={form.razao_social}
                class="input input-bordered {erros.razao_social ? 'input-error' : ''}"
                placeholder="Nome empresarial completo" autocomplete="organization" />
              {#if erros.razao_social}<p class="text-error text-xs mt-1">{erros.razao_social}</p>{/if}
            </div>

            <!-- Nome Fantasia -->
            <div class="form-control">
              <label class="label" for="nomeFantasia">
                <span class="label-text font-semibold">{$_("pessoas.nome_fantasia")}</span>
              </label>
              <input id="nomeFantasia" type="text" bind:value={form.nome_fantasia}
                class="input input-bordered" placeholder="Nome comercial" />
            </div>

            <!-- CNPJ -->
            <div class="form-control">
              <label class="label" for="cnpj">
                <span class="label-text font-semibold">
                  <span class="text-error">*</span> {$_("pessoas.cnpj")}
                </span>
              </label>
              <input id="cnpj" type="text" inputmode="numeric"
                value={form.cnpj} oninput={onCNPJ}
                class="input input-bordered font-mono {erros.cnpj ? 'input-error' : ''}"
                placeholder="00.000.000/0000-00" autocomplete="off" />
              {#if erros.cnpj}<p class="text-error text-xs mt-1">{erros.cnpj}</p>{/if}
            </div>

            <!-- Inscrição Estadual -->
            <div class="form-control">
              <label class="label" for="inscricaoEstadual">
                <span class="label-text font-semibold">{$_("pessoas.inscricao_estadual")}</span>
              </label>
              <input id="inscricaoEstadual" type="text" bind:value={form.inscricao_estadual}
                class="input input-bordered" />
            </div>

            <!-- Inscrição Municipal -->
            <div class="form-control">
              <label class="label" for="inscricaoMunicipal">
                <span class="label-text font-semibold">{$_("pessoas.inscricao_municipal")}</span>
              </label>
              <input id="inscricaoMunicipal" type="text" bind:value={form.inscricao_municipal}
                class="input input-bordered" />
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
          <!-- E-mail -->
          <div class="form-control md:col-span-2">
            <label class="label" for="email">
              <span class="label-text font-semibold">{$_("pessoas.email")}</span>
            </label>
            <input id="email" type="email" bind:value={form.email}
              class="input input-bordered {erros.email ? 'input-error' : ''}"
              placeholder="email@exemplo.com" autocomplete="email" />
            {#if erros.email}<p class="text-error text-xs mt-1">{erros.email}</p>{/if}
          </div>

          <!-- Telefone -->
          <div class="form-control">
            <label class="label" for="telefone">
              <span class="label-text font-semibold">{$_("pessoas.telefone")}</span>
            </label>
            <input id="telefone" type="tel" inputmode="numeric"
              value={form.telefone} oninput={onTelefone}
              class="input input-bordered font-mono" placeholder="(00) 0000-0000"
              autocomplete="tel" />
          </div>

          <!-- Celular -->
          <div class="form-control">
            <label class="label" for="celular">
              <span class="label-text font-semibold">{$_("pessoas.celular")}</span>
            </label>
            <input id="celular" type="tel" inputmode="numeric"
              value={form.celular} oninput={onCelular}
              class="input input-bordered font-mono" placeholder="(00) 00000-0000"
              autocomplete="tel" />
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
          <!-- CEP com auto-preenchimento -->
          <div class="form-control">
            <label class="label" for="cep">
              <span class="label-text font-semibold">{$_("pessoas.cep")}</span>
              {#if buscandoCEP}
                <span class="label-text-alt text-primary flex items-center gap-1">
                  <span class="loading loading-spinner loading-xs"></span> Buscando...
                </span>
              {/if}
            </label>
            <input id="cep" type="text" inputmode="numeric"
              value={form.cep} oninput={onCEP} onblur={buscarCEP}
              class="input input-bordered font-mono {erros.cep ? 'input-error' : ''}"
              placeholder="00000-000" maxlength="9" />
            {#if erros.cep}<p class="text-error text-xs mt-1">{erros.cep}</p>{/if}
          </div>

          <!-- Logradouro -->
          <div class="form-control md:col-span-2 lg:col-span-3">
            <label class="label" for="logradouro">
              <span class="label-text font-semibold">{$_("pessoas.logradouro")}</span>
            </label>
            <input id="logradouro" type="text" bind:value={form.logradouro}
              class="input input-bordered" placeholder="Rua, Avenida, etc."
              autocomplete="street-address" />
          </div>

          <!-- Número -->
          <div class="form-control">
            <label class="label" for="numero">
              <span class="label-text font-semibold">{$_("pessoas.numero")}</span>
            </label>
            <input id="numero" type="text" bind:value={form.numero}
              class="input input-bordered" placeholder="123" />
          </div>

          <!-- Complemento -->
          <div class="form-control">
            <label class="label" for="complemento">
              <span class="label-text font-semibold">{$_("pessoas.complemento")}</span>
            </label>
            <input id="complemento" type="text" bind:value={form.complemento}
              class="input input-bordered" placeholder="Apto, Sala, etc." />
          </div>

          <!-- Bairro -->
          <div class="form-control">
            <label class="label" for="bairro">
              <span class="label-text font-semibold">{$_("pessoas.bairro")}</span>
            </label>
            <input id="bairro" type="text" bind:value={form.bairro}
              class="input input-bordered" autocomplete="address-level3" />
          </div>

          <!-- Cidade -->
          <div class="form-control">
            <label class="label" for="cidade">
              <span class="label-text font-semibold">{$_("pessoas.cidade")}</span>
            </label>
            <input id="cidade" type="text" bind:value={form.cidade}
              class="input input-bordered" autocomplete="address-level2" />
          </div>

          <!-- Estado -->
          <div class="form-control">
            <label class="label" for="estado">
              <span class="label-text font-semibold">{$_("pessoas.estado")}</span>
            </label>
            <select id="estado" bind:value={form.estado} class="select select-bordered">
              <option value="">Selecione...</option>
              {#each ["AC","AL","AP","AM","BA","CE","DF","ES","GO","MA","MT","MS","MG","PA","PB","PR","PE","PI","RJ","RN","RS","RO","RR","SC","SP","SE","TO"] as uf}
                <option value={uf}>{uf}</option>
              {/each}
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Botões -->
    <div class="flex gap-4 justify-end pb-4">
      <a href="/pessoas" class="btn btn-ghost">{$_("common.cancel")}</a>
      <button type="submit" class="btn btn-primary min-w-32" disabled={enviando}>
        {#if enviando}
          <span class="loading loading-spinner loading-sm"></span>
          Salvando...
        {:else}
          <Icon icon="heroicons:check" class="w-5 h-5" />
          {$_("common.save")}
        {/if}
      </button>
    </div>
  </form>
</div>
