<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Edit Room' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('h1', { class: 'font-extrabold text-3xl' }, trans('Edit Room')),
	})),
}
</script>

<script setup>
	const props = defineProps({
		room: Object,
	});
</script>

<template>
	<RoomForm :hotel="room.hotel" :room="room" />
</template>
