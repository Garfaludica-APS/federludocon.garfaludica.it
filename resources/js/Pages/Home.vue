<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: '' }, () => h(PubLayout, { showNote: true }, () => page)),
}
</script>

<script setup>
const props = defineProps({
	tdgLogo: String,
});
</script>

<template>
	<div class="py-12">
		<div class="mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<Intro :tdgLogo="$props.tdgLogo" />
		</div>
	</div>
</template>
