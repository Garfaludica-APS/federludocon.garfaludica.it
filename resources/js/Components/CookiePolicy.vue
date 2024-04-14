<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const emit = defineEmits(['accept']);

const props = defineProps({
	visible: {
		type: Boolean,
		default: true,
	},
});

const acceptCookies = () => {
	emit('accept');
};
</script>

<template>
	<div class="flex flex-col ml:flex-row items-center fixed transition-[bottom] duration-500 inset-x-4 bg-zinc-200 dark:bg-zinc-900 text-gray-900 dark:text-gray-100 z-50 p-8 rounded-xl shadow-2xl ring-4 ring-gray-400 dark:ring-gray-950 text-sm ml:text-base" :class="{'bottom-4': visible, '!bottom-[-1000%]': !visible}">
		<div class="grow">
			<h1 class="text-xl font-bold">{{ $t('Cookies Notice') }}</h1>
			<p class="mt-6" v-html="$t('cookie_notice_text')"></p>
			<Link :href="route(page.props.rp + 'modal.privacy', {redirect: route().current()})" class="inline-block mt-4 text-sm text-green-600 dark:text-green-500 underline" preserve-state preserve-scroll>{{ $t('Privacy Policy') }}</Link>
		</div>
		<div class="flex-none flex flex-col sm:flex-row ml:flex-col justify-center items-center max-w-full ml:max-w-xs mt-10 ml:mt-0 ml:ml-20">
			<button type="button" class="inline-flex min-w-52 items-center justify-center max-w-xs p-6 h-8 rounded-2xl font-bold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 w-full bg-green-600 hover:bg-green-500 text-white active:bg-green-500 focus:ring-green-500" @click="acceptCookies">{{ $t('Accept Cookies') }}</button>
			<p class="mt-4 sm:mt-0 ml:mt-4 sm:pl-8 ml:pl-0 text-xs ml:text-sm text-stone-700 dark:text-stone-500 text-center leading-tight" v-html="$t('cookie_no_decline_text')"></p>
		</div>
	</div>
</template>
