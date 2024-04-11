<script setup>
import { ref, computed, onMounted } from 'vue'
import { loadLanguageAsync, getActiveLanguage } from 'laravel-vue-i18n';

const emit = defineEmits(['switchLang']);

const props = defineProps({
	languages: {
		type: Array,
		default: () => ['en', 'it'],
	},
})


function getLanguageIndex(language) {
	return props.languages.indexOf(language);
}

const currentLanguage = ref('it');

const nextLanguage = computed(() => {
	const idx = (getLanguageIndex(currentLanguage.value) + 1) % props.languages.length;
	return props.languages[idx];
});

function setCookie(lang, days = 365) {
	const date = new Date();
	date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
	let expires = "expires=" + date.toUTCString();
	const domain = window.location.hostname;
	document.cookie = `lang=${lang};${expires};path=/;SameSite=Strict;domain=${domain};secure`;
}

function loadNextLanguage() {
	currentLanguage.value = nextLanguage.value;
	loadLanguageAsync(currentLanguage.value);
	setCookie(currentLanguage.value);
	emit('switchLang', currentLanguage.value);
}

onMounted(() => {
	currentLanguage.value = getActiveLanguage();
})
</script>

<template>
	<button @click="loadNextLanguage()">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-1/2 w-auto mx-auto">
			<path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
		</svg>
		<span class="block mx-auto text-sm">{{ nextLanguage.toUpperCase() }}</span>
	</button>
</template>
