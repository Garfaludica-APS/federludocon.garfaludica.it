<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Hotels' }, () => h(PubLayout, () => page)),
}
</script>

<script setup>
import HotelPresentation from '@/Components/HotelPresentation.vue';

defineProps({
	hotels: Array,
});
</script>

<template>
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div>
					<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
						<HotelPresentation v-for="hotel in hotels" :key="hotel.id" :hotel="hotel" />
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
