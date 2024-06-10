<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import TransparentButton from '@/Components/TransparentButton.vue';
import Countdown from '@/Components/Countdown.vue';

import tdglogo from '@/../../storage/images/tdg-logo.png';

const page = usePage();

const props = defineProps({
	tdgLogo: String,
});
</script>

<template>
	<div class="px-6 py-2 lg:p-8 bg-transparent dark:from-gray-700/50 dark:via-transparent text-center">
		<img :src="tdglogo" alt="Logo Tana dei Goblin" class="inline-block px-16 w-full max-w-3xl h-auto max-h-[14%] mx-auto" />

		<h1 class="mt-6 text-3xl 2xs:text-4xl xs:text-5xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl uppercase font-black leading-tight whitespace-nowrap text-white py-4 sm:py-6" v-html="$t('home_event_title')"></h1>
		<p class="text-white font-bold text-lg xs:text-xl sm:text-2xl" v-html="$t('home_event_subtitle')"></p>

		<div v-if="page.props.settings.portalOpen" class="animate-fade-in">
			<div class="mt-4 text-gray-500 dark:text-gray-400 leading-relaxed flex justify-center items-center flex-wrap -mx-4">
				<TransparentButton :href="route(page.props.rp + 'book')" class="text-sm border-green-500 hover:bg-green-500 active:bg-green-700 focus:ring-green-500 mx-4 my-4 animate-pulse hover:animate-none text-white">{{ $t('Booking Portal') }}</TransparentButton>
				<TransparentButton href="https://t.me/gobcongarfagnana" class="text-sm border-indigo-500 hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 mx-4 my-4 text-white" newtab>{{ $t('Telegram Group') }}</TransparentButton>
			</div>

			<!-- <p class="mt-8 text-gray-100 text-lg">{{ $t('Looking for friends to play with at the GobCon?') }} <Link :href="lroute('tables')" class="inline-block underline text-orange-600 hover:text-orange-500 active:text-orange-500 font-bold">{{ $t('Set up a table!') }}</Link></p> -->
		</div>
		<div v-else-if="!page.props.settings.portalClose">
			<p class="mt-8 text-gray-100 text-base xs:text-lg sm:text-xl font-semibold">{{ $t('The booking portal is closed right now. It will open in:') }}</p>
			<Countdown :startSeconds="page.props.settings.portalTimer" class="pt-12 text-orange-600" @countdown-end="page.props.settings.portalOpen = true" />
			<p class="mt-10 text-gray-100 text-sm xs:text-base sm:text-lg">
				{{ $t('While waiting, you can join our Telegram group to stay updated!') }} <a href="https://t.me/gobcongarfagnana" class="underline text-indigo-600 hover:text-indigo-500 active:text-indigo-500 font-bold" target="_blank">{{ $t('Join the group!') }}</a>
			</p>
		</div>
		<div v-else class="animate-fade-in">
			<p class="mt-8 text-gray-100 text-base xs:text-lg sm:text-2xl lg:text-4xl font-semibold text-green-600">{{ $t('The event is over. Thank you all for participating!') }}</p>
			<p class="mt-10 text-gray-100 text-sm xs:text-base sm:text-lg">
				{{ $t('You may want to join our Telegram group to stay updated with the activities of our association!') }} <a href="https://t.me/associazionegarfaludica" class="underline text-indigo-600 hover:text-indigo-500 active:text-indigo-500 font-bold" target="_blank">{{ $t('Join the group!') }}</a>
			</p>
		</div>

		<div class="mt-20 flex flex-wrap justify-center leading-tight max-w-7xl mx-auto px-8 text-white text-left">
			<div class="sm:flex-[0_0_50%] w-full my-2 sm:max-w-[50%] px-6">
				<h2 class="uppercase font-black text-2xl">{{ $t('About the Event') }}</h2>
				<p class="mt-2" v-html="$t('home_about_event_text')"></p>
				<Link :href="route(page.props.rp + 'about')" class="text-rose-500 font-semibold underline hover:text-rose-400">{{ $t('More informations...') }}</Link>
			</div>
			<div class="sm:flex-[0_0_25%] w-full my-2 sm:max-w-[25%] px-6">
				<h2 class="uppercase font-black text-2xl">{{ $t('Where') }}</h2>
				<p class="mt-2" v-html="$t('home_where_text')"></p>
				<Link :href="route(page.props.rp + 'venue')" class="text-rose-500 font-semibold underline hover:text-rose-400">{{ $t('Find out how to reach us!') }}</Link>
			</div>
			<div class="sm:flex-[0_0_25%] w-full my-2 sm:max-w-[25%] px-6">
				<h2 class="uppercase font-black text-2xl">{{ $t('When') }}</h2>
				<p class="mt-2" v-html="$t('home_when_text')"></p>
			</div>
		</div>
	</div>
</template>
