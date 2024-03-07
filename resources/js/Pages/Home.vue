<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Intro from '@/Components/Intro.vue';

const props = defineProps({
	tdgLogo: String,
});
</script>

<template>
	<AppLayout :title="$t('Home')" v-slot="{ curLang }">
		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
				<Intro :tdgLogo="$props.tdgLogo" :curLang="curLang" />
			</div>
		</div>
	</AppLayout>
</template>
