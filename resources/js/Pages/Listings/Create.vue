<script setup>
import { computed, onMounted, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Checkbox from '@/components/Checkbox.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    errors: Object,
    auth: Object,
});

const form = useForm({
    email: '',
    name: '',
    password: '',
    password_confirmation: '',
    title: '',
    company: '',
    logo: null,
    location: '',
    apply_link: '',
    tags: null,
    content: '',
    is_highlighted: false,
    payment_method_id: '',
});

const isGuest = ref(!props.auth.user);

const stripeKey = ref(import.meta.env.VITE_STRIPE_KEY);
const stripe = ref();
const elements = ref();
const cardElement = ref();
const cardError = ref();

onMounted(() => {
    includeStripe('js.stripe.com/v3/', function () {
        configureStripe();
    });
});

const hasErrors = computed(() => Object.keys(props.errors).length > 0);

const onSubmit = async () => {
    cardError.value = null;

    const { paymentMethod, error } = await stripe.value.createPaymentMethod({
        type: 'card',
        card: cardElement.value,
    });

    if (error) {
        cardError.value = error.message;
    }

    if (paymentMethod) {
        form.payment_method_id = paymentMethod?.id;
        form.post(route('listings.store'));
    }
};

const includeStripe = (URL, callback) => {
    let documentTag = document, tag = 'script',
        object = documentTag.createElement(tag),
        scriptTag = documentTag.getElementsByTagName(tag)[0];
    object.src = '//' + URL;
    if (callback) { object.addEventListener('load', function (e) { callback(null, e); }, false); }
    scriptTag.parentNode.insertBefore(object, scriptTag);
};

const configureStripe = () => {
    stripe.value = Stripe(stripeKey.value);
    elements.value = stripe.value.elements();
    cardElement.value = elements.value.create('card', {
        classes: {
            base: 'StripeElement rounded-md shadow-sm bg-white px-2 py-3 border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full'
        },
    });

    cardElement.value.mount('#card-element');
};
</script>

<template>

    <Head title="Listing - Create" />

    <AppLayout>
        <section class="text-gray-600 body-font overflow-hidden">
            <div class="w-full md:w-1/2 py-24 mx-auto">
                <div class="mb-4">
                    <h2 class="text-2xl font-medium text-gray-900 title-font">
                        Create a new listing ($99)
                    </h2>
                </div>
                <div class="mb-4 p-4 bg-red-200 text-red-800" v-if="hasErrors">
                    <ul>
                        <li v-for="error in errors">{{ error }}</li>
                    </ul>
                </div>
                <form id="payment_form" class="bg-gray-100 p-4"
                    @submit.prevent="onSubmit">
                    <template v-if="isGuest">
                        <div class="flex mb-4">
                            <div class="flex-1 mx-2">
                                <InputLabel for="email" value="Email Address" />
                                <TextInput id="email" type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email" required autofocus
                                    autocomplete="username" />
                                <InputError class="mt-2"
                                    :message="form.errors.email" />
                            </div>
                            <div class="flex-1 mx-2">
                                <InputLabel for="name" value="Full Name" />
                                <TextInput id="name" type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name" required />
                                <InputError class="mt-2"
                                    :message="form.errors.name" />
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="flex-1 mx-2">
                                <InputLabel for="password" value="Password" />
                                <TextInput id="password" type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password" required
                                    autocomplete="current-password" />
                                <InputError class="mt-2"
                                    :message="form.errors.password" />
                            </div>
                            <div class="flex-1 mx-2">
                                <InputLabel for="password_confirmation"
                                    value="Confirm Password" />
                                <TextInput id="password_confirmation"
                                    type="password" class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    required autocomplete="new-password" />
                                <InputError class="mt-2"
                                    :message="form.errors.password_confirmation" />
                            </div>
                        </div>
                    </template>
                    <div class="mb-4 mx-2">
                        <InputLabel for="title" value="Job Title" />
                        <TextInput id="title" type="text"
                            class="mt-1 block w-full" v-model="form.title"
                            required />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>
                    <div class="mb-4 mx-2">
                        <InputLabel for="company" value="Company Name" />
                        <TextInput id="company" type="text"
                            class="mt-1 block w-full" v-model="form.company"
                            required />
                        <InputError class="mt-2"
                            :message="form.errors.company" />
                    </div>
                    <div class="mb-4 mx-2">
                        <InputLabel for="logo" value="Company Logo" />
                        <TextInput id="logo" name="logo"
                            class="block mt-1 w-full" type="file"
                            @input="form.logo = $event.target.files[0]" />
                    </div>
                    <div class="mb-4 mx-2">
                        <InputLabel for="location"
                            value="Location (e.g. Remote, United States)" />
                        <TextInput id="location" type="text"
                            class="mt-1 block w-full" v-model="form.location"
                            required />
                        <InputError class="mt-2"
                            :message="form.errors.location" />
                    </div>
                    <div class="mb-4 mx-2">
                        <InputLabel for="apply_link" value="Link to Apply" />
                        <TextInput id="apply_link" name="apply_link" type="text"
                            class="mt-1 block w-full" v-model="form.apply_link"
                            required />
                        <InputError class="mt-2"
                            :message="form.errors.apply_link" />
                    </div>
                    <div class="mb-4 mx-2">
                        <InputLabel for="tags"
                            value="Tags separate by comma)" />
                        <TextInput id="tags" name="tags" type="text"
                            class="mt-1 block w-full" v-model="form.tags" />
                    </div>
                    <div class="mb-4 mx-2">
                        <InputLabel for="content"
                            value="Listing Content (Markdown is okay)" />
                        <textarea id="content" name="content" rows="8"
                            class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full"
                            v-model="form.content" />
                    </div>
                    <div class="mb-4 mx-2">
                        <InputLabel for="is_highlighted"
                            class="inline-flex items-center font-medium text-sm text-gray-700">
                            <Checkbox id="is_highlighted" name="is_highlighted"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50"
                                :checked="form.is_highlighted"
                                v-model="form.is_highlighted" />
                            <span class="ml-2">Highlight this post (extra
                                $19)</span>
                        </InputLabel>
                    </div>
                    <div class="mb-6 mx-2">
                        <div id="card-element"></div>
                        <div v-if="cardError" v-text="cardError"
                            class="text-red-500 mt-2" />
                    </div>
                    <div class="mb-2 mx-2">
                        <PrimaryButton type="submit"
                            class="block w-full items-center bg-indigo-500 text-white border-0 py-2 focus:outline-none hover:bg-indigo-600 rounded text-base mt-4 md:mt-0"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            Pay + Continue
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </section>
    </AppLayout>
</template>
