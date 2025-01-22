<script>
import { ref } from 'vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

import locandina from '@/../../storage/images/snesl.jpg';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Stelle Nere &amp; Strane Lune' }, () => h(PubLayout, () => page)),
}
</script>

<script setup>
const open = ref(false);
</script>

<template>
	<div class="py-12">
		<div class="mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="px-6 py-2 lg:p-8 bg-transparent dark:from-gray-700/50 dark:via-transparent text-center">
				<h1 class="mt-6 text-2xl 2xs:text-3xl xs:text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl uppercase font-black leading-tight text-red-300 py-4 sm:py-6">{{ $t('Stelle Nere &amp; Strane Lune') }}</h1>
				<p class="text-red-300 font-bold text-xl xs:text-2xl sm:text-3xl italic">{{ $t('Un viaggio onirico alla Rocca Ariostesca') }}</p>
				<p class="mt-4 text-white font-bold text-md xs:text-lg sm:text-xl" v-html="$t('Sabato 8 febbraio 2025, ore 21:00<br>Rocca Ariostesca (Piazzetta Ariosto, 1A), Castelnuovo di Garfagnana (LU)')"></p>
			</div>
			<div class="mt-8 mx-auto px-8 text-white flex flex-wrap justify-center items-center leading-tight max-w-7xl">
				<div class="sm:flex-[0_0_40%] w-full my-2 sm:max-w-[40%] px-6">
					<img :src="locandina" alt="Locandina Stelle Nere e Strane Lune" class="w-full rounded-lg shadow-lg">
				</div>
				<div class="sm:flex-[0_0_60%] w-full my-2 sm:max-w-[60%] px-6">
					<p class="text-lg">{{ $t('Avete mai sognato di varcare i confini della realtà e di immergervi in un mondo fantastico? Se la risposta è sì, allora l\'evento "Stelle Nere e Strane Lune" è quello che fa per voi. Si tratta di un "Gioco dei Ruoli dal Vivo", o in inglese LARP, in cui i partecipanti interpretano personaggi immaginari in un ambiente immersivo. Questo particolare evento, ispirato al ciclo di opere del Re in Giallo scritto da Robert W. Chambers, vi trasporterà in una dimensione onirica, la Fortezza, un luogo raggiungibile solo attraverso i sogni, che sarà teatro di storie intriganti e misteri da svelare.') }}</p>
					<p class="text-lg">{{ $t('L\'appuntamento è alla Rocca Ariostesca, dalle 21:00 alla mezzanotte. Non preoccupatevi se non avete mai partecipato ad un LARP prima d\'ora: l\'evento è gratuito ed aperto a tutti, dai 14 anni in su, e lo staff sarà a disposizione per guidarvi passo dopo passo. I posti sono limitati, quindi affrettatevi a prenotare!') }}</p>
					<p class="mt-2 text-lg">{{ $t('Ma di cosa si tratta esattamente?') }}</p>
					<p class="text-lg">{{ $t('Immaginate di essere un "Sognatore", una creatura in grado di accedere a questa Fortezza attraverso i propri sogni. Ogni Sognatore è definito dalla sua "Storia", un breve testo che ne delinea il passato, il suo carattere e le sue aspirazioni. All\'interno della Fortezza, incontrerete ovviamente altri Sognatori, ognuno con storie e obiettivi diversi.') }}</p>
					<p class="text-lg">{{ $t('Lo scopo del gioco è quello di interagire con gli altri Sognatori, scambiare storie e perseguire i propri obiettivi. La "moneta di scambio" in questo mondo onirico sono proprio le Storie: potrete ascoltarne quante ne vorrete, ma potrete custodirne e raccontarne solo una manciata. Attenzione però: alcune storie, le "Grandi Storie", sono particolarmente potenti e racchiudono segreti in grado di cambiare il destino della Fortezza e dei suoi abitanti.') }}</p>
					<p class="mt-2 text-lg">{{ $t('Non sono previsti combattimenti o interazioni fisiche simulate. Il gioco si basa principalmente sul dialogo e l\'interpretazione del proprio personaggio. Se lo desiderate, potrete anche indossare un costume a scelta, ma non è obbligatorio.') }}</p>
					<p class="text-lg">{{ $t('Per partecipare a "Stelle Nere e Strane Lune" basta prenotarsi seguendo la procedura che inizia premendo il pulsante in fondo a questa pagina e poi presentarsi alla Rocca Ariostesca il giorno dell\'evento. Lo staff vi fornirà tutte le informazioni necessarie per iniziare a giocare. Non perdete l\'occasione di vivere un\'esperienza unica e immersiva, in un mondo fantastisco dove le storie prendono vita!') }}</p>
				</div>
			</div>
			<div v-if="open" class="mt-8 mx-auto text-center">
				<p class="my-2 text-blue-500 font-bold text-xl">{{ $t('L\'evento è gratuito ma è necessario prenotare. Prenota ora!') }}</p>
				<TransparentButton :href="route('book')" class="text-2xl border-green-500 hover:bg-green-500 active:bg-green-700 focus:ring-green-500 mx-auto my-4 animate-pulse hover:animate-none text-white">{{ $t('Prenota Ora!') }}</TransparentButton>
			</div>
			<div v-else class="mt-8 mx-auto text-center">
				<p class="my-2 text-green-500 font-bold text-xl">{{ $t('L\'evento è gratuito ma è necessario prenotare.') }}</p>
				<p class="my-2 text-red-500 font-bold text-xl">{{ $t('Le prenotazioni sono al momento chiuse e apriranno nelle prossime ore, sempre su questa pagina!') }}</p>
			</div>
		</div>
	</div>
	</template>
