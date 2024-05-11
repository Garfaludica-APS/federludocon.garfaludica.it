<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { getActiveLanguage, trans } from 'laravel-vue-i18n';
import { router } from '@inertiajs/vue3';

const props = defineProps({
	hotel: Object,
	meal: {
		type: Object,
		default: null,
	},
});

const form = useForm({
	type: '',
	menu: '',
	price: 0.0,
	meal_time: '',
	reservable: true,
});

function setTimeIfEmpty() {
	if (form.meal_time)
		return;
	if (form.type === 'breakfast')
		form.meal_time = '07:00';
	else if (form.type === 'lunch')
		form.meal_time = '13:00';
	else if (form.type === 'dinner')
		form.meal_time = '20:00';
}

const mealTypes = [
	{ value: 'breakfast', label: trans('Breakfast') },
	{ value: 'lunch', label: trans('Lunch') },
	{ value: 'dinner', label: trans('Dinner') },
];

const mealMenus = [
	{ value: 'standard', label: trans('Standard') },
	{ value: 'vegetarian', label: trans('Vegetarian') },
	{ value: 'vegan', label: trans('Vegan') },
];

function submitForm() {
	if (props.meal)
		form.put(route('admin.hotel.meals.update', { hotel: props.hotel, meal: props.meal }));
	else
		form.post(route('admin.hotel.meals.store', { hotel: props.hotel }));
}

function cancelForm() {
	router.visit(route('admin.hotel.meals.index', { hotel: props.hotel }));
}

onMounted(() => {
	if (props.meal) {
		form.type = props.meal.type;
		form.menu = props.meal.menu;
		form.price = props.meal.price;
		form.meal_time = props.meal.meal_time;
		form.reservable = props.meal.reservable;
	}
});
</script>

<template>
	<form class="mt-8 bg-white dark:bg-gray-900 dark:text-white rounded-lg shadow-xl overflow-visible" @submit.prevent="submitForm">
		<div class="px-10 py-12">
			<FlashMessage v-if="$page.props.flash.location === 'page'" class="text-sm mt-6" />
			<div v-if="form.errors.global" class="text-red-500 text-sm mt-6">{{ form.errors.global }}</div>
			<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-4 md:space-y-0 mt-6">
				<MazSelect
					v-model="form.type"
					:options="mealTypes"
					:label="$t('Meal type')"
					option-value-key="value"
					option-label-key="label"
					option-input-value-key="label"
					required
					@selected-option="setTimeIfEmpty"
				/>
				<MazSelect
					v-model="form.menu"
					:options="mealMenus"
					:label="$t('Menu')"
					option-value-key="value"
					option-label-key="label"
					option-input-value-key="label"
					required
				/>
			</div>
			<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-4 md:space-y-0 mt-6">
				<MazInputPrice
					v-model="form.price"
					:label="$t('Price')"
					currency="EUR"
					:locale="getActiveLanguage()"
					min="0"
					required
				/>
				<MazPicker
					v-model="form.meal_time"
					format="HH:mm"
					:error="form.errors.meal_time ? true : false"
					:hint="form.errors.meal_time"
					:label="$t('Meal time')"
					only-time
					required
				/>
				<div>
					<MazSwitch
						v-model="form.reservable"
						color="primary"
					/> <span class="text-sm">{{ $t('Reservable') }}</span>
				</div>
			</div>
			<div class="flex justify-center mt-6 mx-auto">
				<MazBtn type="submit" size="lg" color="primary" class="mx-2" :disabled="form.processing">{{ $t('Save') }}</MazBtn>
				<MazBtn type="button" size="lg" color="transparent" class="mx-2" @click="cancelForm">{{ $t('Cancel') }}</MazBtn>
			</div>
		</div>
	</form>
</template>
