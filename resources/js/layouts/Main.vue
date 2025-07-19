<script setup lang="ts">
import Navbar from '@/components/header/Navbar.vue'
import Footer from '@/components/footer/Footer.vue'
</script>


<template>
    <!--Navbar -->
    <Navbar />
    <div class="bg-gray-100">
        <!-- Main content -->
        <main class="max-w-[1720px] mx-auto px-6 sm:px-12 min-h-screen">
            <slot />
        </main>
    </div>
    <!--Footer-->
    <Footer />
</template>