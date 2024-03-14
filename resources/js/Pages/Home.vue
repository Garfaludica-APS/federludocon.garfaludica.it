<script setup>
import PubLayout from '@/Layouts/PubLayout.vue';
import Intro from '@/Components/Intro.vue';

const props = defineProps({
	tdgLogo: String,
});

defineOptions({ layout: PubLayout });
</script>

<template>
	<div class="py-12">
		<div class="mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<Intro :tdgLogo="$props.tdgLogo" />
		</div>
	</div>
</template>
