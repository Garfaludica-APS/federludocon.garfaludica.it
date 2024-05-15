<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Bookings' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('h1', { class: 'flex-1 font-extrabold text-3xl' }, trans('Bookings')),
	})),
}
</script>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { currency } from 'maz-ui';
import { getActiveLanguage } from 'laravel-vue-i18n';

const page = usePage();

const props = defineProps({
	bookings: Array,
});

const locale = computed(() => getActiveLanguage());

function formatPrice(value) {
	return currency(value, locale.value, { currency: 'EUR' });
}

const pageNum = ref(1);
const pageSize = ref(20);

const bookingsRows = computed(() => {
	return filteredRows.value.map(booking => ({
		uuid: booking.id,
		id: booking.short_id.toString().padStart(4, '0'),
		name: booking.billing_info.last_name + ' ' + booking.billing_info.first_name,
		email: booking.email,
		period: dayjs(booking.first_check_in).format('D/M') + ' - ' + dayjs(booking.last_check_out).format('D/M'),
		total: booking.total,
		status: booking.state,
	}));
});

const completedFilter = ref(true);
const cancelledFilter = ref(false);
const refundFilter = ref(false);
const refundedFilter = ref(false);
const failedFilter = ref(false);

const panoramicFilter = ref(true);
const iseraFilter = ref(true);
const braccicortiFilter = ref(true);

function changedFilter() {
	setTimeout(() => {
		localStorage.completedFilter = completedFilter.value;
		localStorage.cancelledFilter = cancelledFilter.value;
		localStorage.refundFilter = refundFilter.value;
		localStorage.refundedFilter = refundedFilter.value;
		localStorage.failedFilter = failedFilter.value;
		localStorage.panoramicFilter = panoramicFilter.value;
		localStorage.iseraFilter = iseraFilter.value;
		localStorage.braccicortiFilter = braccicortiFilter.value;
	}, 500);
}


const filteredRows = computed(() => {
	return props.bookings.filter(booking => {
		if (!completedFilter.value && booking.state == 'completed')
			return false;
		if (!cancelledFilter.value && booking.state == 'cancelled')
			return false;
		if (!refundFilter.value && booking.state == 'refund_requested')
			return false;
		if (!refundedFilter.value && booking.state == 'refunded')
			return false;
		if (!failedFilter.value && booking.state == 'failed')
			return false;

		var allowedHotels = [];
		if (panoramicFilter.value)
			allowedHotels.push('Panoramic Hotel');
		if (iseraFilter.value)
			allowedHotels.push('Isera Refuge');
		if (braccicortiFilter.value)
			allowedHotels.push('Braccicorti Farmhouse');
		var include = false;
		for (var i = 0; i < booking.hotels.length; i++) {
			if (allowedHotels.includes(booking.hotels[i].name)) {
				include = true;
				break;
			}
		}
		return include;
	});
});

function viewBooking(booking) {
	router.get(route('admin.bookings.show', booking.uuid));

}

onMounted(() => {
	if (localStorage.completedFilter === undefined) {
		localStorage.completedFilter = true;
		localStorage.cancelledFilter = false;
		localStorage.refundFilter = page.props.auth.admin.is_super_admin;
		localStorage.refundedFilter = false;
		localStorage.failedFilter = page.props.auth.admin.is_super_admin;

		localStorage.panoramicFilter = page.props.auth.admin.is_super_admin;
		localStorage.iseraFilter = page.props.auth.admin.is_super_admin;
		localStorage.braccicortiFilter = page.props.auth.admin.is_super_admin;

		var hotels = page.props.auth.admin.hotels;
		for (var i = 0; i < hotels.length; i++) {
			var h = hotels[i];
			if (h.name == 'Panoramic Hotel')
				localStorage.panoramicFilter = true;
			if (h.name == 'Isera Refuge')
				localStorage.iseraFilter = true;
			if (h.name == 'Braccicorti Farmhouse')
				localStorage.braccicortiFilter = true;
		}
	}
	completedFilter.value = localStorage.completedFilter === 'true';
	cancelledFilter.value = localStorage.cancelledFilter === 'true';
	refundFilter.value = localStorage.refundFilter === 'true';
	refundedFilter.value = localStorage.refundedFilter === 'true';
	failedFilter.value = localStorage.failedFilter === 'true';
	panoramicFilter.value = localStorage.panoramicFilter === 'true';
	iseraFilter.value = localStorage.iseraFilter === 'true';
	braccicortiFilter.value = localStorage.braccicortiFilter === 'true';
});
</script>

<template>
	<section class="mb-6">
		<h2 class="font-bold text-2xl mb-4">{{ $t('Filters') }}</h2>
		<h3 class="font-bold text-lg">{{ $t('Status') }}</h3>
		<div class="flex flex-row flex-wrap gap-x-8 pb-6">
			<div><MazSwitch @update:model-value="changedFilter" v-model="completedFilter" color="success" /><span class="ml-1">{{ $t('COMPLETED') }}</span></div>
			<div><MazSwitch @update:model-value="changedFilter" v-model="cancelledFilter" color="danger" /><span class="ml-1">{{ $t('CANCELLED') }}</span></div>
			<div><MazSwitch @update:model-value="changedFilter" v-model="refundFilter" color="warning" /><span class="ml-1">{{ $t('REFUND_REQUESTED') }}</span></div>
			<div><MazSwitch @update:model-value="changedFilter" v-model="refundedFilter" color="primary" /><span class="ml-1">{{ $t('REFUNDED') }}</span></div>
			<div><MazSwitch @update:model-value="changedFilter" v-model="failedFilter" color="danger" /><span class="ml-1">{{ $t('FAILED') }}</span></div>
		</div>
		<h3 class="font-bold text-lg">{{ $t('Hotel') }}</h3>
		<div class="flex flex-row flex-wrap gap-x-8 pb-6">
			<div><MazSwitch @update:model-value="changedFilter" v-model="panoramicFilter" color="primary" /><span class="ml-1">{{ $t('hotel_name_Panoramic Hotel') }}</span></div>
			<div><MazSwitch @update:model-value="changedFilter" v-model="iseraFilter" color="secondary" /><span class="ml-1">{{ $t('hotel_name_Isera Refuge') }}</span></div>
			<div><MazSwitch @update:model-value="changedFilter" v-model="braccicortiFilter" color="info" /><span class="ml-1">{{ $t('hotel_name_Braccicorti Farmhouse') }}</span></div>
		</div>
		<MazTable
			class="mt-4"
			size="md"
			:title="$t('Bookings')"
			color="secondary"
			search
			sortable
			hoverable
			background-even
			pagination
			v-model:page="pageNum"
			v-model:page-size="pageSize"
			:headers="[
				{ label: $t('Order #'), key: 'id', classes: 'font-bold' },
				{ label: $t('Name'), key: 'name' },
				{ label: $t('Email'), key: 'email' },
				{ label: $t('Period'), key: 'period', sortable: false },
				{ label: $t('Total'), key: 'total', classes: 'font-bold' },
				{ label: $t('Status'), key: 'status', classes: 'uppercase font-bold' },
			]"
			:rows="bookingsRows"
		>
			<template #cell-id="{ row, value }">
				<span :class="{
					'text-green-500': row.status == 'completed',
					'text-red-500': row.status == 'cancelled' || row.status == 'failed',
					'text-yellow-500': row.status == 'refund_requested',
					'text-indigo-500': row.status == 'refunded',
				}">{{ value }}</span>
			</template>
			<template #cell-name="{ row, value }">
				<span :class="{
					'text-green-500': row.status == 'completed',
					'text-red-500': row.status == 'cancelled' || row.status == 'failed',
					'text-yellow-500': row.status == 'refund_requested',
					'text-indigo-500': row.status == 'refunded',
				}">{{ value }}</span>
			</template>
			<template #cell-email="{ row, value }">
				<span :class="{
					'text-green-500': row.status == 'completed',
					'text-red-500': row.status == 'cancelled' || row.status == 'failed',
					'text-yellow-500': row.status == 'refund_requested',
					'text-indigo-500': row.status == 'refunded',
				}">{{ value }}</span>
			</template>
			<template #cell-period="{ row, value }">
				<span :class="{
					'text-green-500': row.status == 'completed',
					'text-red-500': row.status == 'cancelled' || row.status == 'failed',
					'text-yellow-500': row.status == 'refund_requested',
					'text-indigo-500': row.status == 'refunded',
				}">{{ value }}</span>
			</template>
			<template #cell-total="{ row, value }">
				<span :class="{
					'text-green-500': row.status == 'completed',
					'text-red-500': row.status == 'cancelled' || row.status == 'failed',
					'text-yellow-500': row.status == 'refund_requested',
					'text-indigo-500': row.status == 'refunded',
				}">{{ formatPrice(value) }}</span>
			</template>
			<template #cell-status="{ value }">
				<span class="border rounded-xl p-2" :class="{
					'text-green-500 border-green-500': value == 'completed',
					'text-red-500 border-red-500': value == 'cancelled' || value == 'failed',
					'text-yellow-500 border-yellow-500': value == 'refund_requested',
					'text-indigo-500 border-indigo-500': value == 'refunded',
				}">{{ $t(value.toUpperCase()) }}</span>
			</template>
			<template #actions="{ row }">
				<MazBtn fab size="md" icon="storage/icons/eye" @click="viewBooking(row)" />
			</template>
		</MazTable>
	</section>
</template>
