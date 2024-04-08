<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
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
		<h1 class="text-5xl sm:text-3xl lg:text-8xl xl:text-10xl bg-gradient-to-r to-[#0b8a26] from-[#9bc009] via-[#fbdb19] inline-block text-transparent bg-clip-text">
			{{$t('Contact Us')}}
		</h1>
	</div>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div>
					<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
						<ApplicationLogo class="block h-16 w-auto" />
						<div class="mt-6 text-slate-200 text-lg dark:text-slate-300">
							<p v-html="$t('Garfa_email_intro')"></p>
							<p class="mt-3" v-html="$t('Garfa_management_intro')"></p>
							<p class="mt-3 text-lg" v-html="$t('org_contact_text')"></p>
							<div>
								<a href="https://t.me/associazionegarfaludica" type="button" class="inline-flex items-center justify-center h-8 px-6 py-6 border-2 rounded-full font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 bg-gray-900/20 text-white text-sm border-indigo-500 hover:border-white hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 mx-4 my-4" target="_blank">
									<p class="text-xl"> {{ $t('Telegram Group') }}</p>
								</a>
							</div>
						</div>
					</div>

					<div>
						<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
							<div class="flex items-center">
								<h1 class="text-4xl sm:text-3xl lg:text-5xl font-medium bg-gradient-to-r to-[#0b8a26] from-[#9bc009] via-[#fbdb19] inline-block text-transparent bg-clip-text">
									{{ $t('Structures') }}
								</h1>
							</div>
							<p class="mt-6 text-slate-200 text-lg dark:text-slate-300">
								{{ $t('Structures_info') }}
							</p>
							<div>
								<p class="mt-6 text-slate-200 text-2xl font-bold italic text-[#e42d33]">
									{{ $t('hotel_name_Panoramic Hotel') }}
								</p>
								<p class="mt-1 ml-3 text-slate-200 text-lg dark:text-slate-300" v-html="$t('Panoramic_Hotel_Contact')"></p>
							</div>
							<div>
								<p class="mt-6 text-slate-200 text-2xl font-bold italic text-[#415fb3]">
									{{ $t('hotel_name_Isera Refuge') }}
								</p>
								<p class="mt-1 ml-3 text-slate-200 text-lg dark:text-slate-300" v-html="$t('Isera_Refuge_Contact')"></p>
							</div>
							<div>
								<p class="mt-6 text-slate-200 text-2xl font-bold italic text-[#488130]">
									{{ $t('hotel_name_Braccicorti Farmhouse') }}
								</p>
								<p class="mt-1 ml-3 text-slate-200 text-lg dark:text-slate-300" v-html="$t('Braccicorti_Farmhouse_Contact')"></p>
							</div>
						</div>
						<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
							<div class="flex items-center">
								<p class="text-xl italic bg-gradient-to-r to-[#0b8a26] from-[#9bc009] via-[#fbdb19] inline-block text-transparent bg-clip-text">
									{{ $t('Problem_Title') }}
								</p>
							</div>
							<p class="mt-3 text-slate-200 text-lg dark:text-slate-300" v-html="$t('Problem_body_Site')"></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
