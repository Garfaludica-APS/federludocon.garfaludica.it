<script setup>
import { ref, onBeforeMount, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();

const props = defineProps({
	allowReset: {
		type: Boolean,
		default: false
	},
});

const showDialog = ref(false);
const showResetDialog = ref(false);
const refreshing = ref(false);

var interval;
function decrement() {
	if (refreshing.value && page.props.sessionExpireSeconds > 60)
		refreshing.value = false;
	if (refreshing.value)
		return;
	if (page.props.sessionExpireSeconds <= 1) {
		terminateSession();
		return;
	}
	if (page.props.sessionExpireSeconds <= 60)
		showDialog.value = true;
	else
		showDialog.value = false;
	page.props.sessionExpireSeconds--;
}

function terminateSession() {
	clearInterval(interval);
	showDialog.value = false;
	const booking = route().params.booking;
	if (!booking)
		router.visit(route(page.props.rp + 'home'));
	else
		router.post(route('booking.terminate', booking));
}

function refreshSession() {
	refreshing.value = true;
	router.reload();
}

function resetOrder() {
	showResetDialog.value = false;
	router.delete(route('booking.reset', route().params.booking));
}

onBeforeMount(() => {
	interval = setInterval(decrement, 1000);
});

onUnmounted(() => {
	clearInterval(interval);
});
</script>

<template>
	<div class="flex flex-col min-h-screen min-w-full bg-slate-100 dark:bg-slate-900 text-black dark:text-white">
		<DarkModeSwitcher class="fixed top-0 right-0 mt-4 mr-4 h-8 w-8 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 dark:hover:text-gray-200 dark:focus:text-gray-200 transition duration-150 ease-in-out" />
		<header class="grow-0 shrink basis-auto mt-4 mb-2 max-w-screen-2xl mx-auto p-2">
			<ApplicationLogo class="h-20 mx-auto w-max max-w-screen" />
			<p class="text-center text-xs">{{ $t('Third Sector Organization (RUNTS rep. n. 113019)') }}</p>
			<p class="text-center mt-3 text-xl font-semibold">{{ $t('GobCon 2024 Garfagnana - Booking Portal') }}</p>
		</header>
		<main class="py-8 px-2 grow shrink basis-auto max-w-screen-2xl mx-auto flex flex-col lg:flex-row space-y-4 lg:space-y-0">
			<section class="basis-8/12 px-4 !w-[97vw] xs:!w-full">
				<slot />
			</section>
			<section class="basis-4/12 px-4 overflow-scroll max-h-[90vh] lg:self-start lg:sticky lg:top-4">
				<MazCard block>
					<template #title>
						<div class="flex justify-between items-center">
							<h2 class="text-xl font-semibold inline-block">{{ $t('Order Summary') }}</h2>
							<MazLink v-if="props.allowReset" target="_self" href="#" @click="showResetDialog = true" color="primary" class="font-bold text-base">{{ $t('Reset') }}</MazLink>
						</div>
					</template>
					<template #subtitle>
						<hr class="my-2" />
					</template>
					<template #content>
						<ul id="cart-details">
						</ul>
					</template>
					<template #footer>
						<div class="flex flex-wrap justify-between text-xl font-bold">
							<span>{{ $t('Total') }}</span>
							<span id="cart-total"></span>
						</div>
						<p class="text-xs mt-2 text-slate-500 dark:text-slate-400">{{ $t('VAT included (10%).') }}</p>
						<p class="text-xs mt-1 text-slate-500 dark:text-slate-400">{{ $t('Local tourist tax excluded: 1€/person per night.') }}</p>
						<p class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Tourist tax must be paid in place to the hotels.') }}</p>
					</template>
				</MazCard>
				<div id="step-actions" class="mt-4 flex space-x-2">
				</div>
			</section>
		</main>
		<footer class="grow-0 shrink basis-[156px] bg-gray-300 dark:bg-slate-900 text-gray-800 dark:text-gray-100 shadow-md mt-12">
			<Copyright />
		</footer>
	</div>
	<MazDialog v-model="showDialog" :title="$t('Session Expiring')" noClose persistent>
		<p>{{ $t('Your session is expiring due to inactivity. If the session expires, you will need to restart the booking process from the beginning.') }}</p>
		<p>{{ $t('Do you need more time?') }}</p>
		<template #footer="{ close }">
			<div class="flex justify-between items-center w-full">
				<MazBtn @click="close(); terminateSession()" color="danger">{{ $t('Terminate Session (:remaining)', { remaining: page.props.sessionExpireSeconds - 1 }) }}</MazBtn>
				<MazBtn @click="close(); refreshSession()" color="primary">{{ $t('I need more time!') }}</MazBtn>
			</div>
		</template>
	</MazDialog>
	<MazDialog v-model="showResetDialog" :title="$t('Reset Order')">
		<p>{{ $t('Are you sure you want to remove all the rooms and meals from your order?') }}</p>
		<template #footer="{ close }">
			<div class="flex justify-between items-center w-full">
				<MazBtn @click="close" color="transparent">{{ $t('No, keep order') }}</MazBtn>
				<MazBtn @click="close(); resetOrder()" color="danger">{{ $t('Yes, reset order!') }}</MazBtn>
			</div>
		</template>
	</MazDialog>
</template>
