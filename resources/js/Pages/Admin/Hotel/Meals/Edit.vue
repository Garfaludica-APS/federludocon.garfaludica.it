<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Edit Meal' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('h1', { class: 'font-extrabold text-3xl' }, trans('Edit Meal')),
	})),
}
</script>

<script setup>
	const props = defineProps({
		meal: Object,
	});
</script>

<template>
	<MealForm :hotel="meal.hotel" :meal="meal" />
</template>
