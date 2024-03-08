<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Intro from '@/Components/Intro.vue';

const props = defineProps({
	tdgLogo: String,
});
</script>

<template>
	<AppLayout v-slot="{ curLang }">
		<div class="py-12">
			<div class="mx-auto sm:px-6 lg:px-8 animate-fade-in">
				<Intro :tdgLogo="$props.tdgLogo" :curLang="curLang" />
			</div>
		</div>
	</AppLayout>
</template>
