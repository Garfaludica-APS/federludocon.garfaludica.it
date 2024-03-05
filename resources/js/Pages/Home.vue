<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Intro from '@/Components/Intro.vue';

const props = defineProps({
	tdgLogo: String,
});
</script>

<template>
	<AppLayout :title="$t('Home')">

		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
				<Intro :tdgLogo="$props.tdgLogo" />
			</div>
		</div>
	</AppLayout>
</template>
