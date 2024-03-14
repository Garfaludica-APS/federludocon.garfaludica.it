<script setup>
import PubLayout from '@/Layouts/PubLayout.vue';
import Book from '@/Components/Book.vue';
</script>

<template>
	<PubLayout :title="$t('Booking Portal')">
		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
					<Book />
					<Book />
				</div>
			</div>
		</div>
	</PubLayout>
</template>
