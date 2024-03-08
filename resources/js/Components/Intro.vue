<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import TransparentButton from '@/Components/TransparentButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
	tdgLogo: String,
	curLang: String,
});
</script>

<template>
	<div class="px-6 py-2 lg:p-8 bg-transparent dark:from-gray-700/50 dark:via-transparent text-center">
		<img src="/storage/images/tdg-logo.png" alt="Logo Tana dei Goblin" class="inline-block max-h-[20vh] h-14 2xs:h-20 xs:h-24 w-auto mx-auto" />

		<h1 class="mt-6 text-2xl 2xs:text-3xl xs:text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl uppercase font-black font-sans leading-tight whitespace-nowrap text-white py-4 sm:py-6">
			<span class="text-green-600">GobCon</span> 2024<br>
			Garfagnana
		</h1>
		<p class="text-white font-bold text-lg">XX-YY June, Corfino Villa Collemandina, Lucca</p>

		<div class="mt-4 text-gray-500 dark:text-gray-400 leading-relaxed flex justify-center items-center flex-wrap -mx-4">
			<TransparentButton :href="lroute('book')" class="border-green-500 hover:bg-green-500 active:bg-green-700 focus:ring-green-500 mx-4 my-4 animate-pulse hover:animate-none">{{ $t('Prenota ora la tua stanza!') }}</TransparentButton>
			<TransparentButton href="https://t.me/associazionegarfaludica" class="border-indigo-500 hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 mx-4 my-4" newtab>Entra nel gruppo Telegram</TransparentButton>
		</div>

		<p class="mt-8 text-gray-100 text-md">Cerchi amici con cui giocare alla GobCon? <Link :href="lroute('tables')" class="inline-block underline text-orange-600 hover:text-orange-500 active:text-orange-500 font-bold">Organizza un tavolo!</Link></p>

		<div class="mt-20 flex flex-wrap justify-center leading-tight max-w-7xl mx-auto px-8 text-white text-left">
			<div class="sm:flex-[0_0_50%] w-full my-2 sm:max-w-[50%] px-6">
				<h2 class="uppercase font-black text-2xl">About The Event</h2>
				<p class="mt-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam Metrodorum non puto ipsum professum, sed, cum appellaretur ab Epicuro, repudiare tantum beneficium noluisse; Mene ergo et Triarium dignos existimas, apud quos turpiter loquare? Non modo carum sibi quemque</p>
			</div>
			<div class="sm:flex-[0_0_25%] w-full my-2 sm:max-w-[25%] px-6">
				<h2 class="uppercase font-black text-2xl">Where</h2>
				<p class="mt-2">Corfino, frazione di Villa Collemandina (Lucca)</p>
			</div>
			<div class="sm:flex-[0_0_25%] w-full my-2 sm:max-w-[25%] px-6">
				<h2 class="uppercase font-black text-2xl">When</h2>
				<p class="mt-2">Friday to Sunday<br>XX-YY June</p>
			</div>
		</div>
	</div>
</template>
