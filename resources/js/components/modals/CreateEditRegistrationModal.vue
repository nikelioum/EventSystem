<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Edit Registration' : 'Create Registration' }}</h2>
      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label for="event_id" class="block text-sm font-medium text-gray-700">Event</label>
          <select
            v-model="form.event_id"
            id="event_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.event_id }"
          >
            <option value="" disabled>Select an event</option>
            <option v-for="event in events" :key="event.id" :value="event.id">{{ event.title }}</option>
          </select>
          <p v-if="form.errors.event_id" class="text-red-500 text-sm mt-1">{{ form.errors.event_id }}</p>
        </div>
        <div class="mb-4">
          <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
          <select
            v-model="form.user_id"
            id="user_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.user_id }"
          >
            <option value="" disabled>Select a user</option>
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }} ({{ user.email }})</option>
          </select>
          <p v-if="form.errors.user_id" class="text-red-500 text-sm mt-1">{{ form.errors.user_id }}</p>
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

// Define TypeScript interface for event data
interface Event {
  id: number;
  title: string;
}

// Define TypeScript interface for user data
interface User {
  id: number;
  name: string;
  email: string;
}

// Define props with TypeScript
const { isOpen, isEditing, registration, events, users } = defineProps<{
  isOpen: boolean;
  isEditing: boolean;
  registration?: Registration;
  events: Event[];
  users: User[];
}>();

// Define emits
const emit = defineEmits<{
  (e: 'close'): void;
}>();

// Form state using Inertia's useForm
const form = useForm<{
  id?: number;
  event_id: number | null;
  user_id: number | null;
  errors: Record<string, string>;
}>({
  id: undefined,
  event_id: null,
  user_id: null,
  errors: {},
});

// Watch for registration prop changes to populate form for editing
watch(
  () => registration,
  (newRegistration) => {
    if (newRegistration && isEditing) {
      form.reset();
      form.id = newRegistration.id;
      form.event_id = newRegistration.event_id;
      form.user_id = newRegistration.user_id;
    } else {
      form.reset();
    }
  },
  { immediate: true }
);

// Submit form (create or update)
const submitForm = () => {
  const routeName = isEditing ? 'admin.registrations.update' : 'admin.registrations.store';
  const method = isEditing ? 'put' : 'post';
  router[method](route(routeName, isEditing ? form.id : undefined), {
    event_id: form.event_id,
    user_id: form.user_id,
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      emit('close');
      form.reset();
      router.reload({ only: ['registrations', 'flash'] });
    },
    onError: (errors: Record<string, string>) => {
      form.errors = errors;
    },
    onFinish: () => {
      router.reload({ only: ['registrations', 'flash'] });
    },
  });
};
</script>