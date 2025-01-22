<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: '' }, () => h(PubLayout, { }, () => page)),
}
</script>

<script setup>
const props = defineProps({
	federludoLogo: String,
	comuneStemma: String,
});
</script>

<template>
	<div class="py-12">
		<div class="mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<Intro :federludoLogo="$props.federludoLogo" :comuneStemma="$props.comuneStemma" />
		</div>
	</div>
</template>
