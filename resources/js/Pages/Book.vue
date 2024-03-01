<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Book from '@/Components/Book.vue';
</script>

<template>
	<AppLayout :title="$t('Book Now!')">
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ $t('Book Now!') }}
			</h2>
		</template>

		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
					<Book />
				</div>
			</div>
		</div>
	</AppLayout>
</template>
