<script setup lang="ts">
import MainLayout from '@/layouts/Main.vue';
import { Head } from '@inertiajs/vue3';
import { Chart } from 'chart.js/auto';
import { ref, onMounted } from 'vue';

// Define TypeScript interface for metrics
interface Metrics {
  total_users: number;
  total_events: number;
  total_registrations: number;
}

// Define TypeScript interface for counts (status and capacity)
interface Counts {
  [key: string]: number;
}

// Destructure props explicitly
const { metrics, eventStatusCounts, eventCapacityCounts } = defineProps<{
  metrics: Metrics;
  eventStatusCounts: Counts;
  eventCapacityCounts: Counts;
}>();

defineOptions({
  layout: MainLayout,
});

// References to canvas elements
const statusChartRef = ref<HTMLCanvasElement | null>(null);
const capacityChartRef = ref<HTMLCanvasElement | null>(null);

// Initialize charts on component mount
onMounted(() => {
  console.log('Props:', { metrics, eventStatusCounts, eventCapacityCounts }); // Debug props

  // Status Chart
  if (statusChartRef.value && eventStatusCounts && Object.keys(eventStatusCounts).length > 0) {
    new Chart(statusChartRef.value, {
      type: 'pie',
      data: {
        labels: Object.keys(eventStatusCounts).map(status => status.charAt(0).toUpperCase() + status.slice(1)),
        datasets: [{
          label: 'Events by Status',
          data: Object.values(eventStatusCounts),
          backgroundColor: [
            '#10B981', // Green
            '#F59E0B', // Yellow
            '#EF4444', // Red
            '#3B82F6', // Blue
            '#8B5CF6'  // Purple
          ],
          borderColor: [
            '#047857',
            '#B45309',
            '#B91C1C',
            '#1D4ED8',
            '#6D28D9'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: '#1F2937',
              font: { size: 14 }
            }
          },
          tooltip: {
            backgroundColor: '#1F2937',
            titleColor: '#FFFFFF',
            bodyColor: '#FFFFFF',
            borderColor: '#D1D5DB',
            borderWidth: 1
          }
        }
      }
    });
  } else {
    console.log('Status Chart not rendered:', {
      hasCanvas: !!statusChartRef.value,
      hasData: !!eventStatusCounts,
      dataLength: eventStatusCounts ? Object.keys(eventStatusCounts).length : 0
    });
  }

  // Capacity Chart
  if (capacityChartRef.value && eventCapacityCounts && Object.keys(eventCapacityCounts).length > 0) {
    new Chart(capacityChartRef.value, {
      type: 'pie',
      data: {
        labels: ['Full', 'Empty', 'Partially Filled'],
        datasets: [{
          label: 'Events by Capacity',
          data: [
            eventCapacityCounts.full || 0,
            eventCapacityCounts.empty || 0,
            eventCapacityCounts.partial || 0
          ],
          backgroundColor: [
            '#EF4444', // Red for Full
            '#D1D5DB', // Gray for Empty
            '#3B82F6'  // Blue for Partial
          ],
          borderColor: [
            '#B91C1C',
            '#9CA3AF',
            '#1D4ED8'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: '#1F2937',
              font: { size: 14 }
            }
          },
          tooltip: {
            backgroundColor: '#1F2937',
            titleColor: '#FFFFFF',
            bodyColor: '#FFFFFF',
            borderColor: '#D1D5DB',
            borderWidth: 1
          }
        }
      }
    });
  } else {
    console.log('Capacity Chart not rendered:', {
      hasCanvas: !!capacityChartRef.value,
      hasData: !!eventCapacityCounts,
      dataLength: eventCapacityCounts ? Object.keys(eventCapacityCounts).length : 0
    });
  }
});
</script>

<template>
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    <Head>
      <title>Admin - Dashboard</title>
      <meta name="description" content="Overview of system status and metrics." />
    </Head>

    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Dashboard</h1>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-semibold mb-2">Total Users</h2>
        <p class="text-3xl font-bold text-gray-800">{{ metrics.total_users }}</p>
      </div>
      <div class="bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-semibold mb-2">Total Events</h2>
        <p class="text-3xl font-bold text-gray-800">{{ metrics.total_events }}</p>
      </div>
      <div class="bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-semibold mb-2">Total Registrations</h2>
        <p class="text-3xl font-bold text-gray-800">{{ metrics.total_registrations }}</p>
      </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <!-- Events by Status Chart -->
      <div class="bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-semibold mb-4">Events by Status</h2>
        <div v-if="eventStatusCounts && Object.keys(eventStatusCounts).length > 0" class="w-full h-64">
          <canvas ref="statusChartRef"></canvas>
        </div>
        <div v-else class="text-gray-500 text-center">No event status data available.</div>
      </div>

      <!-- Events by Capacity Chart -->
      <div class="bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-semibold mb-4">Events by Capacity</h2>
        <div v-if="eventCapacityCounts && Object.keys(eventCapacityCounts).length > 0" class="w-full h-64">
          <canvas ref="capacityChartRef"></canvas>
        </div>
        <div v-else class="text-gray-500 text-center">No event capacity data available.</div>
      </div>
    </div>
  </div>
</template>