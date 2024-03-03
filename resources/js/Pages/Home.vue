<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

defineProps({
	loripsum: String,
});
</script>

<template>
	<AppLayout :title="$t('Home')">
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ $t('Home') }}
			</h2>
		</template>

		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
					<Welcome :loripsum="$props.loripsum" />
				</div>
			</div>
		</div>
	</AppLayout>
</template>
