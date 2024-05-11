<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Forgot Password' }, () => h(
		AuthLayout, {
			backRoute: 'auth.login',
			backText: 'Back to Login',
		},
		() => page)),
}
</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
	email: '',
});

const emailInput = ref(null);

const sendEmail = () => {
	form.post(route('auth.password.email'), {
		onError: () => emailInput.value.focus(),
		onFinish: () => form.reset(),
	});
};
</script>

<template>
	<FlashMessage v-if="$page.props.flash.location === 'page'" class="text-lg text-center px-10 py-12 mt-8 bg-white dark:bg-gray-900 rounded-lg shadow-xl overflow-hidden" />
	<form v-else class="mt-8 bg-white dark:bg-gray-900 dark:text-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="sendEmail">
		<div class="px-10 py-12">
			<h1 class="text-3xl font-bold text-center">{{ $t('Password Recovery') }}</h1>
			<p class="mt-4 text-center">{{ $t('Enter your email address and we will send you a link to reset your password.') }}</p>
			<div class="w-24 mx-auto mt-6 border-b-2" />
			<TextInput
				v-model="form.email"
				ref="emailInput"
				:label="$t('Email')"
				:error="form.errors.email"
				type="email"
				class="mt-10"
				:placeholder="$t('Email')"
				autocomplete="email"
				autofocus
				autocapitalize="off"
			/>
		</div>
		<div class="flex space-x-4 justify-between px-10 py-4 items-center bg-gray-100 dark:bg-slate-800">
			<ActionButton :disabled="form.email.length < 3 || form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 disabled:bg-indigo-600/50 disabled:text-gray-100 ml-auto" classes="text-xl rounded-md font-bold" type="submit">
				{{ $t('Recover Password') }}
			</ActionButton>
		</div>
	</form>
</template>
