<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Contact Us' }, () => h(PubLayout, () => page)),
}
</script>

<script setup>
const emailAddress = 'info@garfaludica.it';
const emailAddressHref = 'mailto:' + emailAddress;
</script>

<template>
	<div class="flex justify-center items-center">
		<h1 class="text-5xl sm:text-3xl lg:text-8xl xl:text-10xl text-white inline-block uppercase">
			{{$t('Contact Us')}}
		</h1>
	</div>

	<div class=" py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div>
					<div class="p-6 lg:p-8 bg-slate-100 dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
						<ApplicationLogo class="block h-16 w-auto" />
						<div class="mt-4 text-black dark:text-slate-200 text-lg">
							<p v-html="$t('Garfa_email_exp')"></p>
							<p class="text inline" v-html="$t('Garfa_email_intro')"></p>
							<a class="font-bold hover:bg-gradient-to-r to-[#0b8a26] from-[#9bc009] via-[#fbdb19] hover:inline-block hover:text-transparent hover:bg-clip-text" rel="noopener" href="mailto:info@garfaludica.it" target="_blank">info@garfaludica.it</a>
							<br><br>
							<div class="font-bold text inline">{{ $t('Presidency') }}</div>: <a class="hover:text-indigo-500" rel="noopener" href="tel:+393247460610" target="_blank">324-746-0610</a> (<a class="hover:underline hover:decoration-indigo-600" rel="external noopener" href="https://t.me/Pillian" target="_blank">Christian Pilli</a>)<br>
							<div class="font-bold text inline">{{ $t('Secretariate') }}</div>: <a class="hover:text-indigo-500" rel="noopener" href="tel:+393314432124" target="_blank">331-443-2124</a> (<a class="hover:underline hover:decoration-indigo-600" rel="external noopener" href="https://t.me/SpeedJack" target="_blank">Niccolò Scatena</a>)<br>
							<div class="font-bold text inline">{{ $t('Vice Presidency') }}</div>: <a class="hover:text-indigo-500" rel="noopener" href="tel:+393282228615" target="_blank">328-222-8615</a> (<a class="hover:underline hover:decoration-indigo-600" rel="external noopener" href="https://t.me/Badpinger" target="_blank">Simone Bertagni</a>)<br>
							<div class="font-bold text inline">{{ $t('Counselors') }}</div>: <a class="hover:underline hover:decoration-indigo-600" rel="external noopener" href="https://t.me/CannibalSmit" target="_blank">Riccardo Evangelisti</a>, <a class="hover:underline hover:decoration-indigo-600" rel="external noopener" href="https://t.me/Picarus" target="_blank">Pierluigi Pellizzer</a><br>
							<div>
								<a href="https://t.me/federludocongarfagnana" type="button" class="inline-flex items-center justify-center h-8 px-6 py-6 border-2 rounded-full font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 text-black dark:text-white hover:text-white text-sm hover:border-gray-600 border-indigo-500 hover:border-white hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 mx-4 my-4" target="_blank">
									<p class="text-xl"> {{ $t('Telegram Group') }}</p>
								</a>
							</div>
							<p class="mt-4">Per contattare Federludo, fare riferimento alla loro <a class="text-indigo-500" href="https://www.federludo.it/contatti/" rel="noopener external nofollow">pagina contatti</a></p>
						</div>
					</div>

					<div>
						<div class="p-6 lg:p-8 bg-slate-100 dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-600 dark:border-gray-700">
							<div class="flex items-center">
								<p class="text-xl italic dark:text-white inline-block">
									{{ $t('Problem_Title') }}
								</p>
							</div>
							<div class="mt-3 text-black dark:text-slate-200 text-lg">
									<p class="text inline" v-html="$t('Problem_body_Site')"> </p>
									<a class="font-bold hover:bg-gradient-to-r to-[#0b8a26] from-[#9bc009] via-[#fbdb19] hover:inline-block hover:text-transparent hover:bg-clip-text" rel="noopener" href="mailto:info@garfaludica.it" target="_blank">info@garfaludica.it</a><br>
									<p class="text inline" v-html="$t('Problem_body_Site_2')"></p>
									<a class="hover:underline hover:decoration-indigo-600" rel="external noopener" href="https://t.me/SpeedJack" target="_blank">Niccolò Scatena</a> | <a class="hover:underline hover:decoration-indigo-600" rel="external noopener" href="https://t.me/MarcoPaccia" target="_blank">Marco Pacciani</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
