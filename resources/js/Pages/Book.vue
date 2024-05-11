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
		<div class="max-w-5xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="bg-white dark:bg-gray-800 overflow-hidden text-black dark:text-white shadow-xl sm:rounded-lg">
				<div>
					<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
						<h1 class="text-4xl font-bold">{{ $t('Booking Portal') }}</h1>
						<p class="mt-4">{{ $t('Garfaludica APS can\'t wait to see you at the GobCon!') }}</p>
						<p class="mt-4">{{ $t('Please enter your email to start booking. We will send you an email with the instructions to proceed with your order.') }}</p>
						<p class="mt-2">{{ $t('Make sure to check your spam folder if you do not receive the email within a few minutes.') }}</p>
						<p class="mt-2">{{ $t('REFUNDS: You can ask for a refund until 24 hours before the event starts. You will receive an email with a link to cancel your order after your order is complete. Please note that orders are not editable: if you need to change your order, you must cancel the previous order and place another one. Refunds are processed manually in a couple of days.') }}</p>
						<p class="mt-2">{{ $t('If you are having any issues, contact us (see the "Contact" page).') }}</p>
						<form class="mx-auto mt-4 max-w-sm" @submit.prevent="submit">
							<MazInput
									v-model="form.email"
									ref="emailInput"
									:label="$t('Email')"
									:error="form.errors.email"
									type="text"
									class="my-4"
									:placeholder="$t('Enter your email')"
									autocapitalize="off"
									block
							/>
							<div class="mx-auto max-w-fit"><div v-html="recaptchaFormSnippet"></div></div>
							<MazBtn size="xl" :disabled="form.processing || form.email.length < 3 || !recaptchaVerified" type="submit" block class="mt-4">
								{{ $t('Start Booking!') }}
							</MazBtn>
							<div v-if="form.errors.global" class="text-red-500 text-sm mt-4">{{ form.errors.global }}</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
