<div
  x-show="$store.sidebar.isMobileOpen"
  x-cloak
  style="display: none"
  @click="$store.sidebar.toggleMobileOpen()"
  class="fixed inset-0 z-50 h-screen w-full bg-gray-900/50 xl:hidden"
></div>
