<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Manage Meals' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('div', { class: 'w-full flex flex-wrap flex-col space-y-4 sm:space-y-0 sm:flex-row justify-between align-center whitespace-nowrap' }, [
			h('h1', { class: 'flex-1 font-extrabold text-3xl' }, trans('Manage Meals')),
			h('div', { class: 'flex flex-1 flex-wrap justify-end align-center' }, [
				h(ActionButton, { id: "add-meal-button", class: 'bg-lime-600 grow sm:grow-0 hover:bg-lime-500 active:bg-lime-700 focus:ring-lime-500', icon: 'user-plus' }, () => trans('Add Meal')),
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
	meals: Array,
	canCreateMeals: Boolean,
});

function startAddMeal() {
	router.visit(route('admin.hotel.meals.create', { hotel: props.hotel }));
}

function editMeal(meal) {
	router.visit(route('admin.hotel.meals.edit', { hotel: props.hotel, meal: meal }));
}

const deletingMeal = ref(null);
const restoringMeal = ref(null);
const confirmDelete = ref(false);
const confirmRestore = ref(false);

function deleteMeal() {
	router.delete(route('admin.hotel.meals.destroy', { hotel: props.hotel, meal: deletingMeal.value }), {
		preserveScroll: true,
		onFinish: () => {
			deletingMeal.value = null;
			confirmDelete.value = false;
		},
	});
}

function restoreMeal() {
	router.patch(route('admin.hotel.meals.restore', { hotel: props.hotel, meal: restoringMeal.value }), {
		preserveScroll: true,
		onFinish: () => {
			restoringMeal.value = null;
			confirmRestore.value = false;
		},
	});
}

var locale = 'en';
var addMealButton;
onMounted(() => {
	locale = getActiveLanguage();
	addMealButton = document.getElementById('add-meal-button');
	if (!props.canCreateMeals)
		addMealButton.classList.add('hidden');
	else
		addMealButton.addEventListener('click', startAddMeal);
});

onUnmounted(() => {
	if (props.canCreateMeals)
		addMealButton.removeEventListener('click', startAddMeal);
});
</script>

<template>
	<section class="mb-6">
		<h2 class="font-bold text-xl py-4">{{ $t('Meals') }}</h2>
		<MazTable
			size="sm"
			color="secondary"
			sortable
			hoverable
			background-even
			:headers="[
				{ label: $t('Type'), key: 'type' },
				{ label: $t('Menu'), key: 'menu' },
				{ label: $t('Price'), key: 'price' },
				{ label: $t('Time'), key: 'meal_time' },
				{ label: $t('Reservable'), key: 'reservable' },
			]"
			:rows="meals"
		>
			<template #cell-type="{ row, value }">
				<span class="capitalize" :class="{ 'line-through': row.can.restore }">{{ $t(value) }}</span>
			</template>
			<template #cell-menu="{ row, value }">
				<span class="capitalize" :class="{ 'line-through': row.can.restore }">{{ $t(value) }}</span>
			</template>
			<template #cell-price="{ row, value }">
				<span :class="{ 'line-through': row.can.restore }">{{ value }}</span>
			</template>
			<template #cell-time="{ row, value }">
				<span :class="{ 'line-through': row.can.restore }">{{ value }}</span>
			</template>
			<template #cell-reservable="{ row, value }">
				<span :class="{ 'line-through': row.can.restore }">{{ value ? $t('Yes') : $t('No') }}</span>
			</template>

			<template #actions="{ row }">
				<div class="flex flex-row items-center space-x-2">
				<MazBtn v-if="row.can.update" fab size="xs" color="primary" icon="storage/icons/pencil-square" @click="editMeal(row)" />
				<MazBtn v-if="row.can.delete" fab size="xs" color="danger" icon="storage/icons/trash" @click="deletingMeal = row; confirmDelete = true" />
				<MazBtn v-if="row.can.restore" fab size="xs" color="secondary" icon="storage/icons/arrow-path" @click="restoringMeal = row; confirmRestore = true" />
				</div>
			</template>
		</MazTable>
	</section>
	<MazDialog v-model="confirmDelete" :title="$t('Delete Meal')">
		<p>{{ $t('Are you sure you want to delete this meal?') }}</p>
		<p>{{ $t('Meal: :type - :menu', { type: deletingMeal.type, menu: deletingMeal.menu }) }}</p>
		<template #footer="{ close }">
			<MazBtn color="danger" @click="close(); deleteMeal()" class="mx-2">{{ $t('Delete') }}</MazBtn>
			<MazBtn @click="close">{{ $t('Cancel') }}</MazBtn>
		</template>
	</MazDialog>
	<MazDialog v-model="confirmRestore" :title="$t('Restore Meal')">
		<p>{{ $t('Are you sure you want to restore this meal?') }}</p>
		<p>{{ $t('Meal: :type - :menu', { type: restoringMeal.type, menu: restoringMeal.menu }) }}</p>
		<template #footer="{ close }">
			<MazBtn color="secondary" @click="close(); restoreMeal()" class="mx-2">{{ $t('Restore') }}</MazBtn>
			<MazBtn @click="close">{{ $t('Cancel') }}</MazBtn>
		</template>
	</MazDialog>
</template>
