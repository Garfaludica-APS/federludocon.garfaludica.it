<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'External Bookings' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('div', { class: 'w-full flex flex-wrap flex-col space-y-4 sm:space-y-0 sm:flex-row justify-between align-center whitespace-nowrap' }, [
			h('h1', { class: 'flex-1 font-extrabold text-3xl' }, trans('External Bookings')),
			h('div', { class: 'flex flex-1 flex-wrap justify-end align-center' }, [
				h(ActionButton, { id: "add-book-button", class: 'bg-lime-600 grow sm:grow-0 hover:bg-lime-500 active:bg-lime-700 focus:ring-lime-500', icon: 'user-plus' }, () => trans('Add External Booking')),
			]),
		]),
	})),
}
</script>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { getActiveLanguage } from 'laravel-vue-i18n';
import { router } from '@inertiajs/vue3';

const props = defineProps({
	hotel: Object,
	externalBookings: Array,
});

function startAddMeal() {
}

var addBookButton;
onMounted(() => {
	addBookButton = document.getElementById('add-book-button');
	addBookButton.classList.add('hidden');
});

onUnmounted(() => {
});
</script>

<template>
	<section class="mb-6">
		<h2 class="font-bold text-xl py-4">{{ $t('Sorry... Currently unavailable') }}</h2>
		<p>{{ $t('This feature is currently unavailable. Please check back later.') }}</p>
	</section>
</template>
