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
				<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
					<h1 class="ms-3 text-2xl font-medium text-black dark:text-slate-200">
						{{ $t('Map') }}
					</h1>
					<p class="ms-3 mt-6 text-lg text-black dark:text-slate-200 leading-relaxed">
						{{ $t('The event will take place in Corfino. You can check the map below to find out where it is. We recommend reaching the place by car.') }}
					</p>
					<p class="ms-3 mt-6 text-lg text-black dark:text-slate-200 leading-relaxed">
						{{$t('Located at the foot of the Pania and near the National Orecchiella Park, the Corfino village enjoys an enchanting landscape of the Apuan Alps and the Appennines. It has ancient origins dating back to 793, when it is mentioned in a parchment.')}}
					</p>
					<p class="ms-3 mt-6 text-lg text-black dark:text-slate-200 leading-relaxed">
						{{ $t('Legenda') }}:
					</p>
					<ul class="ms-3 text-lg text-black dark:text-slate-200 leading-relaxed">
						<li>P: {{ $t('hotel_name_Panoramic Hotel') }}</li>
						<li>I: {{ $t('hotel_name_Isera Refuge') }}</li>
						<li>A: {{ $t('hotel_name_Braccicorti Farmhouse') }}</li>
						<li>C: {{ $t('Camper Equipped Area') }}</li>
					</ul>
					<div class="mt-4 p-8 min-w-full max-w-full mx-auto">
						<VenueMap />
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
