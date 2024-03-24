<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
	href: String,
	active: Boolean,
});

const classes = computed(() => {
	return props.active
		? 'flex cursor-pointer py-3 dark:text-slate-300 focus:outline-none font-bold brightness-200 transition duration-150 ease-in-out'
		: 'flex cursor-pointer py-3 dark:text-slate-300 dark:hover:text-white focus:outline-none transition duration-150 ease-in-out';
});
</script>

<template>
	<Link :href="href" :class="classes">
		<slot name="icon" />
		<span class="grow text-ellipsis line-clamp-1 pr-12">
			<slot />
		</span>
	</Link>
</template>
