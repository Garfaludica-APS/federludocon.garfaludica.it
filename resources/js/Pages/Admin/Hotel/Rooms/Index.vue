<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Manage Rooms' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('div', { class: 'w-full flex flex-wrap flex-col space-y-4 sm:space-y-0 sm:flex-row justify-between align-center whitespace-nowrap' }, [
			h('h1', { class: 'flex-1 font-extrabold text-3xl' }, trans('Manage Rooms')),
			h('div', { class: 'flex flex-1 flex-wrap justify-end align-center' }, [
				h(ActionButton, { id: "add-room-button", class: 'bg-lime-600 grow sm:grow-0 hover:bg-lime-500 active:bg-lime-700 focus:ring-lime-500', icon: 'user-plus' }, () => trans('Add Rooms')),
			]),
		]),
	})),
}
</script>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { getActiveLanguage } from 'laravel-vue-i18n';
import { router } from '@inertiajs/vue3';

const props = defineProps({
	hotel: Object,
	rooms: Array,
	canCreateRooms: Boolean,
});

function startAddRoom() {
	router.visit(route('admin.hotel.rooms.create', { hotel: props.hotel }));
}

function editRoom(room) {
	router.visit(route('admin.hotel.rooms.edit', { hotel: props.hotel, room: room }));
}

const deletingRoom = ref(null);
const restoringRoom = ref(null);
const confirmDelete = ref(false);
const confirmRestore = ref(false);

function deleteRoom() {
	router.delete(route('admin.hotel.rooms.destroy', { hotel: props.hotel, room: deletingRoom.value }), {
		preserveScroll: true,
		onFinish: () => {
			deletingRoom.value = null;
			confirmDelete.value = false;
		},
	});
}

function restoreRoom() {
	router.patch(route('admin.hotel.rooms.restore', { hotel: props.hotel, room: restoringRoom.value }), {
		preserveScroll: true,
		onFinish: () => {
			restoringRoom.value = null;
			confirmRestore.value = false;
		},
	});
}

var locale = 'en';
var addRoomButton;
onMounted(() => {
	locale = getActiveLanguage();
	addRoomButton = document.getElementById('add-room-button');
	if (!props.canCreateRooms)
		addRoomButton.classList.add('hidden');
	else
		addRoomButton.addEventListener('click', startAddRoom);
});

onUnmounted(() => {
	if (props.canCreateRooms)
		addRoomButton.removeEventListener('click', startAddRoom);
});
</script>

<template>
	<section class="mb-6">
		<h2 class="font-bold text-xl py-4">{{ $t('Rooms') }}</h2>
		<MazTable
			size="sm"
			color="secondary"
			sortable
			hoverable
			background-even
			:headers="[
				{ label: $t('Name'), key: 'name' },
				{ label: $t('Quantity'), key: 'quantity' },
			]"
			:rows="rooms"
		>
			<template #cell-name="{ row, value }">
				<span :class="{ 'line-through': row.can.restore }">{{ value[locale] }}</span>
			</template>
			<template #cell-quantity="{ row, value }">
				<span :class="{ 'line-through': row.can.restore }">{{ value }}</span>
			</template>

			<template #actions="{ row }">
				<div class="flex flex-row items-center space-x-2">
				<MazBtn v-if="row.can.update" fab size="xs" color="primary" icon="storage/icons/pencil-square" @click="editRoom(row)" />
				<MazBtn v-if="row.can.delete" fab size="xs" color="danger" icon="storage/icons/trash" @click="deletingRoom = row; confirmDelete = true" />
				<MazBtn v-if="row.can.restore" fab size="xs" color="secondary" icon="storage/icons/arrow-path" @click="restoringRoom = row; confirmRestore = true" />
				</div>
			</template>
		</MazTable>
	</section>
	<MazDialog v-model="confirmDelete" :title="$t('Delete Room')">
		<p>{{ $t('Are you sure you want to delete this room?') }}</p>
		<p>{{ $t('Room: :name', { name: deletingRoom.name[locale] }) }}</p>
		<template #footer="{ close }">
			<MazBtn color="danger" @click="close(); deleteRoom()" class="mx-2">{{ $t('Delete') }}</MazBtn>
			<MazBtn @click="close">{{ $t('Cancel') }}</MazBtn>
		</template>
	</MazDialog>
	<MazDialog v-model="confirmRestore" :title="$t('Restore Room')">
		<p>{{ $t('Are you sure you want to restore this room?') }}</p>
		<p>{{ $t('Room: :name', { name: restoringRoom.name[locale] }) }}</p>
		<template #footer="{ close }">
			<MazBtn color="secondary" @click="close(); restoreRoom()" class="mx-2">{{ $t('Restore') }}</MazBtn>
			<MazBtn @click="close">{{ $t('Cancel') }}</MazBtn>
		</template>
	</MazDialog>
</template>
