<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import BookingLayout from '@/Layouts/BookingLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Book rooms' }, () => h(BookingLayout, { allowReset: true }, () => page)),
}
</script>

<script setup>
import { computed, ref, onBeforeMount, onMounted } from 'vue';
import { getActiveLanguage, trans } from 'laravel-vue-i18n';
import { currency } from 'maz-ui';
import { router, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';

const page = usePage();

const props = defineProps({
	booking: Object,
	hotels: Array,
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

const selectedCheckin = ref({});
const selectedCheckout = ref({});
const availableCheckouts = ref({});
const peopleCount = ref({});
const maxPeople = ref({});
const roomLoading = ref({});
const roomDisabled = ref({});
const errors = ref({});

function checkinSelected(room, option) {
	const checkin = selectedCheckin.value[room.id][option['id']];
	availableCheckouts.value[room.id][option['id']] = [];
	selectedCheckout.value[room.id][option['id']] = null;
	roomDisabled.value[room.id][option['id']] = true;
	roomLoading.value[room.id][option['id']] = true;
	errors.value[room.id][option['id']] = null;
	maxPeople.value[room.id][option['id']] = 1;
	axios.post(route('booking.room.available-checkouts', { room: room, booking: props.booking }), { checkin: checkin }).then(response => {
		page.props.sessionExpireSeconds = response.data.sessionExpireSeconds;
		const checkouts = response.data.checkouts;
		if (checkouts.length === 0) {
			errors.value[room.id][option['id']] = trans('Room not available in the period selected.');
			roomDisabled.value[room.id][option['id']] = true;
			roomLoading.value[room.id][option['id']] = false;
			return;
		}
		errors.value[room.id][option['id']] = null;
		availableCheckouts.value[room.id][option['id']] = response.data.checkouts;
		roomLoading.value[room.id][option['id']] = false;
	}).catch(error => {
		errors.value[room.id][option['id']] = trans('An error occured while fetching available checkout dates. Please, try again.')
		roomDisabled.value[room.id][option['id']] = true;
		roomLoading.value[room.id][option['id']] = false;
	});
}

function checkoutSelected(room, option) {
	const checkin = selectedCheckin.value[room.id][option['id']];
	if (!checkin)
		return;
	errors.value[room.id][option['id']] = null;
	if (option['people'] > 0) {
		roomLoading.value[room.id][option['id']] = false;
		roomDisabled.value[room.id][option['id']] = false;
		return;
	}
	roomLoading.value[room.id][option['id']] = true;
	roomDisabled.value[room.id][option['id']] = true;
	maxPeople.value[room.id][option['id']] = 1;
	axios.post(route('booking.room.max-people', { room: room, booking: props.booking }), { checkin: checkin, checkout: selectedCheckout.value[room.id][option['id']] }).then(response => {
		page.props.sessionExpireSeconds = response.data.sessionExpireSeconds;
		if (response.data.maxPeople <= 0) {
			errors.value[room.id][option['id']] = 'There are no available slots for the selected period.';
			roomLoading.value[room.id][option['id']] = false;
			roomDisabled.value[room.id][option['id']] = true;
			return;
		}
		maxPeople.value[room.id][option['id']] = response.data.maxPeople;
		roomLoading.value[room.id][option['id']] = false;
		roomDisabled.value[room.id][option['id']] = false;
	}).catch(error => {
		errors.value[room.id][option['id']] = trans('An error occured while fetching slot availability. Please, try again.')
		roomDisabled.value[room.id][option['id']] = true;
		roomLoading.value[room.id][option['id']] = false;
	});
}

function addRoom(room, option) {
	roomLoading.value[room.id][option['id']] = true;
	if (option['people'] === 0) {
		router.put(route('booking.rooms.store', props.booking), {
			room: room.id,
			buy_option: option['id'],
			checkin: selectedCheckin.value[room.id][option['id']],
			checkout: selectedCheckout.value[room.id][option['id']],
			people: peopleCount.value[room.id][option['id']],
		}, {
			preserveState: false,
			preserveScroll: true,
			onSuccess: () => {
				selectedCheckin.value[room.id][option['id']] = null;
				selectedCheckout.value[room.id][option['id']] = null;
				peopleCount.value[room.id][option['id']] = 1;
			},
			onFinish: () => {
				roomLoading.value[room.id][option['id']] = false;
			},
		});
	} else {
		router.put(route('booking.rooms.store', props.booking), {
			room: room.id,
			buy_option: option['id'],
			checkin: selectedCheckin.value[room.id][option['id']],
			checkout: selectedCheckout.value[room.id][option['id']],
		}, {
			preserveState: false,
			preserveScroll: true,
			onSuccess: () => {
				selectedCheckin.value[room.id][option['id']] = null;
				selectedCheckout.value[room.id][option['id']] = null;
			},
			onFinish: () => {
				roomLoading.value[room.id][option['id']] = false;
			},
		});
	}
}

function isMultiBookable(option) {

	return option['people'] === 0;
}

function rangeSelected(room, option) {
	return selectedCheckin.value[room.id][option['id']] && selectedCheckout.value[room.id][option['id']];
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
			room: getCartDesc(reservation, true),
			period: dayjs(reservation.checkin).format('D/M') + ' - ' + dayjs(reservation.checkout).format('D/M'),
			people: reservation.people,
			price: formatPrice(reservation.price),
		};
	});
});

const removingRoom = ref(null);
const showRemoveDialog = ref(false);

function removeRoom(reservation) {
	showRemoveDialog.value = false;
	router.delete(route('booking.rooms.delete', { booking: props.booking, reservation: reservation.id }), {}, {
		preserveState: false,
		preserveScroll: true,
	});
}

const showDialog = ref(false);

function terminateSession() {
	showDialog.value = false;
	router.post(route('booking.terminate', props.booking));
}

onBeforeMount(() => {
	props.hotels.forEach(hotel => {
		hotel.rooms.forEach(room => {
			selectedCheckin.value[room.id] = {};
			selectedCheckout.value[room.id] = {};
			availableCheckouts.value[room.id] = {};
			roomLoading.value[room.id] = {};
			roomDisabled.value[room.id] = {};
			errors.value[room.id] = {};
			peopleCount.value[room.id] = {};
			maxPeople.value[room.id] = {};
			room.buy_options.forEach(option => {
				selectedCheckin.value[room.id][option['id']] = null;
				selectedCheckout.value[room.id][option['id']] = null;
				availableCheckouts.value[room.id][option['id']] = [];
				roomLoading.value[room.id][option['id']] = false;
				roomDisabled.value[room.id][option['id']] = true;
				errors.value[room.id][option['id']] = null;
				if (isMultiBookable(option)) {
					peopleCount.value[room.id][option['id']] = 1;
					maxPeople.value[room.id][option['id']] = 1;
				}
			});
		});
	});
});

const mounted = ref(false);
onMounted(() => {
	mounted.value = true;
});
</script>

<template>
	<MazCard block>
		<template #title>
			<h1 class="!text-4xl">{{ $t('Rooms Booking') }}</h1>
		</template>
		<template #subtitle>
			{{ $t('Step :step of :total', { step: 1, total: 4 }) }}
		</template>
		<template #content>
			<p class="mt-3">{{ $t('Select the rooms you want to book for the event. Please, note that double rooms are available in 2 options: select the appropriate option based on the number of people (for two people, select "Double room"; for one person, select "Double room (FOR A SINGLE PERSON)"). You can add multiple rooms to your booking.') }}</p>
			<p class="mt-2">{{ $t('Note that not all dates may be available for all rooms. If the date does not appear in Checkin/Checkout fields, it means that the room is not available for that period.') }}</p>
			<p class="mt-2">{{ $t('If you do not want to book any rooms but you want to book some meals (example: you attend to the event only for a single day and you do not want to stay overnight), just press on the "Next" button without adding any room. You will be able to book your meals in the next page.') }}</p>
			<p class="mt-2">{{ $t('There are no discounts for minors.') }}</p>
			<p class="mt-2">{{ $t('In case of issues with the booking, try to reset your order by pressing the "Reset" button near the Order Summary and restart from the beginning. If the problem persist, please contact: info@garfaludica.it (or use the Telegram group: t.me/gobcongarfagnana).') }}</p>
			<p class="mt-2 text-sm text-orange-700">{{ $t('NOTE: event organizers who are also administrators of Garfaludica APS MUST NOT place any order via this portal at the moment. Instructions for how they must book for the event will be provided in the coming weeks.') }}</p>
			<p class="mt-2 text-sm text-green-700">{{ $t('Garfaludica APS does not retain any fees on your order and does not earn anything from organizing this event. All the collected money will be forwarded to the participating hotels in the form of a clearance transfer operation.') }}</p>
			<p class="mt-2 text-xl">{{ $t('See you at the GobCon!') }}</p>
			<h2 class="my-4 text-3xl flex items-center after:flex-1 after:border-b after:ml-3">{{ $t('Selected Rooms') }}</h2>
				<MazTable size="sm" color="secondary" background-even
					:headers="[
						{ label: $t('Room'), key: 'room' },
						{ label: $t('Period'), key: 'period' },
						{ label: $t('People'), key: 'people' },
						{ label: $t('Price'), key: 'price' },
					]"
					:rows="reservedRooms"
				>
					<template #cell-room="{ value }">
						<span class="whitespace-normal">{{ value }}</span>
					</template>
					<template #actions="{ row }">
						<MazBtn color="danger" fab size="sm" icon="storage/icons/trash" @click="removingRoom = row; showRemoveDialog = true" />
					</template>
				</MazTable>
			<h2 class="my-4 text-3xl flex items-center after:flex-1 after:border-b after:ml-3">{{ $t('Available Rooms') }}</h2>
			<div v-for="hotel in hotels" :key="hotel.id">
				<h3 class="mt-6 mb-2 text-2xl">{{ $t('hotel_name_' + hotel.name) }}</h3>
				<template v-for="room in hotel.rooms" :key="room.id">
					<MazCard class="my-2" v-for="option in room.buy_options" :key="room.id + '_' + option['id']" block>
						<template #title>
							<div class="flex justify-between items-start flex-wrap">
								<h4 v-if="option[locale] === 'default'">{{ room.name[locale] }}</h4>
								<h4 v-else>{{ room.name[locale] + ' (' + option[locale] + ')' }}</h4>
								<div class="flex flex-col items-center justify-center">
									<p>{{ $t('Price: :price', { price: formatPrice(option['price']) }) }}</p>
									<p v-if=isMultiBookable(option) class="text-sm text-slate-500 dark:text-slate-400">{{ $t('per person, per night') }}</p>
									<p v-else class="text-sm text-slate-500 dark:text-slate-400">{{ $t('per night') }}</p>
								</div>
							</div>
						</template>
						<template #subtitle>
							<p> {{ $t('hotel_name_' + hotel.name) }} </p>
						</template>
						<template #content>
							<p class="text-slate-600 dark:text-slate-300">{{ room.description[locale] }}</p>
							<div class="flex flex-col xs:flex-row justify-between items-center xs:space-x-4">
								<MazSelect block class="mt-3" v-model="selectedCheckin[room.id][option['id']]" :label="$t('Check-in date')" :options="room.checkin_dates" @selected-option="checkinSelected(room, option)" :disabled="room.checkin_dates.length == 0" />
								<MazSelect block class="mt-3" v-model="selectedCheckout[room.id][option['id']]" :label="$t('Check-out date')" :options="availableCheckouts[room.id][option['id']]" @selected-option="checkoutSelected(room, option)" :disabled="availableCheckouts[room.id][option['id']].length == 0" />
							</div>
						</template>
						<template #footer>
							<div class="flex justify-between items-center flex-wrap">
								<div class="flex flex-col items-start">
									<p v-if="room.checkin_dates.length > 0 && !rangeSelected(room, option)" class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Select check-in and check-out dates to add the room.') }}</p>
									<p v-if="isMultiBookable(option) && rangeSelected(room, option) && !roomLoading[room.id][option['id']] && !roomDisabled[room.id][option['id']]" class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Max people for selected period: :avail', { avail: maxPeople[room.id][option['id']] }) }}</p>
									<p v-if="errors[room.id][option['id']]" class="text-sm text-red-500 dark:text-red-400">{{ errors[room.id][option['id']] }}</p>
								</div>
								<div class="flex flex-row items-center justify-start space-x-4 flex-wrap">
									<MazInputNumber class="mt-3 ml-4 max-w-64" v-if="isMultiBookable(option)" v-model="peopleCount[room.id][option['id']]" rightIcon="storage/icons/users" placeholder="1" :min="1" :max="maxPeople[room.id][option['id']]" :step="1" :label="$t('Number of people')" :disabled="roomDisabled[room.id][option['id']]" />
									<MazBtn class="mt-3" :leftIcon="room.checkin_dates.length > 0 ? 'storage/icons/plus' : 'storage/icons/no-symbol'" color="secondary" :loading="roomLoading[room.id][option['id']]" :disabled="roomDisabled[room.id][option['id']] || room.checkin_dates.length == 0" @click="addRoom(room, option)">{{ room.checkin_dates.length > 0 ? $t('Add Room') : $t('SOLD OUT!') }}</MazBtn>
								</div>
							</div>
						</template>
					</MazCard>
				</template>
			</div>
		</template>
		<template #footer>
			<div class="flex space-x-2">
				<div class="flex-1 grow">
					<MazBtn block size="xl" leftIcon="storage/icons/x-mark" color="danger" @click="showDialog = true" class="h-full">{{ $t('Cancel Booking') }}</MazBtn>
				</div>
				<div class="flex-1 grow">
					<MazBtn block size="xl" color="primary" rightIcon="storage/icons/forward" :href="route('booking.meals', booking)" class="h-full">{{ $t('Next') + ' (' + $t('Meals Booking') + ')'}}</MazBtn>
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
			<MazBtn block leftIcon="storage/icons/x-mark" color="danger" @click="showDialog = true" class="h-full">{{ $t('Cancel Booking') }}</MazBtn>
		</div>
		<div class="flex-1 grow">
			<MazBtn block color="primary" rightIcon="storage/icons/forward" :href="route('booking.meals', booking)" class="h-full">{{ $t('Next') + ' (' + $t('Meals Booking') + ')'}}</MazBtn>
		</div>
	</Teleport>
	<MazDialog v-model="showDialog" :title="$t('Cancel Booking')">
		<p>{{ $t('Do you want to cancel the booking process a go back to the home page?') }}</p>
		<template #footer="{ close }">
			<div class="flex justify-between items-center w-full">
				<MazBtn @click="close" color="secondary">{{ $t('No, stay here') }}</MazBtn>
				<MazBtn @click="close(); terminateSession()" color="danger">{{ $t('Yes, back to home page') }}</MazBtn>
			</div>
		</template>
	</MazDialog>
	<MazDialog v-model="showRemoveDialog" :title="$t('Remove Room')">
		<p>{{ $t('Do you want to remove this room from your booking?') }}</p>
		<p>{{ $t('Room: :room', { room: removingRoom.room }) }}</p>
		<template #footer="{ close }">
			<div class="flex justify-between items-center w-full">
				<MazBtn @click="close" color="secondary">{{ $t('No') }}</MazBtn>
				<MazBtn @click="close(); removeRoom(removingRoom)" color="danger">{{ $t('Yes') }}</MazBtn>
			</div>
		</template>
	</MazDialog>
</template>
