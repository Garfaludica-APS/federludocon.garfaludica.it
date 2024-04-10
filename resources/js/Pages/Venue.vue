<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Venue' }, () => h(PubLayout, () => page)),
}
</script>

<script setup>
import VenueMap from '@/Components/VenueMap.vue';
</script>

<template>
	<div class="py-12">
		<div class="w-screen max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="w-full bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div class="min-w-full max-w-full mx-auto">
					<VenueMap />
				</div>
			</div>
		</div>
	</div>
</template>
