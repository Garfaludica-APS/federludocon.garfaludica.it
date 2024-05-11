<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Booking' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('h1', { class: 'flex-1 font-extrabold text-3xl' }, trans('Booking')),
	})),
}
</script>

<script setup>
const props = defineProps({
	booking: Object,
});
</script>

<template>
	<section class="mb-6">
		<h2 class="font-bold text-xl py-4">{{ $t('Sorry... Currently unavailable') }}</h2>
		<p>{{ $t('This feature is currently unavailable. Please check back later.') }}</p>
	</section>
</template>
