<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Book from '@/Components/Book.vue';
</script>

<template>
	<AppLayout :title="$t('Book Now!')">
		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
					<Book />
					<Book />
				</div>
			</div>
		</div>
	</AppLayout>
</template>
