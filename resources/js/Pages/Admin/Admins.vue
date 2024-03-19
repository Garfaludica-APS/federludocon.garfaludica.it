<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(AdminLayout, { title: 'Administrators' }, {
		default: () => page,
		header: () => h('div', { class: 'w-full flex flex-wrap flex-col space-y-4 sm:space-y-0 sm:flex-row justify-between align-center whitespace-nowrap' }, [
			h('h1', { class: 'flex-1 font-extrabold text-3xl' }, trans('Admin Management')),
			h('div', { class: 'flex flex-1 flex-wrap justify-end align-center' }, [
				h(ActionButton, { class: 'bg-lime-600 grow sm:grow-0 hover:bg-lime-500 active:bg-lime-700 focus:ring-lime-500', icon: 'user-plus', href: route('admin') }, () => trans('Add Admin')),
			]),
		]),
	}),
}
</script>

<script setup>
import { computed } from 'vue';

const props = defineProps({
	admins: Array,
});

const hasAdmins = computed(() => props.admins !== undefined && props.admins.length > 0);
</script>

<template>
	<div class="rounded-2xl dark:bg-slate-900/70 bg-white">
		<table>
			<thead>
				<tr>
					<th>Username</th>
					<th>Email</th>
					<th />
				</tr>
			</thead>
			<tbody v-if="hasAdmins">
				<tr v-for="admin in admins" :key="admin.id">
					<td data-label="Username">
						{{ admin.name }}
					</td>
					<td data-label="Email">
						{{ admin.email }}
					</td>
					<td />
				</tr>
			</tbody>
			<tbody v-else>
				<tr>
					<td colspan="3" class="italic">
						No admins found.
					</td>
				</tr>
			</tbody>
			<tfoot>
				<td colspan="3" />
			</tfoot>
		</table>
	</div>
</template>
