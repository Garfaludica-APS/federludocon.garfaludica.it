<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import BookingLayout from '@/Layouts/BookingLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Book meals' }, () => h(BookingLayout, { allowReset: true }, () => page)),
}
</script>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { getActiveLanguage, trans } from 'laravel-vue-i18n';
import { currency } from 'maz-ui';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';

const props = defineProps({
	booking: Object,
	hotels: Array,
	freeMeals: Object,
	dates: Array,
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

function mealReservations(date, type) {
	return props.booking.meals.filter(reservation => dayjs(reservation.date).format('YYYY-MM-DD') === dayjs(date).format('YYYY-MM-DD') && reservation.meal.type === type);
}

function freeMealsForMeal(date, type) {
	return props.freeMeals[date][type];
}

function groupedMeals(type) {
	const grouped = [];
	props.hotels.forEach(hotel => {
		hotel.meals.forEach(meal => {
			if (meal.type === type)
				grouped.push(meal);
		});
	});
	return grouped;
}

const notes = ref('');

function nextStep() {
	router.post(route('booking.notes.store', props.booking), { notes: notes.value });
}

const mounted = ref(false);
onMounted(() => {
	mounted.value = true;
	if (props.booking.notes)
		notes.value = props.booking.notes;
});
</script>

<template>
	<MazCard block>
		<template #title>
			<h1 class="!text-4xl">{{ $t('Meals Booking') }}</h1>
		</template>
		<template #subtitle>
			{{ $t('Step :step of :total', { step: 2, total: 4 }) }}
		</template>
		<template #content>
			<p class="mt-3">{{ $t('Select the meals you want to book for the event. All lunches are served by the Isera Refuge. All dinners are served by the Panoramic Hotel. The breakfasts are served by the hotels where you will stay.') }}</p>
			<p class="mt-2">{{ $t('Additional breakfasts (in addition to those included with the rooms) are not reservable. All hotels have an internal bar where you can take any breakfast not included with your rooms paying in loco.') }}</p>
			<p class="mt-2">{{ $t('For lunches and dinners, you can choose between 3 menus: standard, vegetarian, vegan.') }}</p>
			<p class="mt-2">{{ $t('Based on the rooms you have booked, the system has automatically added some meals to your order. Feel free to change the booked meals as you wish. Discounts will be applied if eligible (in case the meal is included with your room reservation).') }}</p>
			<p class="mt-2">{{ $t('Event begins on the afternoon of Friday 14 June, but you can book meals starting from lunch on Friday if you wish.') }}</p>
			<p class="mt-2">{{ $t('There are no discounts for minors.') }}</p>
			<p class="mt-2 font-semibold">{{ $t('IMPORTANT: Please, if you have any food allergies or intolerances, specify them in the "Notes" at the end of this page.') }}</p>
			<p class="mt-2">{{ $t('In case of issues with the booking, try to reset your order by pressing the "Reset" button near the Order Summary and restart from the beginning. If the problem persist, please contact: info@garfaludica.it (or use the Telegram group: t.me/gobcongarfagnana).') }}</p>
			<p class="mt-2 text-sm text-orange-700">{{ $t('NOTE: event organizers who are also administrators of Garfaludica APS MUST NOT place any order via this portal at the moment. Instructions for how they must book for the event will be provided in the coming weeks.') }}</p>
			<p class="mt-2 text-sm text-green-700">{{ $t('Garfaludica APS does not retain any fees on your order and does not earn anything from organizing this event. All the collected money will be forwarded to the participating hotels in the form of a clearance transfer operation.') }}</p>
			<p class="mt-2 text-xl">{{ $t('See you at the GobCon!') }}</p>
			<template v-for="date in dates" :key="date">
				<h2 class="my-4 text-3xl flex items-center after:flex-1 after:border-b after:ml-3 capitalize">{{ freeMeals[date]['DISPLAY'] }}</h2>
				<template v-for="type in ['lunch', 'dinner']" :key="type">
					<MealBookForm :date="dayjs(date).format('YYYY-MM-DD')" :booking="booking" :mealType="type" :meals="groupedMeals(type)" :reservations="mealReservations(date, type)" :freeMeals="freeMealsForMeal(date, type)" />
				</template>
			</template>
			<h2 class="my-4 text-3xl flex items-center after:flex-1 after:border-b after:ml-3">{{ $t('Notes') }}</h2>
			<p class="mt-3">{{ $t('Please, specify here any food allergies or intolerances you have (or your friends have).') }}</p>
			<p class="mt-2">{{ $t('Try to make us understand when an allergic/intolerant person has a meal and which menu he/she has chosen.') }}</p>
			<p class="mt-2 font-bold">{{ $t('Example:') }}</p>
			<p class="ml-4 text-sm"><code>
				{{ $t('Luca: celiac; has lunch and dinner both friday and saturday (menu: vegetarian).') }}<br>
				{{ $t('Sara: lactose intolerant; has a dinner saturday (menu: standard) and a lunch sunday (menu: vegan).') }}
			</code></p>
			<p class="mt-4">{{ $t('You can also use this field for any other request regarding your order.') }}</p>
			<MazTextarea
				v-model="notes"
				:label="$t('Additional notes')"
				:placeholder="$t('Specify any food allergies or intolerances here.')"
				class="w-full mt-4"
				:error="notes.length > 4096"
				:hint="notes.length > 4096 ? $t('Max 4096 characters.') : ''"
			/>
			<div v-if="notes.length > 4096" class="mt-2 text-red-700">{{ $t('Text is too long.') }}</div>
		</template>
		<template #footer>
			<div class="flex space-x-2">
				<div class="flex-1 grow">
					<MazBtn block size="xl" leftIcon="storage/icons/backward" color="danger" :href="route('booking.rooms', booking)" class="h-full">{{ $t('Back') + ' (' + $t('Rooms Booking') + ')' }}</MazBtn>
				</div>
				<div class="flex-1 grow">
					<MazBtn block size="xl" color="primary" rightIcon="storage/icons/forward" @click="nextStep" class="h-full" :disabled="notes.length > 4096">{{ $t('Next') + ' (' + $t('Billing') + ')'}}</MazBtn>
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
			<MazBtn block leftIcon="storage/icons/backward" color="danger" :href="route('booking.rooms', booking)" class="h-full">{{ $t('Back') + ' (' + $t('Rooms Booking') + ')' }}</MazBtn>
		</div>
		<div class="flex-1 grow">
			<MazBtn block color="primary" rightIcon="storage/icons/forward" @click="nextStep" class="h-full" :disabled="notes.length > 4096">{{ $t('Next') + ' (' + $t('Billing') + ')'}}</MazBtn>
		</div>
	</Teleport>
</template>
