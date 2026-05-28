# Frontend - SvelteKit 🚀

Admin dashboard moderno construído com **SvelteKit 2** e integrado com o template **Takeka** (ichsanputr/takeka).

## 🎨 Stack Tecnológica

- **Svelte 5** - Framework JavaScript reativo
- **SvelteKit 2** - Framework full-stack
- **Tailwind CSS 4** - Framework CSS utilitário
- **DaisyUI 5** - Componentes UI
- **Melt UI** - Componentes headless
- **Chart.js** - Gráficos interativos
- **Leaflet** - Mapas interativos
- **svelte-i18n** - Internacionalização
- **Motion** - Animações

## 🚀 Desenvolvimento

### Iniciar servidor de desenvolvimento
```bash
npm run dev
```

O servidor estará disponível em: http://localhost:5174/

### Build de produção
```bash
npm run build
```

### Preview da build
```bash
npm run preview
```

## 📁 Estrutura do Projeto

```
src/
├── lib/
│   ├── components/      # Componentes do Takeka
│   ├── pages/          # Páginas completas
│   ├── data/           # Dados de exemplo
│   ├── i18n/           # Internacionalização (en, id, es, ko)
│   ├── stores/         # Stores Svelte (tema, etc.)
│   ├── utils/          # Utilitários
│   └── images/         # Imagens
├── routes/             # Rotas do SvelteKit
│   ├── +layout.svelte  # Layout principal
│   ├── +page.svelte    # Home
│   ├── users/          # Gestão de usuários
│   ├── products/       # Gestão de produtos
│   ├── analytics/      # Analytics
│   ├── charts/         # Gráficos
│   ├── projects/       # Projetos (Gantt/Kanban)
│   ├── profile/        # Perfil
│   ├── maps/           # Mapas
│   ├── schedule/       # Agenda
│   ├── settings/       # Configurações
│   └── auth/           # Autenticação (login/register)
├── app.css             # Estilos globais
└── app.html            # HTML base
```

## 🎯 Recursos do Template Takeka

### ✨ Componentes Disponíveis
- **DashboardLayout** - Layout principal com sidebar e navbar
- **Sidebar** - Menu lateral responsivo
- **Topbar** - Barra superior com busca, notificações, perfil
- **LoadingSpinner** - Spinner customizado
- **Charts** - Componentes de gráficos
- **Calendar** - Calendário completo
- **Gantt** - Gráficos Gantt
- **Grid** - Grid com drag & drop
- **Maps** - Mapas interativos

### 🌍 Internacionalização
- 🇺🇸 Inglês (en)
- 🇮🇩 Indonésio (id)
- 🇪🇸 Espanhol (es)
- 🇰🇷 Coreano (ko)

### 🌙 Temas
- Dark/Light mode
- DaisyUI themes: light, dark, cupcake, corporate, business
- Persistência em localStorage

## 📝 Páginas Disponíveis

Acesse as seguintes rotas:

- `/` - Home
- `/dashboard-takeka` - Dashboard principal do Takeka
- `/users` - Gestão de usuários
- `/products` - Gestão de produtos
- `/analytics` - Analytics e métricas
- `/charts` - Gráficos interativos
- `/projects` - Gestão de projetos
- `/profile` - Perfil do usuário
- `/maps` - Mapas interativos
- `/schedule` - Agenda/calendário
- `/settings` - Configurações
- `/auth/login` - Login
- `/auth/register` - Registro

## 📚 Documentação Adicional

- **[INTEGRACAO_TAKEKA.md](INTEGRACAO_TAKEKA.md)** - Documentação completa da integração
- **[RESUMO_INTEGRACAO_TAKEKA.md](RESUMO_INTEGRACAO_TAKEKA.md)** - Resumo da implementação
- **[COMANDOS_TAKEKA.md](COMANDOS_TAKEKA.md)** - Comandos úteis e guia rápido
- **[TEMPLATE_ADMIN.md](TEMPLATE_ADMIN.md)** - Documentação do template admin DaisyUI

## 🔗 Links Úteis

- [Template Takeka Original](https://github.com/ichsanputr/takeka)
- [Demo Takeka](https://takeka.ichsan.site/)
- [Documentação SvelteKit](https://kit.svelte.dev/)
- [Documentação DaisyUI](https://daisyui.com/)
- [Documentação Tailwind CSS](https://tailwindcss.com/)

## 🛠️ Instalação

```bash
# Instalar dependências
npm install

# Iniciar servidor de desenvolvimento
npm run dev
```

## 📦 Dependências Principais

```json
{
  "dependencies": {
    "@iconify/svelte": "^5.0.0",
    "@melt-ui/svelte": "^0.86.6",
    "chart.js": "^4.5.0",
    "daisyui": "^5.0.43",
    "leaflet": "^1.9.4",
    "motion": "^12.18.1",
    "svelte-i18n": "^4.0.1",
    "wx-svelte-gantt": "^2.1.1",
    "wx-svelte-grid": "^2.1.5"
  }
}
```

## 🎯 Próximos Passos

1. **Integrar com Backend Laravel**
   - Conectar com API REST
   - Substituir dados mock por dados reais

2. **Adicionar Autenticação**
   - Implementar login/logout
   - Proteger rotas privadas

3. **Traduzir para Português**
   - Criar arquivo `pt-BR.json`
   - Traduzir interface

4. **Customizar para Projeto**
   - Adaptar componentes
   - Adicionar páginas específicas

## 📄 Licença

Este projeto utiliza o template Takeka, que é licenciado sob MIT License.

---

**Desenvolvido com** ❤️ **usando SvelteKit e Template Takeka**
