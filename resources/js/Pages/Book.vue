<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Booking Portal' }, () => h(PubLayout, () => page)),
}
</script>

<script setup>
import Book from '@/Components/Book.vue';
</script>

<template>
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<Book />
				<Book />
			</div>
		</div>
	</div>
</template>
