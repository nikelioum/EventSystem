<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">Registration Details</h2>
      <div class="space-y-4">
        <div>
          <span class="block text-sm font-medium text-gray-700">ID</span>
          <p class="text-gray-900">{{ registration?.id }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Event</span>
          <p class="text-gray-900">{{ registration?.event?.title || 'N/A' }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">User Name</span>
          <p class="text-gray-900">{{ registration?.user?.name || 'N/A' }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">User Email</span>
          <p class="text-gray-900">{{ registration?.user?.email || 'N/A' }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Registered At</span>
          <p class="text-gray-900">{{ formattedCreatedAt }}</p>
        </div>
        <!-- <div>
          <span class="block text-sm font-medium text-gray-700">Updated At</span>
          <p class="text-gray-900">{{ registration?.updated_at || 'N/A' }}</p>
        </div> -->
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
import { computed } from 'vue'
// Define TypeScript interface for registration data
interface Registration {
  id: number;
  event_id: number;
  user_id: number;
  created_at: string;
  updated_at?: string;
  event: { id: number; title: string } | null;
  user: { id: number; name: string; email: string } | null;
}

// Define props with TypeScript
const { isOpen, registration } = defineProps<{
  isOpen: boolean;
  registration: Registration | null;
}>();

// Define emits
const emit = defineEmits<{
  (e: 'close'): void;
}>();

// Format created_at
const formattedCreatedAt = computed(() => {
  if (!registration?.created_at) return 'N/A'
  const date = new Date(registration.created_at)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${day}-${month}-${year} ${hours}:${minutes}`
})
</script>