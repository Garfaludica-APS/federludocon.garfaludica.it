<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
	type: {
		type: String,
		default: 'button',
	},
	color: {
		type: String,
		default: 'green',
	},
	href: String,
});

const classes = computed(() => {
	return 'inline-flex items-center justify-center h-8 px-4 py-2 border-2 rounded-full font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 ' +
		`bg-gray-900/10 hover:bg-${props.color}-500 text-white border-${props.color}-500 active:bg-${props.color}-700 focus:ring-${props.color}-500`;
});
</script>

<template>
	<Link :href="$props.href" class="my-auto">
		<button :type="$props.type" :class="classes">
			<slot />
		</button>
	</Link>
</template>
