<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import BookingLayout from '@/Layouts/BookingLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Billing' }, () => h(BookingLayout, { allowReset: true }, () => page)),
}
</script>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { getActiveLanguage, trans } from 'laravel-vue-i18n';
import { currency } from 'maz-ui';
import { router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';

const props = defineProps({
	booking: Object,
	hotels: Array,
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
	country_code: 'IT',
	phone: '',
});

const locale = computed(() => getActiveLanguage());

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

function nextStep() {
	form.post(route('booking.billing.store', props.booking), {
		preserveScroll: true,
		preserveState: true,
	});
}

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
			<h1 class="!text-4xl">{{ $t('Billing Information') }}</h1>
		</template>
		<template #subtitle>
			{{ $t('Step :step of :total', { step: 3, total: 4 }) }}
		</template>
		<template #content>
			<p class="mt-3">{{ $t('Please, provide your billing information.') }}</p>
			<p class="mt-2">{{ $t('In case of issues with the booking, try to reset your order by pressing the "Reset" button near the Order Summary and restart from the beginning. If the problem persist, please contact: info@garfaludica.it (or use the Telegram group: t.me/gobcongarfagnana).') }}</p>
			<p class="mt-2 text-sm text-orange-700">{{ $t('NOTE: event organizers who are also administrators of Garfaludica APS MUST NOT place any order via this portal at the moment. Instructions for how they must book for the event will be provided in the coming weeks.') }}</p>
			<p class="mt-2 text-sm text-green-700">{{ $t('Garfaludica APS does not retain any fees on your order and does not earn anything from organizing this event. All the collected money will be forwarded to the participating hotels in the form of a clearance transfer operation.') }}</p>
			<p class="mt-2 text-xl">{{ $t('See you at the GobCon!') }}</p>
			<hr class="border-b border-gray-500 my-4" />
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
					<MazInput
						v-model="form.country_code"
						:label="$t('Country')"
						:error="form.errors.country_code ? true : false"
						:hint="form.errors.country_code"
						type="text"
						required
						ref="countryInput"
						@keydown.enter.prevent="taxIdInput.focus()"
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
			</div>
		</template>
		<template #footer>
			<div class="flex space-x-2">
				<div class="flex-1 grow">
					<MazBtn block size="xl" leftIcon="storage/icons/backward" color="danger" :href="route('booking.meals', booking)" class="h-full">{{ $t('Back') + ' (' + $t('Meals Booking') + ')' }}</MazBtn>
				</div>
				<div class="flex-1 grow">
					<MazBtn block size="xl" color="primary" rightIcon="storage/icons/forward" @click="nextStep" :loading="form.processing" :disabled="!validForm" class="h-full">{{ $t('Next') + ' (' + $t('Summary') + ')'}}</MazBtn>
				</div>
			</div>
		</template>
	</MazCard>
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
			<MazBtn block leftIcon="storage/icons/backward" color="danger" :href="route('booking.meals', booking)" class="h-full">{{ $t('Back') + ' (' + $t('Meals Booking') + ')' }}</MazBtn>
		</div>
		<div class="flex-1 grow">
			<MazBtn block color="primary" rightIcon="storage/icons/forward" @click="nextStep" :loading="form.processing" :disabled="!validForm" class="h-full">{{ $t('Next') + ' (' + $t('Summary') + ')'}}</MazBtn>
		</div>
	</Teleport>
</template>
