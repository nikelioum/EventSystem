<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">Event Details</h2>
      <div class="space-y-4">
        <div>
          <span class="block text-sm font-medium text-gray-700">ID</span>
          <p class="text-gray-900">{{ event?.id }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Title</span>
          <p class="text-gray-900">{{ event?.title }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Description</span>
          <p class="text-gray-900">{{ event?.description || 'N/A' }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Event Date</span>
          <p class="text-gray-900">{{ event?.event_date ? formatDateTime(event.event_date) : 'N/A' }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Location</span>
          <p class="text-gray-900">{{ event?.location }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Capacity</span>
          <p class="text-gray-900">{{ event?.capacity }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Registrations</span>
          <p class="text-gray-900">{{ event?.current_registrations_count }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Status</span>
          <p class="text-gray-900">{{ event?.status }}</p>
        </div>
        <!-- <div>
          <span class="block text-sm font-medium text-gray-700">Created At</span>
          <p class="text-gray-900">{{ event?.created_at }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Updated At</span>
          <p class="text-gray-900">{{ event?.updated_at }}</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-gray-700">Deleted At</span>
          <p class="text-gray-900">{{ event?.deleted_at || 'N/A' }}</p>
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
// Define TypeScript interface for event data
interface Event {
  id: number;
  title: string;
  description: string | null;
  event_date: string;
  location: string;
  capacity: number;
  current_registrations_count: number;
  status: 'draft' | 'published' | 'cancelled';
  created_at: string;
  updated_at: string;
  deleted_at: string | null;
}


function formatDateTime(dateString: string): string {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${day}-${month}-${year} ${hours}:${minutes}`;
}

// Define props with TypeScript
const { isOpen, event } = defineProps<{
  isOpen: boolean;
  event: Event | null;
}>();

// Define emits
const emit = defineEmits<{
  (e: 'close'): void;
}>();
</script>