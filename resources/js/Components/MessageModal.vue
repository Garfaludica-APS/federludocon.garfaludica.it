<script setup>
import { ref, computed, watch, onMounted } from 'vue';
// import Modal from './Modal.vue';
// import ActionButton from './ActionButton.vue';

const emit = defineEmits(['close']);

const props = defineProps({
	show: {
		type: Boolean,
		default: false,
	},
	maxWidth: {
		type: String,
		default: '2xl',
	},
	closeable: {
		type: Boolean,
		default: true,
	},
	type: {
		type: String,
		default: 'default',
	},
	timeout: {
		type: Number,
		default: null,
	},
});

const actualTimeout = computed(() => props.timeout ? props.timeout : 0);
const remaining = ref(0);

const iconPath = computed(() => {
	switch (props.type) {
		case 'success':
			return 'M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z';
		case 'warning':
			return 'M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z';
		case 'error':
			return 'M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z';
		case 'info':
			return 'M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z';
		default:
			return null;
	}
});

const iconColor = computed(() => {
	switch (props.type) {
		case 'success':
			return 'text-green-600 dark:text-green-400';
		case 'warning':
			return 'text-yellow-600 dark:text-yellow-400';
		case 'error':
			return 'text-red-600 dark:text-red-400';
		case 'info':
			return 'text-indigo-600 dark:text-indigo-400';
		default:
			return 'text-black dark:text-white';
	}
});

const close = () => {
	emit('close');
};


var interval;

const progress = () => {
	remaining.value--;
	if (remaining.value <= 0) {
		clearInterval(interval);
		close();
	}
};

watch(actualTimeout, (value) => {
	if (value <= 0) {
		if (interval)
			clearInterval(interval);
		return;
	}

	remaining.value = Math.ceil(value / 1000);
	interval = setInterval(progress, 1000);
});
</script>

<template>
	<Modal
		:show="show"
		:max-width="maxWidth"
		:closeable="closeable"
		@close="close"
	>
		<div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
			<div class="sm:flex sm:items-start">
				<div v-if="iconPath" class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
					<svg class="h-6 w-6" :class="iconColor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" :d="iconPath" />
					</svg>
				</div>

				<div class="mt-3 text-center sm:mt-0 sm:ms-4 sm:text-start">
					<div class="text-lg text-gray-900 dark:text-gray-100">
						<slot />
					</div>
				</div>
			</div>
		</div>

		<div v-if="closeable" class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-end">
			<ActionButton
				class="bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 ms-3"
				@click="close"
			>{{ remaining > 0 ? $t('Close (:timer)', { 'timer': remaining }) : $t('Close') }}</ActionButton>
		</div>
	</Modal>
</template>
