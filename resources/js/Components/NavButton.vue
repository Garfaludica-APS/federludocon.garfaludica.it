<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
	type: {
		type: String,
		default: 'button',
	},
	active: {
		type: Boolean,
		default: false,
	},
	href: String,
});

const classes = computed(() => {
	return props.active
		? 'inline-flex items-center justify-center h-8 px-4 py-2 bg-green-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none ring-2 ring-green-500 ring-offset-2 dark:ring-offset-gray-800 transition ease-in-out duration-150'
		: 'inline-flex items-center justify-center h-8 px-4 py-2 bg-green-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150';
});
</script>

<template>
	<Link :href="$props.href" class="my-auto">
		<button :type="$props.type" :class="classes">
			<slot />
		</button>
	</Link>
</template>
