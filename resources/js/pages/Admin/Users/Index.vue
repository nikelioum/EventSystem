<template>
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">

    <Head>
      <title>Admin - Users</title>
      <meta name="description" content="Manage users in the admin panel." />
    </Head>

    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Users</h1>

    <!-- Flash Message -->
    <div v-if="$page.props.flash?.message" class="mb-4 p-4 rounded" :class="{
      'bg-green-100 text-green-700': $page.props.flash?.type === 'success',
      'bg-red-100 text-red-700': $page.props.flash?.type === 'error',
    }">
      {{ $page.props.flash.message }}
    </div>

    <!-- Create User Button -->
    <div class="mb-6">
      <button @click="openCreateModal"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 cursor-pointer">
        Create User
      </button>
    </div>


    <!-- Responsive Users Table -->
    <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 hidden sm:table-header-group">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              ID
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Email
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Role
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users.data" :key="user.id" class="flex flex-col sm:table-row">
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">ID:</span>
              {{ user.id }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Name:</span>
              {{ user.name }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Email:</span>
              {{ user.email }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Role:</span>
              {{ user.role }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell space-x-2">
              <span class="sm:hidden font-medium text-gray-500">Actions:</span>
              <div class="flex space-x-2">
                <button @click="openViewModal(user)" class="text-blue-600 hover:text-blue-900 cursor-pointer">
                  View
                </button>
                <button @click="openEditModal(user)" class="text-indigo-600 hover:text-indigo-900 cursor-pointer">
                  Edit
                </button>
                <button @click="deleteUser(user.id)" :disabled="user.events_count > 0"
                  :title="user.events_count > 0 ? 'Cannot delete: user has event registrations' : 'Delete user'"
                  class="font-medium transition-colors" :class="user.events_count > 0
                    ? 'text-red-300 cursor-not-allowed'  // lighter red text + no pointer
                    : 'text-red-600 hover:text-red-900 cursor-pointer'">
                  Delete
                </button>


              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
      <div v-if="users.links" class="flex flex-wrap items-center space-x-1 mt-4">
        <template v-for="link in users.links" :key="link.label">
          <Link v-if="link.url" :href="link.url" class="px-3 py-1 text-sm border rounded" :class="{
            'bg-blue-600 text-white': link.active,
            'text-gray-700 hover:bg-gray-100': !link.active,
          }" v-html="link.label" />
          <span v-else class="px-3 py-1 text-sm border rounded text-gray-400" v-html="link.label" />
        </template>
      </div>
    </div>

    <!-- Create/Edit Modal Component -->
    <CreateEditUserModal :isOpen="isModalOpen" :isEditing="isEditing" :user="selectedUser" @close="closeModal" />

    <!-- View Modal Component -->
    <ViewUserModal :isOpen="isViewModalOpen" :user="selectedUser" @close="closeViewModal" />
  </div>
</template>

<script setup lang="ts">
import MainLayout from "@/layouts/Main.vue";
import { Link, Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import CreateEditUserModal from "@/components/modals/CreateEditUserModal.vue";
import ViewUserModal from "@/components/modals/ViewUserModal.vue";

// Define TypeScript interface for user data
interface User {
  id: number;
  name: string;
  email: string;
  role: "admin" | "user";
  events_count?: number;
}

// Define TypeScript interface for pagination links
interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

// Define TypeScript interface for paginated users
interface PaginatedUsers {
  data: User[];
  links: PaginationLink[];
  current_page: number;
  last_page: number;
}

// Define TypeScript interface for flash message
interface FlashMessage {
  message: string;
  type: "success" | "error";
}

// Define props with TypeScript
defineProps<{
  users: PaginatedUsers;
  flash?: FlashMessage;
}>();

defineOptions({
  layout: MainLayout,
});

// Modal state for create/edit
const isModalOpen = ref(false);
const isEditing = ref(false);

// Modal state for view
const isViewModalOpen = ref(false);
const selectedUser = ref<User | null>(null);

// Open create modal
const openCreateModal = () => {
  selectedUser.value = null;
  isEditing.value = false;
  isModalOpen.value = true;
};

// Open edit modal with user data
const openEditModal = (user: User) => {
  selectedUser.value = user;
  isEditing.value = true;
  isModalOpen.value = true;
};

// Open view modal with user data
const openViewModal = (user: User) => {
  selectedUser.value = user;
  isViewModalOpen.value = true;
};

// Close create/edit modal
const closeModal = () => {
  isModalOpen.value = false;
  selectedUser.value = null;
};

// Close view modal
const closeViewModal = () => {
  isViewModalOpen.value = false;
  selectedUser.value = null;
};

// Delete user with policy check
const deleteUser = (userId: number) => {
  if (confirm("Are you sure you want to delete this user?")) {
    router.delete(route("admin.users.destroy", userId), {
      onSuccess: () => {
        // Flash message handled by controller redirect
      },
      onError: (errors: Record<string, string>) => {
        router.reload({
          data: {
            flash: { message: errors.__all__ || "Cannot delete user.", type: "error" },
          },
        });
      },
    });
  }
};
</script>
