<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Dashboard' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('h1', { class: 'font-extrabold text-3xl' }, trans('Dashboard - Admin Panel')),
	})),
}
</script>

<template>
	{{ $t('There is nothing here... yet. Please select an item from the left sidebar.') }}
</template>
