<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import Icon from '@iconify/svelte';
  import { _, isLoading } from 'svelte-i18n';
  import { generateUserAvatar } from '../utils/avatar.js';
  import { users } from '../data/dashboard.js';
  import { motionInView, staggerAnimate } from '../utils/motion.js';
  import ThemeLogo from './ThemeLogo.svelte';
  
  export let isOpen = false;
  
  let menuItemsElements = [];
  
  // Get current route from page store
  $: currentRoute = $page.url.pathname;
  
  // Create translated menu items with fallbacks
  $: translatedMenuItems = [
    { path: '/', icon: 'heroicons:home', name: 'Dashboard' },
    { path: '/pessoas', icon: 'heroicons:users', name: 'Pessoas' },
    { path: '/pessoas/criar', icon: 'heroicons:user-plus', name: 'Nova Pessoa' }
  ];
  
  onMount(() => {
    // Animate menu items on mount
    if (menuItemsElements.length > 0) {
      staggerAnimate(menuItemsElements, 'fadeInLeft', { delay: 0.1 });
    }
  });
  
  function closeSidebar() {
    isOpen = false;
  }
  
  // Handle menu item click with animation
  function handleMenuClick(path) {
    goto(path);
    closeSidebar();
  }
  
  // Add logout function
  function handleLogout() {
    closeSidebar();
    setTimeout(() => {
      alert('Logout function - implementar autenticação');
    }, 150);
  }
</script>

<!-- Mobile backdrop -->
{#if isOpen}
  <div 
    class="fixed inset-0 z-40 bg-neutral bg-opacity-75 lg:hidden transition-opacity duration-300"
    on:click={closeSidebar}
    on:keydown={(e) => e.key === 'Enter' || e.key === ' ' ? closeSidebar() : null}
    role="button"
    tabindex="-1"
  ></div>
{/if}

<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-base-100 shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 {isOpen ? 'translate-x-0' : '-translate-x-full'} flex flex-col h-full">
  <!-- Logo section -->
  <div class="flex items-center justify-center h-16 px-6 border-b border-base-300 flex-shrink-0">
    <ThemeLogo width="w-32" height="h-8" alt="Pessoas CRUD" />
  </div>
  
  <!-- Navigation section - takes up remaining space -->
  <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
    {#each translatedMenuItems as item, index}
      <a
        bind:this={menuItemsElements[index]}
        href={item.path}
        class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {currentRoute === item.path ? 'bg-blue-500/10 text-blue-600 border border-blue-500/20' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'}"
        on:click|preventDefault={() => handleMenuClick(item.path)}
        use:motionInView={{ animation: 'fadeInLeft', delay: index * 0.1 }}
      >
        <Icon icon={item.icon} class="w-5 h-5 mr-3" />
        {item.name}
        {#if currentRoute === item.path}
          <div class="ml-auto w-2 h-2 bg-blue-500 rounded-full"></div>
        {/if}
      </a>
    {/each}
  </nav>
  
  <!-- User section - always at bottom -->
  <div class="border-t border-base-300 p-4 flex-shrink-0 mt-auto">
    <div class="flex items-center">
      <img 
        src="https://api.dicebear.com/7.x/avataaars/svg?seed=John" 
        alt="User avatar"
        class="w-10 h-10 rounded-full object-cover bg-base-200"
      />
      <div class="ml-3 flex-1 min-w-0">
        <p class="text-sm font-medium text-base-content truncate">John Doe</p>
        <p class="text-xs text-base-content/60 truncate">john.doe@example.com</p>
      </div>
      <div class="flex items-center gap-2">
        <button 
          class="p-1.5 rounded-md text-base-content/60 hover:text-red-600 hover:bg-red-100 transition-colors"
          on:click={handleLogout}
          title="Logout"
        >
          <Icon icon="heroicons:arrow-right-on-rectangle" class="w-5 h-5" />
        </button>
        <button class="p-1.5 rounded-md text-base-content/60 hover:text-base-content hover:bg-base-200">
          <Icon icon="heroicons:ellipsis-vertical" class="w-5 h-5" />
        </button>
      </div>
    </div>
  </div>
</div>
