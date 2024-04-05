<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PubLayout from '@/Layouts/PubLayout.vue';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'The Association' }, () => h(PubLayout, () => page)),
}
</script>


<template>
	<div class="flex justify-center items-center">
		<img :src="imageurl('logo.png')" alt="Logo Garfaludica APS" />
	</div>
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
				<div>
					<div
						class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

						<h1 class="text-2xl font-medium text-gray-900 dark:text-white">
							Who we are
						</h1>

						<p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
							Welcome to the exciting play world of Garfaludica APS! Located in the picturesque setting of
							Castelnuovo di Garfagnana, we are an association dedicated to enthusiasts of board games,
							wargames, role-playing games, cards, chess and much more.

							At our headquarters, you will find a welcoming and vibrant environment, where the enthusiasm
							for the game is palpable in every corner. Whether you're a seasoned veteran or a curious
							newcomer, there's always something new to discover and someone to share your passion for the
							game with.

							Our hours are flexible to ensure maximum convenience for our members. To stay updated on
							scheduled events and opening hours, we invite you to consult our official Telegram Group,
							where you can connect with other members, participate in discussions and receive the latest
							news about the association.
						</p>
					</div>

					<div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
						<div>
							<div class="flex items-center">
								<svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  									<path fill-rule="evenodd" d="M11.906 1.994a8.002 8.002 0 0 1 8.09 8.421 7.996 7.996 0 0 1-1.297 3.957.996.996 0 0 1-.133.204l-.108.129c-.178.243-.37.477-.573.699l-5.112 6.224a1 1 0 0 1-1.545 0L5.982 15.26l-.002-.002a18.146 18.146 0 0 1-.309-.38l-.133-.163a.999.999 0 0 1-.13-.202 7.995 7.995 0 0 1 6.498-12.518ZM15 9.997a3 3 0 1 1-5.999 0 3 3 0 0 1 5.999 0Z" clip-rule="evenodd"/>
								</svg>

								<h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
									Where to find us
								</h2>
							</div>
							<div >
								<button type="button" class="inline-flex items-center justify-center h-10 px-20 py-6 border-2 rounded-full font-semibold tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-500 bg-gray-900/20 text-white text-sm border-red-500 hover:bg-red-500 hover:border-white-500 active:bg-red-700 focus:ring-red-500 mx-4 my-4" target="_blank">
									<svg class="w-6 h-6 text-red-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  										<path fill-rule="evenodd" d="M11.906 1.994a8.002 8.002 0 0 1 8.09 8.421 7.996 7.996 0 0 1-1.297 3.957.996.996 0 0 1-.133.204l-.108.129c-.178.243-.37.477-.573.699l-5.112 6.224a1 1 0 0 1-1.545 0L5.982 15.26l-.002-.002a18.146 18.146 0 0 1-.309-.38l-.133-.163a.999.999 0 0 1-.13-.202 7.995 7.995 0 0 1 6.498-12.518ZM15 9.997a3 3 0 1 1-5.999 0 3 3 0 0 1 5.999 0Z" clip-rule="evenodd"/>
									</svg>
									<a href="https://maps.app.goo.gl/2idX8ujXq7p7jAqTA"><p class="text-xl text-red-500"> Come to visit! </p></a>
								</button>
							</div>
						</div>

						<div>
							<div class="flex items-center">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
									stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
									<path stroke-linecap="round"
										d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
								</svg>
								<h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
									<a href="https://laracasts.com">Laracasts</a>
								</h2>
							</div>

							<p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
								Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript
								development. Check them out, see for yourself, and massively level up your development
								skills in the process.
							</p>

							<p class="mt-4 text-sm">
								<a href="https://laracasts.com"
									class="inline-flex items-center font-semibold text-indigo-700 dark:text-indigo-300">
									Start watching Laracasts

									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
										class="ms-1 w-5 h-5 fill-indigo-500 dark:fill-indigo-200">
										<path fill-rule="evenodd"
											d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
											clip-rule="evenodd" />
									</svg>
								</a>
							</p>
						</div>

						<div>
							<div class="flex items-center">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
									stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
									<path stroke-linecap="round" stroke-linejoin="round"
										d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
								</svg>
								<h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
									<a href="https://tailwindcss.com/">Tailwind</a>
								</h2>
							</div>

							<p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
								Laravel Jetstream is built with Tailwind, an amazing utility first CSS framework that
								doesn't get in your way. You'll be amazed how easily you can build and maintain fresh,
								modern designs with this wonderful framework at your fingertips.
							</p>
						</div>

						<div>
							<div class="flex items-center">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
									stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
									<path stroke-linecap="round" stroke-linejoin="round"
										d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
								</svg>
								<h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
									Authentication
								</h2>
							</div>

							<p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
								Authentication and registration views are included with Laravel Jetstream, as well as
								support for user email verification and resetting forgotten passwords. So, you're free
								to get started with what matters most: building your application.
							</p>
							<div class="text-sm dark:text-gray-400" v-html="$props.loripsum"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
