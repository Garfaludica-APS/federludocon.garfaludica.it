<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Create Room' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('h1', { class: 'font-extrabold text-3xl' }, trans('Create Room')),
	})),
}
</script>

<script setup>
	const props = defineProps({
		hotel: Object,
	});
</script>

<template>
	<RoomForm :hotel="hotel" />
</template>
