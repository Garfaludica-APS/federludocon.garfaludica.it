<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import BookingLayout from '@/Layouts/BookingLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Manage Order' }, () => h(BookingLayout, { allowReset: false, disableExpire: true }, () => page)),
}
</script>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { getActiveLanguage, trans } from 'laravel-vue-i18n';
import { currency } from 'maz-ui';
import { router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { toast } from 'vue3-toastify';

const props = defineProps({
	booking: Object,
	hotels: Array,
	refundable: Boolean,
	countries: Array,
});

const form = useForm({
	first_name: '',
	last_name: '',
	tax_id: '',
	address_line_1: '',
	address_line_2: '',
	city: '',
	state: '',
	postal_code: '',
	country_code: 'it',
	phone: '',
});

const locale = computed(() => getActiveLanguage());

const countriesOptions = computed(() => {
	return props.countries.map(country => {
		return { value: country.code_2, label: country.name };
	});
});

const totalCart = computed(() => {
	const rooms = props.booking.rooms.reduce((total, room) => total + parseFloat(room.price), 0);
	const meals = props.booking.meals.reduce((total, meal) => total + parseFloat(meal.price), 0);
	return rooms + meals - totalDiscount.value;
});

const emptyCart = computed(() => props.booking.rooms.length === 0 && props.booking.meals.length === 0);

const totalDiscount = computed(() => {
	return props.booking.meals.reduce((total, meal) => total + parseFloat(meal.discount), 0);
});

function formatPrice(value) {
	return currency(value, locale.value, { currency: 'EUR' });
}

function getCartDesc(reservation, noPeriod = false) {
	var desc = '';
	desc += reservation.room.name[locale.value];
	if (reservation.buy_option[locale.value] !== 'default')
		desc += ' (' + reservation.buy_option[locale.value] + ')';
	const hotel = props.hotels.find(hotel => hotel.id === reservation.room.hotel_id);
	if (hotel)
		desc += ' - ' + trans('hotel_name_' + hotel.name);
	if (!noPeriod)
		desc += ' [' + dayjs(reservation.checkin).format('D/M') + ' - ' + dayjs(reservation.checkout).format('D/M') + ']';
	return desc;
}

function getCartDescMeal(reservation) {
	var desc = '';
	desc += trans(reservation.meal.type);
	if (reservation.meal.menu !== 'standard')
		desc += ' (' + trans(reservation.meal.menu) + ')';
	if (reservation.meal.type === 'breakfast') {
		const hotel = props.hotels.find(hotel => hotel.id === reservation.meal.hotel_id);
		if (hotel)
			desc += ' @ ' + trans('hotel_name_' + hotel.name);
	}
	desc += ' [' + dayjs(reservation.date).format('D/M') + ']';
	return desc;
}

const reservedRooms = computed(() => {
	return props.booking.rooms.map(reservation => {
		return {
			id: reservation.id,
			period: dayjs(reservation.checkin).format('D/M') + ' - ' + dayjs(reservation.checkout).format('D/M'),
			hotel: trans('hotel_name_' + props.hotels.find(hotel => hotel.id === reservation.room.hotel_id).name),
			room: reservation.room.name[locale.value],
			people: reservation.people,
			price: formatPrice(reservation.price),
		};
	});
});

const reservedMeals = computed(() => {
	return props.booking.meals.map(reservation => {
		return {
			id: reservation.id,
			date: dayjs(reservation.date).format('D/M'),
			quantity: reservation.quantity,
			type: trans(reservation.meal.type),
			menu: trans(reservation.meal.menu),
			hotel: trans('hotel_name_' + props.hotels.find(hotel => hotel.id === reservation.meal.hotel_id).name),
			price: formatPrice(reservation.price),
			discount: formatPrice(-reservation.discount),
			total: formatPrice(reservation.price - reservation.discount),
		};
	});
});

const lastNameInput = ref(null);
const addressLine1Input = ref(null);
const addressLine2Input = ref(null);
const postalCodeInput = ref(null);
const cityInput = ref(null);
const stateInput = ref(null);
const countryInput = ref(null);
const taxIdInput = ref(null);
const phoneInput = ref(null);

const validForm = computed(() => {
	return form.first_name && form.last_name && form.tax_id && form.address_line_1 && form.city && form.state && form.postal_code;
});

const phoneCountryCode = 'IT';

function saveBilling() {
	form.patch(route('booking.update-billing', props.booking), {
		preserveScroll: true,
		preserveState: true,
	});
}

const notesForm = useForm({
	notes: '',
});

function saveNotes() {
	notesForm.patch(route('booking.add-notes', props.booking), {
		preserveScroll: true,
		preserveState: false,
	}, {
		onFinish: () => {
			notesForm.reset();
		},
	});
}

const showDialog = ref(false);
const cancelling = ref(false);

function startCancelOrder() {
	showDialog.value = true;
}

function cancelOrder() {
	cancelling.value = true;
	router.post(route('booking.refund', props.booking), {
		preserveState: false,
		preserveScroll: false,
		onError: () => {
			cancelling.value = false;
		},
	});
}

const mounted = ref(false);
onMounted(() => {
	mounted.value = true;
	if (props.booking.billing_info) {
		form.first_name = props.booking.billing_info.first_name;
		form.last_name = props.booking.billing_info.last_name;
		form.tax_id = props.booking.billing_info.tax_id;
		form.address_line_1 = props.booking.billing_info.address_line_1;
		form.address_line_2 = props.booking.billing_info.address_line_2;
		form.city = props.booking.billing_info.city;
		form.state = props.booking.billing_info.state;
		form.postal_code = props.booking.billing_info.postal_code;
		form.country_code = props.booking.billing_info.country_code;
		form.phone = props.booking.billing_info.phone;
	}
	form.defaults();
});
</script>

<template>
	<MazCard block>
		<template #title>
			<h1 class="!text-4xl">{{ $t('Manage Order') }}</h1>
		</template>
		<template #subtitle>
			{{ $t('Order: :order', { order: 'GARFALUDICA-' + booking.short_id.toString().padStart(4, "0") }) }}<br />
			{{ $t('Status: :status', { status: $t(booking.state) }) }}
		</template>
		<template #content>
			<p class="mt-3">{{ $t('You can review and edit your order here.') }}</p>
			<p v-if="refundable" class="mt-2">{{ $t('You can add notes to your booking, change your billing informations and cancel the booking and ask for a refund.') }}</p>
			<p v-else class="mt-2">{{ $t('You can add notes to your booking and change your billing informations. Unfortunately, it\'s too late now to ask for a refund: your order can not be cancelled anymore.') }}</p>
			<p v-if="refundable" class="mt-2">{{ $t('If you just want to change the menu (Standard, Vegetarian, Vegan) for some meals, just describe the change you want in the notes (since this does not alter the total cost of your booking). If you want to change things that alter the total cost of your booking (such as adding/removing rooms/meals/people or changing check-in and check-out dates) you must cancel the order and place a new one.') }}</p>
			<p v-else class="mt-2">{{ $t('If you want to change the menu (Standard, Vegetarian, Vegan) for some meals, just describe the change you want in the notes. Other changes are not possible anymore.') }}</p>
			<p v-if="refundable" class="mt-2">{{ $t('If you cancel the order, all the money you paid will be refunded to you within 2-3 working days. In the meantime, you can place a new order.') }}</p>
			<hr class="border-b border-gray-500 my-4" />
			<h2 class="text-2xl font-bold">{{ $t('Billing Information') }}</h2>
			<div class="mt-4">
				<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-6 md:space-y-0 mt-6">
					<MazInput
						v-model="form.first_name"
						:label="$t('First Name')"
						:error="form.errors.first_name ? true : false"
						:hint="form.errors.first_name"
						type="text"
						required
						autoFocus
						@keydown.enter.prevent="lastNameInput.focus()"
						block
					/>
					<MazInput
						v-model="form.last_name"
						:label="$t('Last Name')"
						:error="form.errors.last_name ? true : false"
						:hint="form.errors.last_name"
						type="text"
						required
						ref="lastNameInput"
						@keydown.enter.prevent="addressLine1Input.focus()"
						block
					/>
				</div>
				<MazInput
					v-model="form.address_line_1"
					:label="$t('Address Line 1')"
					:error="form.errors.address_line_1 ? true : false"
					:hint="form.errors.address_line_1"
					type="text"
					required
					ref="addressLine1Input"
					@keydown.enter.prevent="addressLine2Input.focus()"
					block
					class="mt-6"
				/>
				<MazInput
					v-model="form.address_line_2"
					:label="$t('Address Line 2')"
					:error="form.errors.address_line_2 ? true : false"
					:hint="form.errors.address_line_2"
					type="text"
					ref="addressLine2Input"
					@keydown.enter.prevent="postalCodeInput.focus()"
					block
					class="mt-6"
				/>
				<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-6 md:space-y-0 mt-6">
					<MazInput
						v-model="form.postal_code"
						:label="$t('Postal Code')"
						:error="form.errors.postal_code ? true : false"
						:hint="form.errors.postal_code"
						type="text"
						required
						ref="postalCodeInput"
						@keydown.enter.prevent="cityInput.focus()"
						block
					/>
					<MazInput
						v-model="form.city"
						:label="$t('City')"
						:error="form.errors.city ? true : false"
						:hint="form.errors.city"
						type="text"
						required
						ref="cityInput"
						@keydown.enter.prevent="stateInput.focus()"
						block
					/>
					<MazInput
						v-model="form.state"
						:label="$t('State')"
						:error="form.errors.state ? true : false"
						:hint="form.errors.state"
						type="text"
						required
						ref="stateInput"
						@keydown.enter.prevent="countryInput.focus()"
						block
					/>
				</div>
				<div class="flex flex-col md:flex-row justify-evenly md:space-x-4 space-y-6 md:space-y-0 mt-6 mb-4">
					<MazSelect
						v-model="form.country_code"
						:options="countriesOptions"
						:label="$t('Country')"
						:error="form.errors.country_code ? true : false"
						:hint="form.errors.country_code"
						required
						ref="countryInput"
						block
					/>
					<MazInput
						v-model="form.tax_id"
						:label="$t('Tax ID')"
						:error="form.errors.tax_id ? true : false"
						:hint="form.errors.tax_id"
						type="text"
						required
						ref="taxIdInput"
						@keydown.enter.prevent="phoneInput.focus()"
						block
					/>
					<MazPhoneNumberInput
						v-model="form.phone"
						v-model:country-code="phoneCountryCode"
						show-code-on-list
						:preferred-countries="['IT']"
						:label="$t('Phone')"
						:error="form.errors.phone ? true : false"
						:hint="form.errors.phone"
						ref="phoneInput"
						block
					/>
				</div>
				<p class="mt-6 text-red-600" v-if="form.errors.global">{{ form.errors.global }}</p>
				<div class="flex flex-col justify-end items-end">
					<MazBtn size="lg" color="secondary" leftIcon="storage/icons/clipboard-document-check" @click="saveBilling" class="grow-0 mt-2" :disabled="!form.isDirty" :processing="form.processing">{{ $t('Save') }}</MazBtn>
				</div>
			</div>
			<hr class="border-b border-gray-500 my-4" />
			<h2 class="text-2xl font-bold">{{ $t('Rooms') }}</h2>
				<MazTable size="sm" color="secondary" background-even
					:headers="[
						{ label: $t('Period'), key: 'period' },
						{ label: $t('Hotel'), key: 'hotel' },
						{ label: $t('Room'), key: 'room' },
						{ label: $t('People'), key: 'people' },
						{ label: $t('Price'), key: 'price' },
					]"
					:rows="reservedRooms"
					sortable
				>
					<template #cell-room="{ value }">
						<span class="whitespace-normal">{{ value }}</span>
					</template>
				</MazTable>
				<p v-if="refundable && booking.rooms.length > 0" class="mt-2">{{ $t('Rooms can not be edited. If you want to edit rooms, you must cancel the order and place a new one.') }}</p>
			<hr class="border-b border-gray-500 my-4" />
			<h2 class="text-2xl font-bold">{{ $t('Meals') }}</h2>
				<MazTable size="sm" color="secondary" background-even
					:headers="[
						{ label: $t('Date'), key: 'date' },
						{ label: $t('Qty'), key: 'quantity' },
						{ label: $t('Meal'), key: 'type' },
						{ label: $t('Menu'), key: 'menu' },
						{ label: $t('Hotel'), key: 'hotel' },
						{ label: $t('Price'), key: 'price' },
						{ label: $t('Discount'), key: 'discount' },
						{ label: $t('Total'), key: 'total' },
					]"
					:rows="reservedMeals"
					sortable
				/>
				<p v-if="refundable && booking.meals.length > 0" class="mt-2">{{ $t('Meals can not be edited. If you want to edit meals, you must cancel the order and place a new one. If you just want to change the menu of a meal, write your change in the "Notes" section below.') }}</p>
			<hr class="border-b border-gray-500 my-4" />
			<h2 class="text-4xl font-extrabold">{{ $t('Total: :total', { total: formatPrice(totalCart) }) }}</h2>
			<hr class="border-b border-gray-500 my-4" />
			<h2 class="text-2xl font-bold">{{ $t('Notes') }}</h2>
			<pre v-if="booking.notes" class="mt-2">{{ booking.notes }}</pre>
			<p v-else class="mt-2 italic">{{ $t('No notes added.') }}</p>
			<h3 class="mt-3 font-bold text-lg">{{ $t('Add notes') }}</h3>
			<div class="flex flex-col justify-end items-end">
				<MazTextarea
					v-model="notesForm.notes"
					:label="$t('Add notes')"
					:placeholder="$t('Add your notes here.')"
					class="w-full mt-4"
					:error="notesForm.errors.notes ? true : false"
					:hint="notesForm.errors.notes"
					required
				/>
				<MazBtn size="lg" color="secondary" leftIcon="storage/icons/document-plus" @click="saveNotes" class="grow-0 mt-3" :processing="notesForm.processing" :disabled="!notesForm.isDirty">{{ $t('Add notes') }}</MazBtn>
			</div>
		</template>
		<template #footer>
			<div class="flex flex-col justify-start items-start">
				<h2 class="text-2xl font-bold">{{ $t('Cancel Order') }}</h2>
				<p class="mt-2">{{ $t('Cancel your order and ask for a refund.') }}</p>
				<MazBtn block class="mt-3" color="danger" leftIcon="storage/icons/trash" @click="startCancelOrder" :loading="cancelling">{{ $t('Cancel Order') }}</MazBtn>
			</div>
		</template>
	</MazCard>
	<MazDialog v-model="showDialog" :title="$t('Cancel Order')">
		<p>{{ $t('Are you sure you want to cancel the order and ask for a full refund? NOTE: THIS OPERATION IS IRREVERSIBLE!') }}</p>
		<template #footer="{ close }">
			<div class="flex justify-between items-center w-full">
				<MazBtn @click="close" color="secondary">{{ $t('Keep Order') }}</MazBtn>
				<MazBtn @click="close(); cancelOrder()" color="danger">{{ $t('Cancel Order') }}</MazBtn>
			</div>
		</template>
	</MazDialog>
	<Teleport v-if="mounted && !emptyCart" to="#cart-details">
		<li v-for="reservation in booking.rooms" class="flex justify-between border-b border-slate-200 dark:border-slate-700">
			<span v-if="reservation.buy_option.people === 0" class="pl-4 indent-[-1rem]">{{ $t(':quantityx :desc', { quantity: reservation.people, desc: getCartDesc(reservation) }) }}</span>
			<span v-else class="pl-4 indent-[-1rem]">{{ getCartDesc(reservation) }}</span>
			<span class="ml-4">{{ formatPrice(reservation.price) }}</span>
		</li>
		<li v-for="reservation in booking.meals" class="flex justify-between border-b border-slate-200 dark:border-slate-700">
			<span>{{ $t(':quantityx :desc', { quantity: reservation.quantity, desc: getCartDescMeal(reservation) }) }}</span>
			<span v-if="reservation.discount > 0" class="ml-4" :title="$t('Discount: :discount', { discount: formatPrice(-reservation.discount) })">{{ '*' + formatPrice(reservation.price) }}</span>
			<span v-else class="ml-4">{{ formatPrice(reservation.price) }}</span>
		</li>
		<li v-if="totalDiscount > 0" class="flex justify-between border-b border-slate-200 dark:border-slate-700">
			<span class="italic">{{ '*' + $t('Discount') }}</span>
			<span class="ml-4 italic">{{ formatPrice(-totalDiscount) }}</span>
		</li>
	</Teleport>
	<Teleport v-else-if="mounted" to="#cart-details">
		<li class="italic">{{ $t('No items added.') }}</li>
	</Teleport>
	<Teleport v-if="mounted" to="#cart-total">
		{{ formatPrice(totalCart) }}
	</Teleport>
	<Teleport v-if="mounted" to="#step-actions">
		<div class="flex-1 grow">
			<MazBtn block leftIcon="storage/icons/trash" color="danger" @click="startCancelOrder" :loading="cancelling">{{ $t('Cancel Order') }}</MazBtn>
		</div>
	</Teleport>
</template>
