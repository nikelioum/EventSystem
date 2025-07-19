<template>
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    <Head>
      <title>Admin - Events</title>
      <meta name="description" content="Manage events in the admin panel." />
    </Head>

    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Events</h1>

    <!-- Flash Message -->
    <div
      v-if="$page.props.flash?.message"
      class="mb-4 p-4 rounded"
      :class="{
        'bg-green-100 text-green-700': $page.props.flash?.type === 'success',
        'bg-red-100 text-red-700': $page.props.flash?.type === 'error',
      }"
    >
      {{ $page.props.flash.message }}
    </div>

    <!-- Create Event Button -->
    <div class="mb-6">
      <button
        @click="openCreateModal"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 cursor-pointer"
      >
        Create Event
      </button>
    </div>

    <!-- Responsive Events Table -->
    <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 hidden sm:table-header-group">
          <tr>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              ID
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Title
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Description
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Event Date
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Location
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Capacity
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Registrations
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Status
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Deleted At
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="event in events.data"
            :key="event.id"
            class="flex flex-col sm:table-row"
            :class="{ 'bg-gray-100': event.deleted_at }"
          >
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">ID:</span>
              {{ event.id }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Title:</span>
              {{ event.title }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Description:</span>
              {{ event.description || "N/A" }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Event Date:</span>
              {{ formatDateTime(event.event_date) }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Location:</span>
              {{ event.location }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Capacity:</span>
              {{ event.capacity }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Registrations:</span>
              {{ event.current_registrations_count }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Status:</span>
              {{ event.status }}
            </td>
            <td class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell">
              <span class="sm:hidden font-medium text-gray-500">Deleted At:</span>
              {{ event.deleted_at || "N/A" }}
            </td>
            <td
              class="px-6 py-4 sm:whitespace-nowrap flex justify-between sm:table-cell space-x-2"
            >
              <span class="sm:hidden font-medium text-gray-500">Actions:</span>
              <div class="flex space-x-2">
                <button
                  @click="openViewModal(event)"
                  class="text-blue-600 hover:text-blue-900 cursor-pointer"
                >
                  View
                </button>
                <button
                  v-if="!event.deleted_at"
                  @click="openEditModal(event)"
                  class="text-indigo-600 hover:text-indigo-900 cursor-pointer"
                >
                  Edit
                </button>
                <button
                  v-if="!event.deleted_at"
                  @click="deleteEvent(event.id)"
                  class="text-red-600 hover:text-red-900 cursor-pointer"
                  :disabled="event.current_registrations_count > 0"
                >
                  Delete
                </button>
                <button
                  v-if="event.deleted_at"
                  @click="restoreEvent(event.id)"
                  class="text-green-600 hover:text-green-900 cursor-pointer"
                >
                  Restore
                </button>
                <button
                  v-if="event.deleted_at"
                  @click="forceDeleteEvent(event.id)"
                  class="text-red-600 hover:text-red-900 cursor-pointer"
                >
                  Force Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
      <div v-if="events.links" class="flex flex-wrap items-center gap-2">
        <template v-for="link in events.links" :key="link.label">
          <Link
            v-if="link.url"
            :href="link.url"
            class="px-3 py-1 text-sm border rounded transition"
            :class="{
              'bg-blue-600 text-white': link.active,
              'text-gray-700 hover:bg-gray-100': !link.active,
            }"
            v-html="link.label"
          />
          <span
            v-else
            class="px-3 py-1 text-sm border rounded text-gray-400"
            v-html="link.label"
          />
        </template>
      </div>
    </div>

    <!-- Create/Edit Modal Component -->
    <CreateEditEventModal
      :isOpen="isModalOpen"
      :isEditing="isEditing"
      :event="selectedEvent"
      @close="closeModal"
    />

    <!-- View Modal Component -->
    <ViewEventModal
      :isOpen="isViewModalOpen"
      :event="selectedEvent"
      @close="closeViewModal"
    />
  </div>
</template>

<script setup lang="ts">
import MainLayout from "@/layouts/Main.vue";
import { Link, Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import CreateEditEventModal from "@/components/modals/CreateEditEventModal.vue";
import ViewEventModal from "@/components/modals/ViewEventModal.vue";

function formatDateTime(dateString: string): string {
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${day}-${month}-${year} ${hours}:${minutes}`;
}

// Define TypeScript interface for event data
interface Event {
  id: number;
  title: string;
  description: string | null;
  event_date: string;
  location: string;
  capacity: number;
  current_registrations_count: number;
  status: "draft" | "published" | "cancelled";
  created_at: string;
  updated_at: string;
  deleted_at: string | null;
}

// Define TypeScript interface for pagination links
interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

// Define TypeScript interface for paginated events
interface PaginatedEvents {
  data: Event[];
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
  events: PaginatedEvents;
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
const selectedEvent = ref<Event | null>(null);

// Open create modal
const openCreateModal = () => {
  selectedEvent.value = null;
  isEditing.value = false;
  isModalOpen.value = true;
};

// Open edit modal with event data
const openEditModal = (event: Event) => {
  selectedEvent.value = event;
  isEditing.value = true;
  isModalOpen.value = true;
};

// Open view modal with event data
const openViewModal = (event: Event) => {
  selectedEvent.value = event;
  isViewModalOpen.value = true;
};

// Close create/edit modal
const closeModal = () => {
  isModalOpen.value = false;
  selectedEvent.value = null;
};

// Close view modal
const closeViewModal = () => {
  isViewModalOpen.value = false;
  selectedEvent.value = null;
};

// Delete event with policy check
const deleteEvent = (eventId: number) => {
  if (confirm("Are you sure you want to delete this event?")) {
    router.delete(route("admin.events.destroy", eventId), {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ["events", "flash"] });
      },
      onError: (errors: Record<string, string>) => {
        router.reload({
          data: {
            flash: { message: errors.__all__ || "Cannot delete event.", type: "error" },
          },
        });
      },
    });
  }
};

// Restore event
const restoreEvent = (eventId: number) => {
  if (confirm("Are you sure you want to restore this event?")) {
    router.post(
      route("admin.events.restore", eventId),
      {},
      {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          router.reload({ only: ["events", "flash"] });
        },
      }
    );
  }
};

// Force delete event
const forceDeleteEvent = (eventId: number) => {
  if (confirm("Are you sure you want to permanently delete this event?")) {
    router.delete(route("admin.events.forceDestroy", eventId), {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ["events", "flash"] });
      },
    });
  }
};
</script>
