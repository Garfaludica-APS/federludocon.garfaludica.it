<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
	color: {
		type: String,
		default: 'green',
	},
	href: String,
	newtab: {
		type: Boolean,
		default: false,
	}
});
</script>

<template>
	<a v-if="$props.newtab" :href="$props.href" type="button" class="inline-flex items-center justify-center h-8 px-6 py-6 border-2 rounded-full font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 bg-gray-900/20 text-white" target="_blank">
		<slot />
	</a>
	<Link v-else :href="$props.href" type="button" as="button" class="inline-flex items-center justify-center h-8 px-6 py-6 border-2 rounded-full font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 bg-gray-900/20 text-white">
		<slot />
	</Link>
</template>
