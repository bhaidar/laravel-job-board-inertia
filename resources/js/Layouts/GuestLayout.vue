<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
</script>

<template>
    <div class="min-h-screen flex flex-col flex-wrap">
        <Header />
        <div class="flex-1 min-h-full">
            <div class="container h-full p-5 mx-auto">
                <slot />
            </div>
        </div>
        <Footer />
    </div>
</template>
