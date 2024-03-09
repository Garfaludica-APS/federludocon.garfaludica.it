<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
	href: String,
	external: {
		type: Boolean,
		default: false
	}
});

</script>

<template>
	<a v-if="external" rel="noopener external" :href="href" class="block w-full pe-4 py-2 text-start text-xs xs:text-sm text-nowrap border-b border-gray-100 dark:border-gray-800 focus:outline-none hover:text-indigo-500 focus:text-indigo-500 transition duration-1000 ease-in-out">
		<span class="inline-block align-top align-text-top h-6 overflow-hidden">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="inline-block fill-indigo-500 h-6 w-6">
				<path fill-rule="evenodd" d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
			</svg>
			<slot />
		</span>
	</a>
	<Link v-else :href="href" class="block w-full pe-4 py-2 text-start text-xs xs:text-sm text-nowrap border-b border-gray-100 dark:border-gray-800 focus:outline-none hover:text-indigo-500 focus:text-indigo-500 transition duration-1000 ease-in-out">
		<span class="inline-block align-top align-text-top h-6 overflow-hidden">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="inline-block fill-indigo-500 h-6 w-6">
				<path fill-rule="evenodd" d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
			</svg>
			<slot />
		</span>
	</Link>
</template>
