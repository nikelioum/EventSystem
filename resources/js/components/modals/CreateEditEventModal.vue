<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Edit Event' : 'Create Event' }}</h2>
      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
          <input
            v-model="form.title"
            id="title"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.title }"
          />
          <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
        </div>
        <div class="mb-4">
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            v-model="form.description"
            id="description"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.description }"
          ></textarea>
          <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
        </div>
        <div class="mb-4">
          <label for="event_date" class="block text-sm font-medium text-gray-700">Event Date</label>
          <input
            v-model="form.event_date"
            id="event_date"
            type="datetime-local"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.event_date }"
          />
          <p v-if="form.errors.event_date" class="text-red-500 text-sm mt-1">{{ form.errors.event_date }}</p>
        </div>
        <div class="mb-4">
          <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
          <input
            v-model="form.location"
            id="location"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.location }"
          />
          <p v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</p>
        </div>
        <div class="mb-4">
          <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
          <input
            v-model.number="form.capacity"
            id="capacity"
            type="number"
            min="1"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.capacity }"
          />
          <p v-if="form.errors.capacity" class="text-red-500 text-sm mt-1">{{ form.errors.capacity }}</p>
        </div>
        <div class="mb-4">
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            v-model="form.status"
            id="status"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.status }"
          >
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <p v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</p>
        </div>
        <div class="flex justify-end space-x-2">
          <button
            type="button"
            @click="emit('close')"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 cursor-pointer"
          >
            {{ isEditing ? 'Update' : 'Create' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { watch } from 'vue';

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

// Define props with TypeScript
const { isOpen, isEditing, event } = defineProps<{
  isOpen: boolean;
  isEditing: boolean;
  event?: Event;
}>();

// Define emits
const emit = defineEmits<{
  (e: 'close'): void;
}>();

// Form state using Inertia's useForm
const form = useForm<{
  id?: number;
  title: string;
  description: string | null;
  event_date: string;
  location: string;
  capacity: number;
  status: 'draft' | 'published' | 'cancelled';
  errors: Record<string, string>;
}>({
  id: undefined,
  title: '',
  description: null,
  event_date: '',
  location: '',
  capacity: 1,
  status: 'draft',
  errors: {},
});

// Convert backend datetime string (e.g. "2025-07-19 14:30:00") to "2025-07-19T14:30" for datetime-local input
function toDatetimeLocal(dateString: string): string {
  if (!dateString) return '';
  const date = new Date(dateString.replace(' ', 'T'));
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

// Watch for event prop changes to populate form for editing
watch(
  () => event,
  (newEvent) => {
    if (newEvent && isEditing) {
      form.reset();
      form.id = newEvent.id;
      form.title = newEvent.title;
      form.description = newEvent.description;
      // Use formatted event_date for datetime-local input
      form.event_date = toDatetimeLocal(newEvent.event_date);
      form.location = newEvent.location;
      form.capacity = newEvent.capacity;
      form.status = newEvent.status;
    } else {
      form.reset();
    }
  },
  { immediate: true }
);

// Submit form (create or update)
const submitForm = () => {
  const routeName = isEditing ? 'admin.events.update' : 'admin.events.store';
  const method = isEditing ? 'put' : 'post';
  router[method](route(routeName, isEditing ? form.id : undefined), {
    title: form.title,
    description: form.description,
    event_date: form.event_date,
    location: form.location,
    capacity: form.capacity,
    status: form.status,
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      emit('close');
      form.reset();
      router.reload({ only: ['events', 'flash'] });
    },
    onError: (errors: Record<string, string>) => {
      form.errors = errors;
    },
    onFinish: () => {
      router.reload({ only: ['events', 'flash'] });
    },
  });
};
</script>
