<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Login' }, () => h(AuthLayout, () => page)),
}
</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import ActionButton from '@/Components/ActionButton.vue';

const form = useForm({
	username: '',
	password: '',
	remember: false,
});

const passwordInput = ref(null);

const login = () => {
	form.post(route('auth.login'), {
		onError: () => passwordInput.value.focus(),
		onFinish: () => form.reset('password'),
	});
};
</script>

<template>
	<form class="mt-8 bg-white dark:bg-gray-900 dark:text-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
		<div class="px-10 py-12">
			<h1 class="text-3xl font-bold text-center">{{ $t('Login') }}</h1>
			<h2 class="mt-4 text-xl font-bold text-center">{{ $t('Reserved Area') }}</h2>
			<div class="w-24 mx-auto mt-6 border-b-2" />
			<div v-if="form.errors.login" class="text-red-500 text-sm mt-6">{{ form.errors.login }}</div>
			<TextInput
				v-model="form.username"
				:label="$t('Username or Email')"
				:error="form.errors.username"
				type="text"
				class="mt-10"
				:placeholder="$t('Username or Email')"
				autocomplete="username"
				autofocus
				autocapitalize="off"
				@keydown.enter.prevent="passwordInput.focus()"
			/>
			<TextInput
				ref="passwordInput"
				v-model="form.password"
				:label="$t('Password')"
				:error="form.errors.password"
				type="password"
				class="mt-6"
				:placeholder="$t('Password')"
				autocomplete="current-password"
				autocapitalize="off"
			/>
			<label class="flex items-center mt-6 select-none" for="remember">
				<input
					v-model="form.remember"
					type="checkbox"
					id="remember"
					class="mr-1"
				/>
				<span class="text-sm">{{ $t('Remember me') }}</span>
			</label>
		</div>
		<div class="flex space-x-4 justify-between px-10 py-4 items-center bg-gray-100 dark:bg-slate-800">
			<Link href="#" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200">{{ $t('Forgot your password?') }}</Link>
			<ActionButton :disabled="form.username.length < 3 || form.password.length < 8 || form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 disabled:bg-indigo-600/50 disabled:text-gray-100 ml-auto" classes="text-xl rounded-md font-bold" type="submit">
				{{ $t('Login') }}
			</ActionButton>
		</div>
	</form>
</template>
