<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Head, Link } from '@inertiajs/inertia-vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';
import pickBy from 'lodash/pickBy';

const props = defineProps({
    listings: Object,
    tags: Object,
    filters: Object,
});

const filters = ref(props?.filters);

const onTagClick = function (tag) {
    const queryTag = filters?.value?.tag === tag ? { tag: '' } : { tag };

    let query = pickBy({ ...filters.value, ...queryTag });
    let queryRoute = route('listings.index', query);

    Inertia.get(queryRoute, {});
};
</script>

<template>

    <Head title="Listings" />
    <GuestLayout>
        <Hero />
        <section class="container px-5 py-12 mx-auto">
            <div class="mb-12">
                <div class="flex justify-center">
                    <button v-for="tag in tags" :key="tag.name"
                        @click.prevent="onTagClick(tag.slug)"
                        class="inline-block ml-2 rounded-md tracking-wide text-xs font-medium title-font py-0.5 px-1.5 border border-indigo-500 uppercase"
                        :class="[tag.slug === filters.tag ? 'bg-indigo-500 text-white' : 'bg-white text-indigo-500']">
                        {{ tag.name }}</button>
                </div>
            </div>
            <div class="mb-12">
                <h2 class="text-2xl font-medium text-gray-900 title-font px-4">
                    All jobs ({{ props.listings?.length }})</h2>
            </div>
            <div class="-my-6">
                <a :href="route('listings.show', [listing.slug])"
                    v-for="listing in props.listings" :key="listing.slug"
                    class="py-6 px-4 flex flex-wrap md:flex-nowrap border-b border-gray-100"
                    :class="[listing.is_highlighted ? 'bg-yellow-100 hover:bg-yellow-200' : 'bg-white hover:bg-gray-100']">
                    <div
                        class="mb-6 mr-4 flex-shrink-0 flex flex-col md:w-16 md:mb-0">
                        <img :src="listing.logo"
                            :alt="`${listing.company} logo`"
                            class="w-16 h-16 rounded-full object-cover">
                    </div>
                    <div
                        class="md:w-1/2 mr-8 flex flex-col items-start justify-center">
                        <h2 class="text-xl font-bold text-gray-900 title-font mb-1"
                            v-text="listing.title" />
                        <p class="leading-relaxed text-gray-900">
                            {{ listing.company }} &mdash; <span
                                class="text-gray-600"
                                v-text="listing.location" />
                        </p>
                    </div>
                    <div
                        class="md:flex-grow mr-8 flex items-center justify-start">
                        <span v-for="tag in listing.tags" :key="tag.name"
                            class="inline-block ml-2 rounded-md tracking-wide text-xs font-medium title-font py-0.5 px-1.5 border border-indigo-500 uppercase"
                            :class="[tag.slug === filters.tag ? 'bg-indigo-500 text-white' : 'bg-white text-indigo-500']">
                            {{ tag.name }}</span>
                    </div>
                    <span class="md:flex-grow flex items-center justify-end">
                        <span v-text="listing.created_at" />
                    </span>
                </a>
            </div>
        </section>

    </GuestLayout>
</template>
