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
			h('div', { class: 'flex flex-1 flex-wrap justify-end align-center' }, [
				h(ActionButton, { id: "invite-admin-button", class: 'bg-lime-600 grow sm:grow-0 hover:bg-lime-500 active:bg-lime-700 focus:ring-lime-500', icon: 'user-plus' }, () => trans('Invite Admin')),
			]),
		]),
	})),
}
</script>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
	admins: Array,
	invitations: Array,
	canCreateInvitations: Boolean,
	hotels: Array,
});

const page = usePage();
const admin = computed(() => page.props.auth.admin);

const selectableHotels = computed(() => props.hotels.filter((hotel) => admin.value.hotels.some((adminHotel) => adminHotel.id === hotel.id)));

const invitingAdmin = ref(false);
const inviteEmailInput = ref(null);

const form = useForm({
	email: '',
	superAdmin: false,
	selectedHotels: [],
});

const startInvitingAdmin = () => {
	if (!props.canCreateInvitations)
		return;
	invitingAdmin.value = true;

	setTimeout(() => inviteEmailInput.value.focus(), 250);
};

const inviteAdmin = () => {
	form.post(route('admin.invitations.store'), {
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

const hasInvitations = computed(() => props.invitations !== undefined && props.invitations.length > 0);

const deletingAdmin = ref(null);

const adminDeleteForm = useForm({});

const deleteAdmin = () => {
	adminDeleteForm.delete(route('admin.admins.destroy', deletingAdmin.value), {
		preserveScroll: true,
		onFinish: () => deletingAdmin.value = null,
	});
};

const deletingInvitation = ref(null);

const invitationDeleteForm = useForm({});

const deleteInvitation = () => {
	invitationDeleteForm.delete(route('admin.invitations.destroy', deletingInvitation.value), {
		preserveScroll: true,
		onFinish: () => deletingInvitation.value = null,
	});
};

var inviteButton;
onMounted(() => {
	form.superAdmin = admin.value.is_super_admin;
	form.defaults();
	inviteButton = document.getElementById('invite-admin-button');
	if (!props.canCreateInvitations) {
		inviteButton.style.display = 'none';
		return;
	}
	inviteButton.addEventListener('click', startInvitingAdmin);
});

onUnmounted(() => {
	if (!props.canCreateInvitations)
		return;
	inviteButton.removeEventListener('click', startInvitingAdmin);
});
</script>

<template>
	<section class="mb-6">
		<h2 class="font-bold text-xl py-4">{{ $t('Administrators') }}</h2>
		<div class="rounded-2xl dark:bg-slate-900/70 bg-white">
			<table class="cssTable">
				<thead>
					<tr>
						<th>{{ $t('Username') }}</th>
						<th>{{ $t('Email') }}</th>
						<th>{{ $t('Super Admin') }}</th>
						<th>{{ $t('Hotels') }}</th>
						<th>{{ $t('Invited By') }}</th>
						<th />
					</tr>
				</thead>
				<tbody>
					<tr :key="admin.id">
						<td data-label="Username">
							{{ admin.username }}
						</td>
						<td data-label="Email">
							{{ admin.email }}
						</td>
						<td data-label="Super Admin">
							{{ admin.is_super_admin ? $t('Yes') : $t('No') }}
						</td>
						<td data-label="Hotels">
							<ul v-if="!admin.is_super_admin">
								<li v-for="hotel in admin.hotels" :key="hotel.id">
									{{ hotel.name }}
								</li>
							</ul>
						</td>
						<td v-if="admin.inviter" data-label="Invited By">
							{{ admin.inviter.username }}
						</td>
						<td v-else data-label="Invited By">
							{{ $t('N/A') }}
						</td>
						<td data-label="Actions" />
					</tr>
					<tr v-for="adm in admins" :key="adm.id">
						<td data-label="Username">
							{{ adm.username }}
						</td>
						<td data-label="Email">
							{{ adm.email }}
						</td>
						<td data-label="Super Admin">
							{{ adm.is_super_admin ? $t('Yes') : $t('No') }}
						</td>
						<td data-label="Hotels">
							<ul>
								<li v-for="hotel in adm.hotels" :key="hotel.id">
									{{ hotel.name }}
								</li>
							</ul>
						</td>
						<td v-if="adm.inviter" data-label="Invited By">
							{{ adm.inviter.username }}
						</td>
						<td v-else data-label="Invited By">
							{{ $t('N/A') }}
						</td>
						<td data-label="actions">
							<div class="flex items-center justify-center">
								<IconButton v-if="adm.can.delete" icon="trash" class="bg-red-600 hover:bg-red-500 active:bg-red-700 focus:ring-red-500" @click="deletingAdmin = adm" />
							</div>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<td colspan="6" />
				</tfoot>
			</table>
		</div>
	</section>
	<section>
		<h2 class="font-bold text-xl py-4">{{ $t('Invitations') }}</h2>
		<div class="rounded-2xl dark:bg-slate-900/70 bg-white">
			<table class="cssTable">
				<thead>
					<tr>
						<th>{{ $t('Email') }}</th>
						<th>{{ $t('Super Admin') }}</th>
						<th>{{ $t('Invited By') }}</th>
						<th>{{ $t('Expiration Time') }}</th>
						<th />
					</tr>
				</thead>
				<tbody v-if="hasInvitations">
					<tr v-for="invitation in invitations" :key="invitation.id">
						<td data-label="Email">
							{{ invitation.email }}
						</td>
						<td data-label="Super Admin">
							{{ invitation.is_super_admin ? $t('Yes') : $t('No') }}
						</td>
						<td data-label="Invited By">
							{{ invitation.creator.username }}
						</td>
						<td data-label="Expiration Time">
							{{ invitation.expires_at }}
						</td>
						<td data-label="actions" class="flex items-center justify-center">
							<IconButton icon="trash" class="bg-red-600 hover:bg-red-500 active:bg-red-700 focus:ring-red-500" @click="deletingInvitation = invitation" />
						</td>
					</tr>
				</tbody>
				<tbody v-else>
					<tr>
						<td colspan="5" class="italic">
							{{$t('No invitation found.')}}
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

			<div class="mt-4" v-if="admin.is_super_admin">
				<SwitchGroup>
					<div class="flex items-center">
						<Switch v-model="form.superAdmin" :class='form.superAdmin ? "bg-blue-600" : "bg-gray-200"' class="relative inline-flex h-5 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
							<span :class='form.superAdmin ? "translate-x-6" : "translate-x-1"' class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform" />
						</Switch>
						<SwitchLabel class="ml-4">{{ $t('Super Admin') }}</SwitchLabel>
					</div>
				</SwitchGroup>
				<InputError :message="form.errors.superAdmin" class="mt-2" />
			</div>
			<div class="mt-4" v-show="!form.superAdmin">
				<Listbox v-model="form.selectedHotels" multiple>
					<ListboxLabel>{{ $t('Select the hotels that the admin can manage:') }}</ListboxLabel>
					<ListboxOptions static>
						<ListboxOption v-for="hotel in selectableHotels" :key="hotel.id" :value="hotel" v-slot="{ active, selected }">
							<li :class="{ 'bg-blue-500 text-white': selected, 'text-gray-900 dark:text-gray-100': !selected }" class="h-6 cursor-pointer">
								<svg v-show="selected" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline-block w-6 h-6">
									<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
								</svg>
								<span :class=' selected ? "ml-0" : "ml-6" '>{{ $t(hotel.name) }}</span>
							</li>
						</ListboxOption>
					</ListboxOptions>
				</Listbox>
				<InputError :message="form.errors.selectedHotels" class="mt-2" />
			</div>
		</template>

		<template #footer>
			<SecondaryButton @click="closeModal">
				{{$t('Cancel')}}
			</SecondaryButton>

			<ActionButton
				icon="user-plus"
				class="bg-lime-600 hover:bg-lime-500 active:bg-lime-700 focus:ring-lime-500 ms-3 disabled:bg-lime-600/50 disabled:text-gray-100"
				:class="{ 'opacity-25': form.processing }"
				:disabled="form.email.length < 3 || (!form.superAdmin && form.selectedHotels.length == 0) || form.processing"
				@click="inviteAdmin"
			>
				{{$t('Invite Admin')}}
			</ActionButton>
		</template>
	</DialogModal>

	<ConfirmationModal :show="deletingAdmin != null" @close="deletingAdmin = null">
		<template #title>
			{{ $t('Delete Admin') }} - {{ deletingAdmin.username }}
		</template>

		<template #content>
			{{ $t('Are you sure you want to delete this admin?') }}<br />
			{{ $t('Admin') }}: <span class="font-bold">{{ deletingAdmin.username }}</span><br />
			<span class="italic text-red-500">{{ $t('This action cannot be undone.') }}</span>
		</template>

		<template #footer>
			<SecondaryButton @click="deletingAdmin = null">
				{{ $t('Cancel') }}
			</SecondaryButton>

			<ActionButton
				icon="trash"
				class="bg-red-600 hover:bg-red-500 active:bg-red-700 focus:ring-red-500 ms-3"
				:class="{ 'opacity-25': form.processing }"
				:disabled="form.processing"
				@click="deleteAdmin"
			>
				{{ $t('Delete Admin') }}
			</ActionButton>
		</template>
	</ConfirmationModal>

	<ConfirmationModal :show="deletingInvitation != null" @close="deletingInvitation = null">
		<template #title>
			{{ $t('Delete Invitation') }}
		</template>

		<template #content>
			{{ $t('Are you sure you want to delete this invitation?') }}
		</template>

		<template #footer>
			<SecondaryButton @click="deletingInvitation = null">
				{{ $t('Cancel') }}
			</SecondaryButton>

			<ActionButton
				icon="trash"
				class="bg-red-600 hover:bg-red-500 active:bg-red-700 focus:ring-red-500 ms-3"
				:class="{ 'opacity-25': form.processing }"
				:disabled="form.processing"
				@click="deleteInvitation"
			>
				{{ $t('Delete Invitation') }}
			</ActionButton>
		</template>
	</ConfirmationModal>
</template>
