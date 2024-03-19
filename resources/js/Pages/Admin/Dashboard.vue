<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(AdminLayout, { title: 'Dashboard' }, {
		default: () => page,
		header: () => h('span', trans('Header')),
	}),
}
</script>

<script setup>
</script>

<template>
	aaa
</template>
