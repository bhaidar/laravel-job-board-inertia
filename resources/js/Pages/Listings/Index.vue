<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const props = defineProps({
    listings: Object,
    tags: Object,
    // collect any query string
    filters: Object,
});

// Tag query string if it exists
const tagQs = props.filters?.tag;

</script>
<template>

    <Head title="Listings" />
    <GuestLayout>
        <Hero />
        <section class="container px-5 py-12 mx-auto">
            <div class="mb-12">
                <div class="flex justify-center">
                    <Link v-for="tag in tags" :key="tag.name"
                        :href="route('listings.index', [tag.slug])"
                        class="inline-block ml-2 rounded-md tracking-wide text-xs font-medium title-font py-0.5 px-1.5 border border-indigo-500 uppercase"
                        :class="[tag.slug === tagQs ? 'bg-indigo-500 text-white' : 'bg-white text-indigo-500']">
                    {{ tag.name }}</Link>
                </div>
            </div>
        </section>

    </GuestLayout>
</template>
