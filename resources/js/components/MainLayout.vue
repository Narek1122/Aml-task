<template>
    <div id="app" class="flex flex-col min-h-screen">
      <nav class="bg-gray-400 shadow-lg">
        <div class="container mx-auto px-4">
          <div class="flex justify-between items-center py-4">
            <div class="text-2xl font-semibold text-gray-100">
              <router-link to="/">Task</router-link>
            </div>
            <button @click="toggleMenu" class="block lg:hidden text-gray-100 focus:outline-none">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
              </svg>
            </button>
            <div class="hidden lg:flex lg:items-center lg:justify-end lg:flex-1">
              <router-link v-if="!isAuthenticated" to="/login" class="text-gray-100 hover:text-gray-200 px-4" :class="{ 'text-xl': isActiveRoute('/login') }">Login</router-link>
              <router-link v-if="!isAuthenticated" to="/register" class="text-gray-100 hover:text-gray-200 px-4" :class="{ 'text-xl': isActiveRoute('/register') }">Register</router-link>
              <router-link v-if="isAuthenticated" to="/profile" class="text-gray-100 hover:text-gray-200 px-4" :class="{ 'text-xl': isActiveRoute('/profile') }">Profile</router-link>
              <button v-if="isAuthenticated" @click="logout" class="text-gray-100 hover:text-gray-200 px-4">Logout</button>
            </div>
          </div>
        </div>
        <div v-show="isMenuOpen" class="lg:hidden">
          <router-link v-if="!isAuthenticated" to="/login" class="block text-gray-100 hover:text-gray-200 py-2 px-4 border-b border-gray-700" :class="{ 'text-xl': isActiveRoute('/login') }">Login</router-link>
          <router-link v-if="!isAuthenticated" to="/register" class="block text-gray-100 hover:text-gray-200 py-2 px-4 border-b border-gray-700" :class="{ 'text-xl': isActiveRoute('/register') }">Register</router-link>
          <router-link v-if="isAuthenticated" to="/profile" class="block text-gray-100 hover:text-gray-200 py-2 px-4 border-b border-gray-700" :class="{ 'text-xl': isActiveRoute('/profile') }">Profile</router-link>
          <button v-if="isAuthenticated" @click="logout" class="block text-gray-100 hover:text-gray-200 py-2 px-4 border-b border-gray-700">Logout</button>
        </div>
      </nav>
      <router-view />
    </div>
  </template>

  <script>
  import { ref, computed, watch } from 'vue';
  import { useRoute, useRouter } from 'vue-router';

  export default {
    setup() {
      const isMenuOpen = ref(false);
      const route = useRoute();
      const router = useRouter();

      const isAuthenticated = ref(!!localStorage.getItem('token'));

      const toggleMenu = () => {
        isMenuOpen.value = !isMenuOpen.value;
      };

      const isActiveRoute = (routePath) => {
        return route.path === routePath;
      };

      const logout = () => {
        localStorage.removeItem('token');
        isAuthenticated.value = false; // Manually update isAuthenticated
        router.push('/login');  // Redirect to login page
      };

      // Watch for route changes
      watch(
        () => route.path,
        () => {
          // Re-check authentication status on every route change
          isAuthenticated.value = !!localStorage.getItem('token');
        }
      );

      return {
        isMenuOpen,
        isAuthenticated,
        toggleMenu,
        isActiveRoute,
        logout
      };
    }
  };
  </script>

  <style scoped>
  .main-content {
    margin-top: 20px;
  }
  </style>
