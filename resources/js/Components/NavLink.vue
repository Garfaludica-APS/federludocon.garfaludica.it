<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
	href: String,
	active: Boolean,
});

const classes = computed(() => {
	return props.active
		? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 dark:border-indigo-600 text-sm font-medium leading-5 text-white group-[.is-scrolled]:text-gray-900 dark:text-white group-[.is-scrolled]:dark:text-gray-100 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
		: 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-300 group-[.is-scrolled]:text-gray-500 group-[.is-scrolled]:dark:text-gray-300 hover:text-gray-200 group-[.is-scrolled]:hover:text-gray-700 group-[.is-scrolled]:dark:hover:text-gray-200 hover:border-gray-500 focus:outline-none focus:text-gray-300 group-[.is-scrolled]:focus:text-gray-700 group-[.is-scrolled]:dark:focus:text-gray-300 focus:border-gray-500 group-[.is-scrolled]:dark:focus:text-gray-200 transition duration-150 ease-in-out';
});
</script>

<template>
	<Link :href="href" :class="classes">
		<slot />
	</Link>
</template>
