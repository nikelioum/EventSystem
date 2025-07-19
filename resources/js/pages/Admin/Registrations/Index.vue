<template>
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">

    <Head>
      <title>Admin - Registrations</title>
      <meta name="description" content="Manage event registrations in the admin panel." />
    </Head>

    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Registrations</h1>

    <!-- Flash Message -->
    <div v-if="$page.props.flash?.message" class="mb-4 p-4 rounded" :class="{
      'bg-green-100 text-green-700': $page.props.flash?.type === 'success',
      'bg-red-100 text-red-700': $page.props.flash?.type === 'error',
    }">
      {{ $page.props.flash.message }}
    </div>

    <!-- Create Registration Button -->
    <!-- Buttons -->
    <div class="mb-6 flex space-x-4">
      <button @click="openCreateModal"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 cursor-pointer">
        Create Registration
      </button>
      <a href="/registrations/export" target="_blank"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 cursor-pointer">
        Export to CSV
      </a>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 mb-6">
      <div>
        <label class="block text-sm font-medium text-gray-700">Filter by Event</label>
        <select v-model="filters.event_id" @change="applyFilters"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2">
          <option value="">All Events</option>
          <option v-for="event in events" :key="event.id" :value="event.id">
            {{ event.title }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Filter by User</label>
        <select v-model="filters.user_id" @change="applyFilters"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2">
          <option value="">All Users</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- Responsive Registrations Table -->
    <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 hidden sm:table-header-group">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              ID
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Event
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              User Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              User Email
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Registered At
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="registration in registrations.data" :key="registration.id" class="flex flex-col sm:table-row">
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">ID:</span>
              {{ registration.id }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Event:</span>
              {{ registration.event?.title || "N/A" }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">User Name:</span>
              {{ registration.user?.name || "N/A" }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">User Email:</span>
              {{ registration.user?.email || "N/A" }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Registered At:</span>
              {{ formatDateTime(registration.created_at) }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell space-x-2">
              <span class="sm:hidden font-medium text-gray-500">Actions:</span>
              <div class="flex space-x-2">
                <button @click="openViewModal(registration)" class="text-blue-600 hover:text-blue-900 cursor-pointer">
                  View
                </button>
                <!-- <button
                  @click="openEditModal(registration)"
                  class="text-indigo-600 hover:text-indigo-900 cursor-pointer"
                >
                  Edit
                </button> -->
                <button @click="deleteRegistration(registration.id)"
                  class="text-red-600 hover:text-red-900 cursor-pointer">
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>


    <!-- Pagination -->
    <div class="mt-6">
      <div v-if="registrations.links" class="flex flex-wrap items-center gap-2">
        <template v-for="link in registrations.links" :key="link.label">
          <Link v-if="link.url" :href="link.url" class="px-3 py-1 text-sm border rounded transition" :class="{
            'bg-blue-600 text-white': link.active,
            'text-gray-700 hover:bg-gray-100': !link.active,
          }" v-html="link.label" />
          <span v-else class="px-3 py-1 text-sm border rounded text-gray-400" v-html="link.label" />
        </template>
      </div>
    </div>


    <!-- Create/Edit Modal Component -->
    <CreateEditRegistrationModal :isOpen="isModalOpen" :isEditing="isEditing" :registration="selectedRegistration"
      :events="events" :users="users" @close="closeModal" />

    <!-- View Modal Component -->
    <ViewRegistrationModal :isOpen="isViewModalOpen" :registration="selectedRegistration" @close="closeViewModal" />
  </div>
</template>

<script setup lang="ts">
import MainLayout from "@/layouts/Main.vue";
import { Link, Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import CreateEditRegistrationModal from "@/components/modals/CreateEditRegistrationModal.vue";
import ViewRegistrationModal from "@/components/modals/ViewRegistrationModal.vue";
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const filters = ref({
  event_id: page.props.filters?.event_id || '',
  user_id: page.props.filters?.user_id || '',
});

const applyFilters = () => {
  router.get(route('admin.registrations.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  });
};

function formatDateTime(dateString: string): string {
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${day}-${month}-${year} ${hours}:${minutes}`;
}

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

// Define TypeScript interface for pagination links
interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

// Define TypeScript interface for paginated registrations
interface PaginatedRegistrations {
  data: Registration[];
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
  registrations: PaginatedRegistrations;
  events: Event[];
  users: User[];
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
const selectedRegistration = ref<Registration | null>(null);

// Open create modal
const openCreateModal = () => {
  selectedRegistration.value = null;
  isEditing.value = false;
  isModalOpen.value = true;
};

// Open edit modal with registration data
const openEditModal = (registration: Registration) => {
  selectedRegistration.value = registration;
  isEditing.value = true;
  isModalOpen.value = true;
};

// Open view modal with registration data
const openViewModal = (registration: Registration) => {
  selectedRegistration.value = registration;
  isViewModalOpen.value = true;
};

// Close create/edit modal
const closeModal = () => {
  isModalOpen.value = false;
  selectedRegistration.value = null;
};

// Close view modal
const closeViewModal = () => {
  isViewModalOpen.value = false;
  selectedRegistration.value = null;
};

// Delete registration
const deleteRegistration = (registrationId: number) => {
  if (confirm("Are you sure you want to delete this registration?")) {
    router.delete(route("admin.registrations.destroy", registrationId), {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ["registrations", "flash"] });
      },
      onError: (errors: Record<string, string>) => {
        router.reload({
          data: {
            flash: {
              message: errors.__all__ || "Failed to delete registration.",
              type: "error",
            },
          },
        });
      },
    });
  }
};
</script>
