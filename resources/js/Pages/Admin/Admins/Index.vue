<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Administrators' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('div', { class: 'w-full flex flex-wrap flex-col space-y-4 sm:space-y-0 sm:flex-row justify-between align-center whitespace-nowrap' }, [
			h('h1', { class: 'flex-1 font-extrabold text-3xl' }, trans('Admin Management')),
			h('div', { class: 'flex flex-1 flex-wrap justify-end align-center', id: "header-button-container" }, [
				h(ActionButton, { class: 'bg-lime-600 grow sm:grow-0 hover:bg-lime-500 active:bg-lime-700 focus:ring-lime-500', icon: 'user-plus' }, () => trans('Invite Admin')),
			]),
		]),
	})),
}
</script>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
	admin: Object,
	admins: Array,
	invites: Array,
});

const invitingAdmin = ref(false);
const inviteEmailInput = ref(null);

const form = useForm({
	email: '',
});

const startInvitingAdmin = () => {
	invitingAdmin.value = true;

	setTimeout(() => inviteEmailInput.value.focus(), 250);
};

const inviteAdmin = () => {
	form.post(route('invites.store'), {
		preserveScroll: true,
		onSuccess: () => closeModal(),
		onError: () => inviteEmailInput.value.focus(),
		onFinish: () => form.reset(),
	});
};

const closeModal = () => {
	invitingAdmin.value = false;

	form.reset();
};

const hasAdmins = computed(() => props.admins !== undefined && props.admins.length > 0);
const hasInvites = computed(() => props.invites !== undefined && props.invites.length > 0);

var headerBtnContainer;
onMounted(() => {
	headerBtnContainer = document.getElementById('header-button-container');
	headerBtnContainer.addEventListener('click', startInvitingAdmin);
});

onUnmounted(() => {
	headerBtnContainer.removeEventListener('click', startInvitingAdmin);
});
</script>

<template>
	<section class="mb-6">
		<h2 class="font-bold text-xl py-4">{{ $t('Administrators') }}</h2>
		<div class="rounded-2xl dark:bg-slate-900/70 bg-white">
			<table>
				<thead>
					<tr>
						<th>{{ $t('Username') }}</th>
						<th>{{ $t('Email') }}</th>
						<th>{{ $t('Super Admin') }}</th>
						<th />
					</tr>
				</thead>
				<tbody v-if="hasAdmins">
					<tr v-for="admin in admins" :key="admin.id">
						<td data-label="Username">
							{{ admin.username }}
						</td>
						<td data-label="Email">
							{{ admin.email }}
						</td>
						<td data-label="Super Admin">
							{{ admin.is_super_admin ? $t('Yes') : $t('No') }}
						</td>
						<td />
					</tr>
				</tbody>
				<tbody v-else>
					<tr>
						<td colspan="4" class="italic">
							No admins found.
						</td>
					</tr>
				</tbody>
				<tfoot>
					<td colspan="4" />
				</tfoot>
			</table>
		</div>
	</section>
	<section>
		<h2 class="font-bold text-xl py-4">{{ $t('Invites') }}</h2>
		<div class="rounded-2xl dark:bg-slate-900/70 bg-white">
			<table>
				<thead>
					<tr>
						<th>{{ $t('Email') }}</th>
						<th>{{ $t('Super Admin') }}</th>
						<th>{{ $t('Invited By') }}</th>
						<th>{{ $t('Expiration Time') }}</th>
						<th />
					</tr>
				</thead>
				<tbody v-if="hasInvites">
					<tr v-for="invite in invites" :key="invite.id">
						<td data-label="Email">
							{{ invite.email }}
						</td>
						<td data-label="Super Admin">
							{{ invite.is_super_admin ? $t('Yes') : $t('No') }}
						</td>
						<td data-label="Invited By">
							{{ invite.admin.username }}
						</td>
						<td data-label="Expiration Time">
							{{ invite.expires_at }}
						</td>
						<td />
					</tr>
				</tbody>
				<tbody v-else>
					<tr>
						<td colspan="5" class="italic">
							No invites found.
						</td>
					</tr>
				</tbody>
				<tfoot>
					<td colspan="5" />
				</tfoot>
			</table>
		</div>
	</section>
	<DialogModal :show="invitingAdmin" @close="closeModal">
		<template #title>
			{{ $t('Invite Admin') }}
		</template>

		<template #content>
			{{ $t('Invite an admin by entering their email address. They will receive an email with a link to accept the invitation.') }}

			<div class="mt-4">
				<TextInput
					ref="inviteEmailInput"
					v-model="form.email"
					type="email"
					class="mt-1 block w-3/4"
					placeholder="Email"
					autocomplete="email"
					@keyup.enter="inviteAdmin"
				/>

				<InputError :message="form.errors.email" class="mt-2" />
			</div>
		</template>

		<template #footer>
			<SecondaryButton @click="closeModal">
				Cancel
			</SecondaryButton>

			<ActionButton
				icon="user-plus"
				class="bg-lime-600 hover:bg-lime-500 active:bg-lime-700 focus:ring-lime-500 ms-3"
				:class="{ 'opacity-25': form.processing }"
				:disabled="form.processing"
				@click="inviteAdmin"
			>
				Invite Admin
			</ActionButton>
		</template>
	</DialogModal>
</template>
