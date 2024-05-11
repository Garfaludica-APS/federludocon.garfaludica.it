<script setup>
import { ref, computed, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Modal } from 'inertia-modal';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const page = usePage();

defineProps({
	title: String,
});

function showToast(msg, pos = 'bottom-right', timeout = 5000, style = 'default')
{
	const t = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
	toast(msg, {
		autoClose: timeout,
		theme: t,
		position: pos,
		type: style,
	});
}

const showModal = ref(false);

const flash = computed(() => page.props.flash.message);

watch(flash, async (oldMsg, newMsg) => {
	if (!page.props.flash || !page.props.flash.message || page.props.flash.message === '' || newMsg === oldMsg)
		return;
	if (page.props.flash.location.startsWith('toast')) {
		const splitted = page.props.flash.location.split('-');
		let shortpos;
		if (splitted.length < 2)
			shortpos = 'br';
		else
			shortpos = splitted[1];
		let position;
		switch (shortpos) {
		case 'tl':
			position = 'top-left';
			break;
		case 'tc':
			position = 'top-center';
			break;
		case 'tr':
			position = 'top-right';
			break;
		case 'bl':
			position = 'bottom-left';
			break;
		case 'bc':
			position = 'bottom-center';
			break;
		case 'br':
		default:
			position = 'bottom-right';
		}
		showToast(page.props.flash.message, position, page.props.flash.timeout, page.props.flash.style);
	} else if (page.props.flash.location === 'modal') {
		showModal.value = true;
	} else if (page.props.flash.location === 'banner') {
		// TODO: show banner
	}
});

const closeModal = () => {
	showModal.value = false;
};
</script>

<template>
	<Head :title="$t(title)" />
	<slot />
	<MessageModal :show="showModal" :timeout="$page.props.flash.timeout ? $page.props.flash.timeout : null" :type="$page.props.flash.style" @close="closeModal">
		{{ $page.props.flash.message }}
	</MessageModal>
	<Modal />
</template>
