<script setup>
import { Head } from '@inertiajs/vue3';
import { Modal } from '../../../vendor/emargareten/inertia-modal';

defineProps({
	title: String,
});
</script>

<template>
	<Head :title="$t(title)" />
	<slot />
	<Modal />
</template>
