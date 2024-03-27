<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Create Account' }, () => h(
		AuthLayout, {
			backRoute: null,
		},
		() => page)),
}
</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import ActionButton from '@/Components/ActionButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const props = defineProps({
	token: {
		type: String,
		required: true,
	},
	invitation: {
		type: Object,
		default: null,
	},
	inviter: {
		type: Object,
		default: null,
	},
});

const form = useForm({
	email: route().params.email,
	token: props.token,
	username: '',
	password: '',
	password_confirmation: '',
});

const passwordInput = ref(null);
const confirmInput = ref(null);

const createAdmin = () => {
	form.post(route('admin.invitation.accept', {
		'invitation': props.invitation
	}), {
		onError: () => passwordInput.value.focus(),
		onFinish: () => form.reset(),
	});
};
</script>

<template>
	<form class="mt-8 bg-white dark:bg-gray-900 dark:text-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="createAdmin">
		<div class="px-10 py-12">
			<h1 class="text-3xl font-bold text-center">{{ $t('Create Account') }}</h1>
			<p v-if="inviter" class="mt-4 text-center">{{ $t('You have been invited by :name (:email) to create an administrator account.', {'name': inviter.username, 'email': inviter.email}) }}</p>
			<div class="w-24 mx-auto mt-6 border-b-2" />
			<FlashMessage v-if="$page.props.flash.location === 'page'" class="text-sm mt-6" />
			<div v-if="form.errors.global" class="text-red-500 text-sm mt-6">{{ form.errors.global }}</div>
			<input v-model="form.token" type="hidden" name="token" />
			<div v-if="form.errors.email" class="text-red-500 text-sm mt-6">{{ form.errors.email }}</div>
			<div v-if="form.errors.token" class="text-red-500 text-sm mt-6">{{ form.errors.token }}</div>
			<TextInput
				v-model="form.username"
				:label="$t('Username')"
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
				v-model="form.password"
				ref="passwordInput"
				:label="$t('Password')"
				:error="form.errors.password"
				type="password"
				class="mt-6"
				:placeholder="$t('Choose your Password')"
				autocomplete="new-password"
				autocapitalize="off"
				@keydown.enter.prevent="confirmInput.focus()"
			/>
			<TextInput
				v-model="form.password_confirmation"
				ref="confirmInput"
				:label="$t('Confirm your Password')"
				:error="form.errors.password_confirmation"
				type="password"
				class="mt-6"
				:placeholder="$t('Confirm your Password')"
				autocomplete="new-password"
				autocapitalize="off"
			/>
		</div>
		<!-- <div v-if="form.password_confirmation.length > 7 && form.password !== form.password_confirmation" class="text-red-500 text-sm mt-2">{{ $('Passwords do not match.') }}</div> -->
		<div class="flex space-x-4 justify-between px-10 py-4 items-center bg-gray-100 dark:bg-slate-800">
			<ActionButton :disabled="form.password.length < 8 || form.password !== form.password_confirmation || form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 disabled:bg-indigo-600/50 disabled:text-gray-100 ml-auto" classes="text-xl rounded-md font-bold" type="submit">
				{{ $t('Create Account') }}
			</ActionButton>
		</div>
	</form>
</template>
