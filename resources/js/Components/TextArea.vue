<script setup>
import { onMounted, ref } from 'vue';
import { v4 as uuid } from 'uuid';

defineProps({
	id: {
		type: String,
		default: () => `text-area-${uuid()}`,
	},
	modelValue: String,
	error: String,
	label: String,
	rows: {
		type: Number,
		default: 20,
	},
	cols: {
		type: Number,
		default: 80,
	},
});

defineEmits(['input', 'update:modelValue']);

const input = ref(null);

const updateValue = (event) => {
	if (event.key === 'Tab') {
		event.preventDefault();
		const start = event.target.selectionStart;
		const end = event.target.selectionEnd;
		const value = event.target.value;
		event.target.value = `${value.substring(0, start)}\t${value.substring(end)}`;
		event.target.selectionStart = start + 1;
		event.target.selectionEnd = start + 1;
	}
};

onMounted(() => {
	if (input.value.hasAttribute('autofocus')) {
		input.value.focus();
	}
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
	<div :class="$attrs.class">
		<label v-if="label" class="block mb-2 mt-4" :for="id">{{ label }}</label>
		<textarea
			ref="input" :id="id" :class="{ 'border-red-500': error }" :rows="rows" :cols="cols"
			v-bind="{ ...$attrs, class: null }"
			class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
			:value="modelValue"
			@input="$emit('update:modelValue', $event.target.value)"
			@keydown="updateValue($event)"
		></textarea>
		<div v-if="error" class="text-red-500 text-sm">{{ error }}</div>
	</div>
</template>
