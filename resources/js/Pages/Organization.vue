<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'The Association' }, () => h(PubLayout, () => page)),
}
</script>


<template>
	<div class="flex justify-center">
		<img :src="imageurl('logo.png')" alt="Logo Garfaludica APS" />
	</div>
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div>
					<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
						<div class="flex items-center">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-black dark:text-slate-200">
  								<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
							</svg>
						<h1 class="ms-3 text-2xl font-medium text-black dark:text-slate-200">
							{{ $t('Who we are') }}
						</h1>
						</div>
						<p class="mt-6 text-lg text-black dark:text-slate-200 leading-relaxed">
							{{$t('organization_page_presentation')}}
						</p>
					</div>

					<div>
						<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
								<h1 class="text-2xl font-medium text-black dark:text-slate-200">
									<p v-html="$t('5xM')"></p>
								</h1>
							<p class="mt-6 text-black dark:text-slate-200 text-lg leading-relaxed">
								<p v-html="$t('pre_5xM')"></p>
								<p v-html="$t('bod_5xM')"></p>
								<ol class="pl-6 lg:pl-8 pb-6 lg:pb-8 text-lg list-decimal text-black dark:text-slate-200 leading-relaxed">
									<li v-html="$t('list_5xM_1')"></li>
									<li v-html="$t('list_5xM_2')"></li>
								</ol>
								<p v-html="$t('end_5xM')"></p>
							</p>
							<p class="mt-6 text-black dark:text-slate-200 text-lg leading-relaxed">
								{{$t('Tax ID code')}} Garfaludica APS: <a class="underline decoration-green-500 font-bold">90011570463</a>
							</p>
						</div>
					</div>

					<div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
						<div>
							<div class="flex items-center">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-black dark:text-slate-200">
  									<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
								</svg>

								<h2 class="ms-3 text-xl font-semibold text-black dark:text-slate-200">
									Contact Us
								</h2>
							</div>

							<div>
								<a href="https://t.me/associazionegarfaludica" type="button" class="inline-flex items-center justify-center h-8 px-6 py-6 border-2 rounded-full font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 bg-gray-500 text-white text-sm border-indigo-500 hover:border-white hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 mx-4 my-4" target="_blank">
									<p class="text-xl"> {{ $t('Telegram Group') }}</p>
								</a>
								<br>
								<a ref="https://www.garfaludica.it/" type="button" class="inline-flex items-center justify-center h-8 px-6 py-6 border-2 rounded-full font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 bg-gray-500 text-white text-sm border-green-500 hover:border-white hover:bg-green-600 active:bg-green-700 focus:ring-indigo-500 mx-4 my-4" target="_blank">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  										<path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 0 1-1.161.886l-.143.048a1.107 1.107 0 0 0-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 0 1-1.652.928l-.679-.906a1.125 1.125 0 0 0-1.906.172L4.5 15.75l-.612.153M12.75 3.031a9 9 0 0 0-8.862 12.872M12.75 3.031a9 9 0 0 1 6.69 14.036m0 0-.177-.529A2.25 2.25 0 0 0 17.128 15H16.5l-.324-.324a1.453 1.453 0 0 0-2.328.377l-.036.073a1.586 1.586 0 0 1-.982.816l-.99.282c-.55.157-.894.702-.8 1.267l.073.438c.08.474.49.821.97.821.846 0 1.598.542 1.865 1.345l.215.643m5.276-3.67a9.012 9.012 0 0 1-5.276 3.67m0 0a9 9 0 0 1-10.275-4.835M15.75 9c0 .896-.393 1.7-1.016 2.25" />
									</svg>
									<p class="ms-3 text-xl"> {{ $t('Website') }}</p>
								</a>

							</div>
							<div class="text-sm dark:text-gray-400" v-html="$props.loripsum"></div>
						</div>
						<div>
							<div class="flex items-center">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
  									<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
								</svg>

								<h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
									Follow Us
								</h2>
							</div>
							<div>
								<a href="https://facebook.com/garfaludica" type="button" class="inline-flex items-center justify-center h-8 px-6 py-6 border-2 rounded-full font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 bg-gray-500 text-white text-sm border-blue-600 hover:border-white hover:bg-blue-600 active:bg-blue-700 focus:ring-blue-600 mx-4 my-4" target="_blank">
									<p class="text-xl"> {{ $t('Facebook page') }}</p>
								</a>
								<br>
								<a href="https://instagram.com/garfaludica" type="button" class="inline-flex items-center justify-center h-8 px-6 py-6 border-2 rounded-full font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 bg-gray-500 text-white text-sm border-pink-600 hover:border-white hover:bg-pink-600 active:bg-pink-600 focus:ring-pink-600 mx-4 my-4" target="_blank">
										<p class="text-xl"> {{ $t('Instagram Page') }}</p>
								</a>
							</div>
						</div>
					</div>
					<div>
						<div class="flex justify-center p lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
							<div>
							<div class="flex justify-center">
								<svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  									<path fill-rule="evenodd" d="M11.906 1.994a8.002 8.002 0 0 1 8.09 8.421 7.996 7.996 0 0 1-1.297 3.957.996.996 0 0 1-.133.204l-.108.129c-.178.243-.37.477-.573.699l-5.112 6.224a1 1 0 0 1-1.545 0L5.982 15.26l-.002-.002a18.146 18.146 0 0 1-.309-.38l-.133-.163a.999.999 0 0 1-.13-.202 7.995 7.995 0 0 1 6.498-12.518ZM15 9.997a3 3 0 1 1-5.999 0 3 3 0 0 1 5.999 0Z" clip-rule="evenodd"/>
								</svg>

								<h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
									Where to find us
								</h2>
							</div>
							<div>
								<a href="https://maps.app.goo.gl/2idX8ujXq7p7jAqTA" type="button" class="ms-3 text-xl inline-flex items-center justify-between h-10 px-20 py-6 border-2 uppercase rounded-full font-semibold tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 bg-gray-500 text-red-400 text-sm border-red-400 hover:bg-red-500 hover:border-white hover:text-white active:bg-red-700 focus:ring-red-500 mx-4 my-4" target="_blank">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
  										<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
  										<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
									</svg>

									{{ $t('Come to visit!') }}
								</a>
							</div>
						</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

</template>
