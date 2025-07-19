<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">User Details</h2>
      <div class="space-y-4">
        <div>
          <span class="block text-sm font-medium text-gray-700">ID</span>
          <p class="text-gray-900">{{ user?.id }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Name</span>
          <p class="text-gray-900">{{ user?.name }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Email</span>
          <p class="text-gray-900">{{ user?.email }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Role</span>
          <p class="text-gray-900">{{ user?.role }}</p>
        </div>
        <div class="flex justify-end">
          <button
            @click="emit('close')"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 cursor-pointer"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Define TypeScript interface for user data
interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
}

// Define props with TypeScript
const { isOpen, user } = defineProps<{
  isOpen: boolean;
  user: User | null;
}>();

// Define emits with TypeScript
const emit = defineEmits<{
  (e: 'close'): void;
}>();
</script>