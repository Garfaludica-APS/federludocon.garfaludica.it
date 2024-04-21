<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Booking Portal' }, () => h(PubLayout, () => page)),
}
</script>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { getActiveLanguage } from 'laravel-vue-i18n';
import TextInput from '@/Components/TextInput.vue';
import ActionButton from '@/Components/ActionButton.vue';

const props = defineProps({
	recaptchaScriptTagSrc: String,
	recaptchaFormSnippet: String,
});

const recaptchaSuccess = () => {
	recaptchaVerified.value = true;
};

const recaptchaExpired = () => {
	recaptchaVerified.value = false;
};

const recaptchaError = () => {
	recaptchaVerified.value = false;
};

const recaptchaVerified = ref(false);
const emailInput = ref(null);

const form = useForm({
	email: '',
	g_recaptcha_response: null,
});

function submit()
{
	form.g_recaptcha_response = document.getElementById('g-recaptcha-response').value;
	form.post(route('book.start'), {
		preserveScroll: true,
		onError: () => emailInput.value.focus(),
		onFinish: () => form.reset(),
	});
}

onMounted(() => {
	if (typeof window === 'undefined')
		return;
	window.recaptchaSuccess = recaptchaSuccess;
	window.recaptchaExpired = recaptchaExpired;
	window.recaptchaError = recaptchaError;
});
</script>

<template>
	<Head>
		<component is="script" :src="recaptchaScriptTagSrc" async defer></component>
	</Head>
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div>
					<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
						<form @submit.prevent="submit">
							<TextInput
									v-model="form.email"
									ref="emailInput"
									:label="$t('Email')"
									:error="form.errors.email"
									type="text"
									class="mt-10"
									:placeholder="$t('Enter your email')"
									autocomplete="email"
									autocapitalize="off"
							/>
							<div v-html="recaptchaFormSnippet"></div>
							<ActionButton :disabled="form.processing || form.email.length < 3 || !recaptchaVerified" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 disabled:bg-indigo-600/50 disabled:text-gray-100 mx-auto" classes="text-xl rounded-md font-bold" type="submit">
								{{ $t('Start Booking!') }}
							</ActionButton>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
