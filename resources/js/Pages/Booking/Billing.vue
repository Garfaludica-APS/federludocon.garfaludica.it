<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import BookingLayout from '@/Layouts/BookingLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Billing details' }, () => h(BookingLayout, () => page)),
}
</script>

<script setup>
</script>

<template>
</template>
