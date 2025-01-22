<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'About' }, () => h(PubLayout, () => page)),
}
</script>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

import logo from '@/../../storage/images/logo.png';
import tdglogo from '@/../../storage/images/tdg-transparent.png';

const page = usePage();

const icon_person = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline text-[#0b8a26]"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>';
const Panoramico_list_1 = computed(() => trans('Double room for one person: <b>:price</b>', {price :'80€'})+'<span class="ml-3">'+icon_person+'</span>');
const Panoramico_list_2 = computed(() => trans('Double room for two people: <b>:price</b>', {price :'60€'})+'<span class="ml-3">'+icon_person+icon_person+'</span>');
const Panoramico_list_3 = computed(() => trans('Triple room: <b>:price</b>', {price :'55€'})+'<span class="ml-3">'+icon_person+icon_person+icon_person+'</span>');
const Panoramico_list_4 = computed(() => trans('Quadruple room: <b>:price</b>', {price :'50€'})+'<span class="ml-3">'+icon_person+icon_person+icon_person+icon_person+'</span>');

const icon_plus_person = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline text-[#0b8a26]"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" /></svg>';
const icon_group = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline text-[#0b8a26]"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>';
const Isera_list_1 = computed(() => trans('Dormitory (with 12 people): <b>:price</b>', {price :'30€'})+'<span class="ml-3">'+icon_plus_person+'</span>');
const Isera_list_2 = computed(() => trans('Tent: <b>:price</b>', {price :'15€'})+'<span class="ml-3">'+icon_group+'</span>');

const Braccicorti_list_1 = computed(() => trans('Double room for one person: <b>:price</b>', {price :'80€'})+'<span class="ml-3">'+icon_person+'</span>');
const Braccicorti_list_2 = computed(() => trans('Double room for two people: <b>:price</b>', {price :'47€'})+'<span class="ml-3">'+icon_person+icon_person+'</span>');
</script>

<template>
	<div class="flex justify-center items-center">
		<h1 class="text-5xl sm:text-3xl lg:text-8xl xl:text-10xl inline-block uppercase text-[#0A7A70]">
			{{$t('Informazioni')}}
		</h1>
	</div>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div>
					<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
						<div class="flex items-center">
							<h2 class="text-4xl font-bold dark:text-white inline-block">
								{{ $t('FederludoCon 2025') }}
							</h2>
						</div>
						<p class="mt-6 text-lg text-black dark:text-slate-200 leading-relaxed" v-html="$t('about_page_presentation_1')"></p>
						<p class="mt-6 text-lg text-black dark:text-slate-200 leading-relaxed" v-html="$t('about_page_presentation_2')"></p>

					</div>

					<div>
						<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
							<div class="flex items-center">
								<h3 class="text-3xl font-bold inline-block dark:text-white">
										<p v-html="$t('Title_programm')"></p>
								</h3>
							</div>
							<p class="mt-6 text-black dark:text-slate-200 text-lg leading-relaxed">
								<p class="font-bold text-xl">{{ $t('Friday') }}</p>
								<ol class="pl-6 lg:pl-8 text-base list-disc text-black dark:text-slate-200 leading-relaxed">
									<li v-html="$t('Fri_list_1')"></li>
									<li v-html="$t('Fri_list_2')"></li>
									<li v-html="$t('Fri_list_3')"></li>
									<li v-html="$t('Fri_list_4')"></li>
								</ol>
							</p>
							<p class="mt-6 text-black dark:text-slate-200 text-lg leading-relaxed">
								<p class="font-bold text-xl">{{ $t('Saturday') }}</p>
								<ol class="pl-6 lg:pl-8 text-base list-disc text-black dark:text-slate-200 leading-relaxed">
									<li v-html="$t('Sat_list_1')"></li>
									<li v-html="$t('Sat_list_2')"></li>
									<li v-html="$t('Sat_list_3')"></li>
									<li v-html="$t('Sat_list_4')"></li>
									<li v-html="$t('Sat_list_5')"></li>
									<li v-html="$t('Sat_list_6')"></li>
									<li v-html="$t('Sat_list_7')"></li>
								</ol>
							</p>
							<p class="mt-6 text-black dark:text-slate-200 text-lg leading-relaxed">
								<p class="font-bold text-xl">{{ $t('Sunday') }}</p>
								<ol class="pl-6 lg:pl-8 pb-6 lg:pb-8 text-base list-disc text-black dark:text-slate-200 leading-relaxed">
									<li v-html="$t('Sun_list_1')"></li>
									<li v-html="$t('Sun_list_2')"></li>
									<li v-html="$t('Sun_list_3')"></li>
								</ol>
							</p>
							<p class="mt-8 text-black dark:text-slate-200 text-xl leading-relaxed">{{ $t('È necessario prenotare per partecipare all\'evento utilizzando il modulo Google Form che Federludo ha comunicato a tutte le federassociate. Se non ha ricevuto il link, contatta Federludo.') }}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
