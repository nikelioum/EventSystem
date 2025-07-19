<template>
  <nav class="bg-white border-b shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
        <!-- Logo -->
        <div class="flex-shrink-0">
          <a href="/admin/dashboard" class="text-xl font-bold text-gray-800"
            >EventSystem</a
          >
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center space-x-8">
          <Link href="/admin/dashboard" class="text-gray-700 hover:text-blue-500"
            >Dashboard</Link
          >
          <Link href="/admin/users" class="text-gray-700 hover:text-blue-500">Users</Link>
          <Link href="/admin/events" class="text-gray-700 hover:text-blue-500"
            >Events</Link
          >
          <Link href="/admin/registrations" class="text-gray-700 hover:text-blue-500"
            >Registrations</Link
          >

          <!-- User Dropdown Desktop -->
          <div v-if="auth?.user" class="relative" @click="toggleUserMenu">
            <button
              class="flex items-center text-gray-700 hover:text-blue-500 focus:outline-none cursor-pointer"
            >
              <span class="mr-1">{{ auth.user.name }}</span>
              <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  :d="userMenuOpen ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"
                />
              </svg>
            </button>
            <div
              v-if="userMenuOpen"
              class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-md z-50"
            >
              <button
                @click="logout"
                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer"
              >
                Logout
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile Hamburger -->
        <div class="md:hidden">
          <button
            @click="toggleMenu"
            type="button"
            class="text-gray-700 focus:outline-none cursor-pointer"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                :d="mobileUserMenuOpen ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div v-if="isOpen" class="md:hidden px-4 pt-2 pb-4 space-y-1 bg-white border-t">
      <Link href="/admin/dashboard" class="block text-gray-700 hover:text-blue-500"
        >Dashboard</Link
      >
      <Link href="/admin/users" class="block text-gray-700 hover:text-blue-500"
        >Users</Link
      >
      <Link href="/admin/events" class="block text-gray-700 hover:text-blue-500"
        >Events</Link
      >
      <Link href="/admin/registrations" class="block text-gray-700 hover:text-blue-500"
        >Registrations</Link
      >

      <!-- Mobile User Dropdown -->
      <div v-if="auth?.user" class="mt-2">
        <button
          @click="toggleMobileUserMenu"
          class="flex items-center w-full justify-between text-gray-700 hover:text-blue-500"
        >
          <span>{{ auth.user.name }}</span>
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <div v-if="mobileUserMenuOpen" class="mt-2 border rounded bg-white shadow-md">
          <button
            @click="logout"
            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          >
            Logout
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const auth = usePage().props.auth;

const isOpen = ref(false);
const userMenuOpen = ref(false);
const mobileUserMenuOpen = ref(false);

function toggleMenu() {
  isOpen.value = !isOpen.value;
}

function toggleUserMenu() {
  userMenuOpen.value = !userMenuOpen.value;
}

function toggleMobileUserMenu() {
  mobileUserMenuOpen.value = !mobileUserMenuOpen.value;
}

function logout() {
  router.post(
    "/logout",
    {},
    {
      onSuccess: () => {
        userMenuOpen.value = false;
        mobileUserMenuOpen.value = false;
      },
    }
  );
}
</script>

<style scoped>
/* Optional: Add smooth animation */
</style>
