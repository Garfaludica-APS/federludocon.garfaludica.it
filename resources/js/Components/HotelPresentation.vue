<script setup>
defineProps({
	hotel: Object,
});
</script>

<template>
	<h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">{{ $t('hotel_name_' + hotel.name) }}</h1>
	<hr />
	<p class="mt-4 text-gray-900 dark:text-white" v-html="$t('stored.hotel_' + hotel.name + '_presentation_text')"></p>
</template>
