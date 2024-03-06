<script setup>
defineProps({
	type: {
		type: String,
		default: 'button',
	},
});
</script>

<template>
	<button :type="type" class="inline-flex items-center justify-center my-auto h-8 px-4 py-2 bg-green-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
		<slot />
	</button>
</template>
