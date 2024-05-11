<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { getActiveLanguage, trans } from 'laravel-vue-i18n';
import { router } from '@inertiajs/vue3';

const props = defineProps({
	hotel: Object,
	room: {
		type: Object,
		default: null,
	},
});

const form = useForm({
	name: {
		it: '',
		en: '',
	},
	description: {
		it: '',
		en: '',
	},
	quantity: 1,
	buy_options: [],
	checkin_time: '16:00',
	checkout_time: '11:00',
});

const enNameInput = ref(null);
const descriptionInput = ref(null);
const enDescriptionInput = ref(null);
const quantityInput = ref(null);
const checkinInput = ref(null);
const checkoutInput = ref(null);

function addBuyOption() {
	if (form.buy_options.length === 0) {
		form.buy_options.push({ people: 1, price: 0.0, included_meals: [], it: '', en: ''})
		return;
	}
	const lastPeople = form.buy_options[form.buy_options.length - 1].people;
	form.buy_options.push({ people: lastPeople + 1, price: 0.0, included_meals: [], it: '', en: '' })
}

const mealOptions = [
	{ value: 'breakfast', label: trans('Breakfast') },
	{ value: 'lunch', label: trans('Lunch') },
	{ value: 'dinner', label: trans('Dinner') },
];

function submitForm() {
	if (props.room)
		form.put(route('admin.hotel.rooms.update', { hotel: props.hotel, room: props.room }));
	else
		form.post(route('admin.hotel.rooms.store', { hotel: props.hotel }));
}

function cancelForm() {
	router.visit(route('admin.hotel.rooms.index', { hotel: props.hotel }));
}

onMounted(() => {
	console.log(props.room);
	if (props.room) {
		form.name = props.room.name;
		form.description = props.room.description;
		form.quantity = props.room.quantity;
		form.checkin_time = props.room.checkin_time;
		form.checkout_time = props.room.checkout_time;
		form.buy_options = props.room.buy_options;
	}
	if (form.buy_options.length === 0) {
		addBuyOption();
	}
});
</script>

<template>
	<form class="mt-8 bg-white dark:bg-gray-900 dark:text-white rounded-lg shadow-xl overflow-visible" @submit.prevent="submitForm">
		<div class="px-10 py-12">
			<FlashMessage v-if="$page.props.flash.location === 'page'" class="text-sm mt-6" />
			<div v-if="form.errors.global" class="text-red-500 text-sm mt-6">{{ form.errors.global }}</div>
			<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-4 md:space-y-0 mt-6">
				<MazInput
					v-model="form.name.it"
					:label="$t('Room Name (italian)')"
					:error="form.errors.name?.it ? true : false"
					:hint="form.errors.name?.it"
					type="text"
					required
					autoFocus
					@keydown.enter.prevent="enNameInput.focus()"
					block
				/>
				<MazInput
					v-model="form.name.en"
					ref="enNameInput"
					:label="$t('Room Name (english)')"
					:error="form.errors.name?.en ? true : false"
					:hint="form.errors.name?.en"
					type="text"
					required
					@keydown.enter.prevent="descriptionInput.focus()"
					block
				/>
			</div>
			<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-4 md:space-y-0 mt-6">
				<MazTextarea
					v-model="form.description.it"
					ref="descriptionInput"
					:label="$t('Description (italian)')"
					:error="form.errors.description?.it ? true : false"
					:hint="form.errors.description?.it"
					:placeholder="$t('Write a short description of the room.')"
					required
					@keydown.enter.prevent="enDescriptionInput.focus()"
					class="w-full"
				/>
				<MazTextarea
					v-model="form.description.en"
					ref="enDescriptionInput"
					:label="$t('Description (english)')"
					:error="form.errors.description?.en ? true : false"
					:hint="form.errors.description?.en"
					:placeholder="$t('Write a short description of the room.')"
					required
					@keydown.enter.prevent="quantityInput.focus()"
					class="w-full"
				/>
			</div>
			<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-4 md:space-y-0 mt-6">
				<MazInputNumber
					v-model="form.quantity"
					ref="quantityInput"
					:label="$t('Quantity')"
					:placeholder="$t('Insert a number')"
					:error="form.errors.quantity ? true : false"
					:hint="form.errors.quantity"
					:min="1"
					required
					@keydown.enter.prevent="checkinInput.focus()"
				/>
				<MazPicker
					v-model="form.checkin_time"
					ref="checkinInput"
					format="HH:mm"
					:error="form.errors.checkin_time ? true : false"
					:hint="form.errors.checkin_time"
					:label="$t('Check-in time')"
					only-time
					required
					@keydown.enter.prevent="checkoutInput.focus()"
				/>
				<MazPicker
					v-model="form.checkout_time"
					ref="checkoutInput"
					format="HH:mm"
					:error="form.errors.checkout_time ? true : false"
					:hint="form.errors.checkout_time"
					:label="$t('Check-out time')"
					only-time
					required
				/>
			</div>
			<hr class="mt-6 border-gray-300 dark:border-gray-700" />
			<div v-for="(option, index) in form.buy_options">
				<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-4 md:space-y-0 mt-6">
					<MazInput
						v-model="option.it"
						:label="$t('Option Name (italian)')"
						type="text"
						required
					/>
					<MazInput
						v-model="option.en"
						:label="$t('Option Name (english)')"
						type="text"
						required
					/>
					<MazInputNumber
						v-model="option.people"
						:label="$t('People')"
						:placeholder="$t('Insert a number')"
						:error="form.errors.buy_options ? true : false"
						:hint="form.errors.buy_options"
						:min="0"
						required
					/>
				</div>
				<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-4 md:space-y-0 mt-4">
					<MazInputPrice
						v-model="option.price"
						:label="$t('Price')"
						currency="EUR"
						:locale="getActiveLanguage()"
						min="0"
						required
					/>
					<MazSelect
						v-model="option.included_meals"
						:options="mealOptions"
						:label="$t('Included Meals')"
						multiple
						option-value-key="value"
						option-label-key="label"
						option-input-value-key="label"
					/>
					<MazBtn size="sm" leftIcon="storage/icons/trash" color="danger" @click="form.buy_options.splice(index, 1)">{{ $t('Remove') }}</MazBtn>
				</div>
				<hr class="mt-6 border-gray-200 dark:border-gray-800" />
			</div>
			<MazBtn size="sm" leftIcon="storage/icons/plus" color="secondary" @click="addBuyOption" class="mx-auto mt-6">{{ $t('Add Buy Option') }}</MazBtn>
			<div class="flex justify-center mt-6 mx-auto">
				<MazBtn type="submit" size="lg" color="primary" class="mx-2" :disabled="form.processing">{{ $t('Save') }}</MazBtn>
				<MazBtn type="button" size="lg" color="transparent" class="mx-2" @click="cancelForm">{{ $t('Cancel') }}</MazBtn>
			</div>
		</div>
	</form>
</template>
