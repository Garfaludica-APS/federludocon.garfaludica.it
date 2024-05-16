<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import BookingLayout from '@/Layouts/BookingLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Summary' }, () => h(BookingLayout, { allowReset: false, addPpContainer: true }, () => page)),
}
</script>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { getActiveLanguage, trans } from 'laravel-vue-i18n';
import { currency } from 'maz-ui';
import { router, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { toast } from 'vue3-toastify';

const page = usePage();

const props = defineProps({
	booking: Object,
	hotels: Array,
	sandbox: {
		type: Boolean,
		default: false,
	},
	pp_client_id: String,
});

const locale = computed(() => getActiveLanguage());

const totalCart = computed(() => {
	return totalCartBeforeAdminDiscount.value - parseFloat(props.booking.discount);
});

const totalCartBeforeAdminDiscount = computed(() => {
	const rooms = props.booking.rooms.reduce((total, room) => total + parseFloat(room.price), 0);
	const meals = props.booking.meals.reduce((total, meal) => total + parseFloat(meal.price), 0);
	return rooms + meals - totalDiscountBeforeAdminDiscount.value;
});

const emptyCart = computed(() => props.booking.rooms.length === 0 && props.booking.meals.length === 0);

const totalDiscountBeforeAdminDiscount = computed(() => {
	return props.booking.meals.reduce((total, meal) => total + parseFloat(meal.discount), 0);
});

const totalDiscount = computed(() => {
	return totalDiscountBeforeAdminDiscount.value + parseFloat(props.booking.discount);
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

function showError(message) {
	const t = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
	toast(message, {
		autoClose: 5000,
		theme: t,
		position: 'top-center',
		type: 'error',
	});
}

function loadPaypalButtons() {
	window.paypal.Buttons({
		style: {
			shape: 'rect',
			layout: 'vertical',
			color: 'blue',
			label: 'checkout',
		},
		async createOrder() {
			page.props.sessionExpireSeconds = 60*60*2;
			try {
				const response = await axios.post(route('booking.createOrder', props.booking));
				if (response.data.success) {
					return response.data.orderId;
				} else {
					showError(response.data.error);
					return null;
				}
			} catch (error) {
				showError('An error occured. Try again.');
			}
		},
		async onApprove(data, actions) {
			page.props.sessionExpireSeconds = 60*30;
			try {
				const response = await axios.post(route('booking.captureOrder', { orderId: data.orderID, booking: props.booking }));
				if (response.data.success)
					return actions.redirect(route('booking.success', props.booking));
				if (response.data.recoverable) {
					if (response.data.continueUrl)
						return actions.redirect(response.data.continueUrl);
					return actions.restart();
				}
				showError(response.data.error);
			} catch (error) {
				showError('An error occured. Try again.');
			}
		},
		onCancel() {
			router.get(route('booking.abort', props.booking));
		},
	}).render('#paypal-button-container');
}

const loadingNext = ref(false);
const loadingPrev = ref(false);

function confirmOrder() {
	loadingPrev.value = true;
	loadingNext.value = true;
	router.post(route('booking.confirm', props.booking), {
		preserveScroll: true,
		preserveState: false,
		onFinish: () => {
			loadingPrev.value = false;
			loadingNext.value = false;
		}
	});
}

const mounted = ref(false);
onMounted(() => {
	mounted.value = true;
	if (totalCart.value <= 0)
		return
	if (typeof window.paypal === 'undefined') {
		const script = document.createElement('script');
		if (props.sandbox)
			script.src = 'https://www.paypal.com/sdk/js?client-id=' + props.pp_client_id + '&currency=EUR&buyer-country=US&components=buttons';
		else
			script.src = 'https://www.paypal.com/sdk/js?client-id=' + props.pp_client_id + '&currency=EUR&components=buttons';
		document.head.appendChild(script);
		script.onload = loadPaypalButtons;
		script.setAttribute('data-sdk-integration-source', 'developer-studio');
		return;
	}
	loadPaypalButtons();
});
</script>

<template>
	<MazCard block>
		<template #title>
			<h1 class="!text-4xl">{{ $t('Review your Order') }}</h1>
		</template>
		<template #subtitle>
			{{ $t('Step :step of :total', { step: 4, total: 4 }) }}
		</template>
		<template #content>
			<p class="mt-3">{{ $t('Please, review your order. If you need to modify your order, press the "Back" button.') }}</p>
			<p class="mt-2">{{ $t('If everything is correct, you can proceed to payment by pressing the "PayPal Checkout" button or the "Debit or Credit Card" button. Please note that not all cards are supported via the "Debit or Credit Card" button. If your card is unsupported, you can press the "PayPal Checkout" button and then the "Pay with a card" button in the next page.') }}</p>
			<p class="mt-2 text-sm text-green-700">{{ $t('Garfaludica APS does not retain any fees on your order and does not earn anything from organizing this event. All the collected money will be forwarded to the participating hotels in the form of a clearance transfer operation.') }}</p>
			<p class="mt-2 text-sm">{{ $t('By paying with your card, you acknowledge that your data will be processed by PayPal subject to the PayPal Privacy Statement available at PayPal.com.') }}</p>
			<p class="mt-2 text-xl">{{ $t('See you at the GobCon!') }}</p>
			<hr class="border-b border-gray-500 my-4" />
			<h2 class="text-2xl">{{ $t('Billing Information') }}</h2>
			<p class="mt-3">{{ $t('First Name') + ': ' + booking.billing_info.first_name }}</p>
			<p>{{ $t('Last Name') + ': ' + booking.billing_info.last_name }}</p>
			<p>{{ $t('Tax ID') + ': ' + booking.billing_info.tax_id }}</p>
			<p>{{ $t(booking.billing_info.address_line_2 ? 'Address Line 1' : 'Address') + ': ' + booking.billing_info.address_line_1 }}</p>
			<p v-if="booking.billing_info.address_line_2">{{ $t('Address Line 2') + ': ' + booking.billing_info.address_line_2 }}</p>
			<p>{{ $t('City') + ': ' + booking.billing_info.city }}</p>
			<p>{{ $t('State') + ': ' + booking.billing_info.state }}</p>
			<p>{{ $t('Postal Code') + ': ' + booking.billing_info.postal_code }}</p>
			<p>{{ $t('Country') + ': ' + booking.billing_info.country_code.toUpperCase() }}</p>
			<p>{{ $t('Email') + ': ' + booking.billing_info.email }}</p>
			<p v-if="booking.billing_info.phone">{{ $t('Phone') + ': ' + booking.billing_info.phone }}</p>
			<hr class="border-b border-gray-500 my-4" />
			<h2 class="text-2xl">{{ $t('Rooms') }}</h2>
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
			<hr class="border-b border-gray-500 my-4" />
			<h2 class="text-2xl">{{ $t('Meals') }}</h2>
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
			<div v-if="booking.notes">
				<hr class="border-b border-gray-500 my-4" />
				<h2 class="text-2xl">{{ $t('Notes') }}</h2>
				<pre class="mt-2">{{ booking.notes }}</pre>
			</div>
			<hr class="border-b border-gray-500 my-4" />
			<p v-if="parseFloat(booking.discount) > 0" class="mt-3">{{ $t('Additional discount') + ': ' + formatPrice(parseFloat(booking.discount)) }}</p>
			<h2 class="text-2xl">{{ $t('Total: :total', { total: formatPrice(totalCart) }) }}</h2>
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
	<Teleport v-if="mounted" to="#cart-subtitle">
		{{ 'GARFALUDICA-' + booking.short_id.toString().padStart(4, '0') }}
	</Teleport>
	<Teleport v-if="mounted" to="#step-actions">
		<div class="flex-1 grow">
			<MazBtn block leftIcon="storage/icons/backward" color="danger" :href="route('booking.billing', booking)" :loading="loadingPrev" @click="loadingPrev = true" roundedSize="md">{{ $t('Back') + ' (' + $t('Billing') + ')' }}</MazBtn>
			<p v-if="props.sandbox" class="text-2xl text-red-600 font-extrabold text-center mt-2">SANDBOX ENABLED!</p>
			<MazBtn block leftIcon="storage/icons/check" color="primary" class="mt-2" roundedSize="md" v-if="totalCart <= 0 && page.props.auth.admin > 0" :loading="loadingNext" @click="confirmOrder">{{ $t('Confirm Order') }}</MazBtn>
		</div>
	</Teleport>
</template>
