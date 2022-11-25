<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
</script>

<template>
    <div class="min-h-screen flex flex-col flex-wrap">
        <Header />
        <div class="flex flex-col flex-1 min-h-full">
            <div class="container flex flex-col flex-1 p-5 mx-auto">
                <slot />
            </div>
        </div>
        <Footer />
    </div>
</template>
