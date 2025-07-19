<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Edit User' : 'Create User' }}</h2>
      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
          <input
            v-model="form.name"
            id="name"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.name }"
          />
          <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
        </div>
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            v-model="form.email"
            id="email"
            type="email"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.email }"
          />
          <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
        </div>
        <div class="mb-4">
          <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
          <select
            v-model="form.role"
            id="role"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.role }"
          >
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
          <p v-if="form.errors.role" class="text-red-500 text-sm mt-1">{{ form.errors.role }}</p>
        </div>
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input
            v-model="form.password"
            id="password"
            type="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.password }"
          />
          <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
        </div>
        <div class="mb-4">
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            id="password_confirmation"
            type="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
            :class="{ 'border-red-500': form.errors.password_confirmation }"
          />
          <p v-if="form.errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ form.errors.password_confirmation }}</p>
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

// Define TypeScript interface for user data
interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
}

// Define props with TypeScript
const { isOpen, isEditing, user } = defineProps<{
  isOpen: boolean;
  isEditing: boolean;
  user?: User;
}>();

// Define emits
const emit = defineEmits<{
  (e: 'close'): void;
}>();

// Form state using Inertia's useForm
const form = useForm<{
  id?: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
  password: string | null;
  password_confirmation: string | null;
  errors: Record<string, string>;
}>({
  id: undefined,
  name: '',
  email: '',
  role: 'user',
  password: null,
  password_confirmation: null,
  errors: {},
});

// Watch for user prop changes to populate form for editing
watch(
  () => user,
  (newUser) => {
    if (newUser && isEditing) {
      form.reset();
      form.id = newUser.id;
      form.name = newUser.name;
      form.email = newUser.email;
      form.role = newUser.role;
      form.password = null;
      form.password_confirmation = null;
    } else {
      form.reset();
    }
  },
  { immediate: true }
);

// Submit form (create or update)
const submitForm = () => {
  const routeName = isEditing ? 'admin.users.update' : 'admin.users.store';
  const method = isEditing ? 'put' : 'post';
  router[method](route(routeName, isEditing ? form.id : undefined), {
    name: form.name,
    email: form.email,
    role: form.role,
    password: form.password,
    password_confirmation: form.password_confirmation,
  }, {
    onSuccess: () => {
      emit('close');
      form.reset();
    },
    onError: (errors: Record<string, string>) => {
      form.errors = errors;
    },
  });
};
</script>