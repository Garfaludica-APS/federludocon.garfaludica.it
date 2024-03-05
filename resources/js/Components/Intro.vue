<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({
	tdgLogo: String,
});
</script>

<template>
	<div class="px-6 py-2 lg:p-8 bg-transparent dark:from-gray-700/50 dark:via-transparent text-center">
		<img src="/storage/images/tdg-logo.png" alt="Logo Tana dei Goblin" class="inline-block max-h-[20vh] h-14 2xs:h-20 xs:h-24 w-auto mx-auto" />

		<h1 class="mt-6 text-2xl 2xs:text-3xl xs:text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl uppercase font-black font-sans leading-tight whitespace-nowrap text-white py-4 sm:py-6">
			<span class="text-green-600">GobCon</span> 2024<br>
			Garfagnana
		</h1>

		<div class="mt-2 text-gray-500 dark:text-gray-400 leading-relaxed">
			<button class="border p-2 mx-auto">Prenota Ora!</button>
			<button class="border p-2 mx-auto">Entra nel gruppo Telegram</button>
		</div>
	</div>
</template>
