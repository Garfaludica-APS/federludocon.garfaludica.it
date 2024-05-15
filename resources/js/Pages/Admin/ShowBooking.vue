<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'View Booking' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('h1', { class: 'flex-1 font-extrabold text-3xl' }, trans('Booking')),
	})),
}
</script>

<script setup>
import { ref, computed } from 'vue';
import { getActiveLanguage, trans } from 'laravel-vue-i18n';
import { router } from '@inertiajs/vue3';
import { currency } from 'maz-ui';
import dayjs from 'dayjs';

const props = defineProps({
	booking: Object,
	hotels: Array,
	ppOrder: Object,
});

const locale = computed(() => getActiveLanguage());

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

const showDialog = ref(false);

function markAsRefunded() {
	showDialog.value = false;
	router.post(route('admin.bookings.mark-refunded', props.booking));
}

const receiptName = computed(() => 'receipt-GARFALUDICA-' + props.booking.short_id.toString().padStart(4, '0') + '.pdf');
</script>

<template>
	<section class="mb-6">
		<MazCard block>
			<template #title>
				<h2 class="!text-4xl">{{ 'GARFALUDICA-' + booking.short_id.toString().padStart(4, '0') }}</h2>
			</template>
			<template #subtitle>
				{{ booking.id }}<br />
				{{ $t('Status: :status', { status: $t(booking.state).toUpperCase() }) }}<br />
				{{ $t('By: :name (:email)', { name: booking.billing_info.last_name + ' ' + booking.billing_info.first_name, email: booking.email }) }}<br />
				{{ $t('Total: :total', { total: formatPrice(booking.total) }) }}<br />
				{{ $t('Last Updated: :date', { date: dayjs(booking.updated_at).format('D/M/YYYY HH:mm') }) }}
			</template>
			<template #content>
				<h3 class="text-2xl mt-4">{{ $t('Billing Information') }}</h3>
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
				<h3 class="text-2xl">{{ $t('Rooms') }}</h3>
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
				<h3 class="text-2xl">{{ $t('Meals') }}</h3>
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
					<h3 class="text-2xl">{{ $t('Notes') }}</h3>
					<pre class="mt-2">{{ booking.notes }}</pre>
				</div>
				<hr class="border-b border-gray-500 my-4" />
				<p v-if="parseFloat(booking.discount) > 0" class="mt-3">{{ $t('Additional discount') + ': ' + formatPrice(parseFloat(booking.discount)) }}</p>
				<h3 class="text-2xl">{{ $t('Total: :total', { total: formatPrice(booking.total) }) }}</h3>
				<hr class="border-b border-gray-500 my-4" />
				<h3 class="text-2xl">{{ $t('PayPal Order Details') }}</h3>
				<pre class="mt-2">{{ JSON.stringify(ppOrder, null, 2) }}</pre>
			</template>
			<template #footer>
				<div class="flex justify-between items-center my-2">
					<MazLink v-if="booking.state == 'completed' || booking.state == 'refund_requested' || booking.state == 'refunded'" class="text-xl" leftIcon="storage/icons/arrow-down-tray" color="primary" :href="route('admin.bookings.invoice', booking)" :download="receiptName">{{ $t('Download Receipt') }}</MazLink>
					<MazBtn v-if="booking.state == 'refund_requested'" leftIcon="storage/icons/check" color="secondary" @click="showDialog = true">{{ $t('Mark as Refunded') }}</MazBtn>
				</div>
			</template>
		</MazCard>
	</section>
	<MazDialog v-model="showDialog" :title="$t('Mark As Refunded?')">
		<p>{{ $t('Are you sure you want to mark this order as refunded?') }}</p>
		<template #footer="{ close }">
			<div class="flex justify-between items-center w-full">
				<MazBtn @click="close" color="danger">{{ $t('No') }}</MazBtn>
				<MazBtn @click="close(); markAsRefunded()" color="secondary">{{ $t('Yes') }}</MazBtn>
			</div>
		</template>
	</MazDialog>
</template>
