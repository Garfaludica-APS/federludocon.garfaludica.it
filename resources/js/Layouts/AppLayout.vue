<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NavButton from '@/Components/NavButton.vue';
import DarkModeSwitcher from '@/Components/DarkModeSwitcher.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import Footer from '@/Components/Footer.vue';
import { loadLanguageAsync, getActiveLanguage } from 'laravel-vue-i18n';

defineProps({
	title: String,
});

const showingNavigationDropdown = ref(false);

const locRoutes = ref([
	{ name: 'home', route: 'home', url: '', active: false },
	{ name: 'about', route: 'about', url: '', active: false },
	{ name: 'hotels', route: 'hotels', url: '', active: false },
	{ name: 'venue', route: 'venue', url: '', active: false },
	{ name: 'organization', route: 'organization', url: '', active: false },
	{ name: 'contact', route: 'contact', url: '', active: false },
	{ name: 'book', route: 'book', url: '', active: false },
]);

function localizeRoutes(lang, replace = true) {
	for (let i = 0; i < locRoutes.value.length; i++) {
		if (lang === 'it')
			locRoutes.value[i].route = locRoutes.value[i].name;
		else
			locRoutes.value[i].route = lang + '.' + locRoutes.value[i].name;
		locRoutes.value[i].url = route(locRoutes.value[i].route);
		if (replace && locRoutes.value[i].active) {
			const st = window.history.state;
			st.url = locRoutes.value[i].url;
			window.history.replaceState(st, document.title, locRoutes.value[i].url);
		}
	}
}

function onScroll(e)
{
	if (e.target.scrollTop > 80) {
		document.querySelector('header').classList.remove('ml:bg-transparent', 'dark:ml:bg-transparent', 'ml:shadow-none');
		document.querySelector('header').classList.add('is-scrolled');
	} else {
		document.querySelector('header').classList.add('ml:bg-transparent', 'dark:ml:bg-transparent', 'ml:shadow-none');
		document.querySelector('header').classList.remove('is-scrolled');
	}
}

onMounted(() => {
	localizeRoutes(getActiveLanguage(), false);
	for (let i = 0; i < locRoutes.value.length; i++)
		locRoutes.value[i].active = route().current(locRoutes.value[i].route);
	const p = document.querySelector('div.bg-gobcon-poster');
	if (p.scrollTop > 80) {
		document.querySelector('header').classList.remove('ml:bg-transparent', 'dark:ml:bg-transparent', 'ml:shadow-none');
		document.querySelector('header').classList.add('is-scrolled');
	}
	p.addEventListener('scroll', onScroll);
});

onUnmounted(() => {
	document.querySelector('div.bg-gobcon-poster').removeEventListener('scroll', onScroll);
});
</script>

<template>
	<Head :title="title" />

	<div class="min-h-screen min-w-full bg-gobcon-poster bg-cover bg-center bg-no-repeat overflow-y-scroll h-screen">
		<div class="min-h-screen min-w-full bg-overlay">
			<header class="fixed inset-x-0 top-0 z-50 pt-1 bg-white dark:bg-slate-900 ml:bg-transparent dark:ml:bg-transparent shadow ml:shadow-none transition-background duration-500 group">
				<div>
					<nav class="max-w-7xl mx-auto px-4 ml:px-6 lg:px-8">

						<div class="flex justify-between h-12">

							<div class="shrink-9 flex items-center">
								<Link :href="locRoutes[0].url">
									<ApplicationLogo class="h-12 w-auto" titleClasses="h-full w-auto hidden 2xs:max-ml:inline-block lg:inline-block" />
								</Link>
							</div>

							<div class="hidden space-x-8 ml:-my-px ml:ms-10 ml:flex ml:ml-auto">
								<NavLink :href="locRoutes[0].url" :active="locRoutes[0].active">
									{{ $t('Home') }}
								</NavLink>
								<NavLink :href="locRoutes[1].url" :active="locRoutes[1].active">
									{{ $t('About') }}
								</NavLink>
								<NavLink :href="locRoutes[2].url" :active="locRoutes[2].active">
									{{ $t('Hotels') }}
								</NavLink>
								<NavLink :href="locRoutes[3].url" :active="locRoutes[3].active">
									{{ $t('Venue') }}
								</NavLink>
								<NavLink :href="locRoutes[4].url" :active="locRoutes[4].active">
									{{ $t('The Organization') }}
								</NavLink>
								<NavLink :href="locRoutes[5].url" :active="locRoutes[5].active">
									{{ $t('Contact') }}
								</NavLink>
								<NavButton :href="locRoutes[6].url" :active="locRoutes[6].active">
									{{ $t('Book Now!') }}
								</NavButton>
							</div>

							<div class="-me-2 flex items-center px-4 space-x-6">
								<DarkModeSwitcher class="h-8 w-8 text-gray-500 dark:text-gray-350" />
								<LanguageSwitcher class="h-8 w-8 text-gray-500 dark:text-gray-350" @switchLang="localizeRoutes" />

								<button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition duration-150 ease-in-out ml:hidden" :class="{'bg-gray-100 dark:bg-gray-900': showingNavigationDropdown}" @click="showingNavigationDropdown = !showingNavigationDropdown">
									<svg
										class="h-6 w-6"
										stroke="currentColor"
										fill="none"
										viewBox="0 0 24 24"
									>
										<path
											:class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M4 6h16M4 12h16M4 18h16"
										/>
										<path
											:class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M6 18L18 6M6 6l12 12"
										/>
									</svg>
								</button>
							</div>
						</div>

						<div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="ml:hidden">
							<div class="pt-2 pb-3 space-y-1">
								<ResponsiveNavLink :href="locRoutes[0].url" :active="locRoutes[0].active">
									{{ $t('Home') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="locRoutes[1].url" :active="locRoutes[1].active">
									{{ $t('About') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="locRoutes[2].url" :active="locRoutes[2].active">
									{{ $t('Hotels') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="locRoutes[3].url" :active="locRoutes[3].active">
									{{ $t('Venue') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="locRoutes[4].url" :active="locRoutes[4].active">
									{{ $t('The Organization') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="locRoutes[5].url" :active="locRoutes[5].active">
									{{ $t('Contact') }}
								</ResponsiveNavLink>
								<NavButton :href="locRoutes[6].url" :active="locRoutes[6].active">
									{{ $t('Book Now!') }}
								</NavButton>
							</div>
						</div>

					</nav>
				</div>
			</header>

			<main class="z-10 py-20">
				<slot />
			</main>
		</div>

		<footer class="bg-gray-300 dark:bg-slate-900 text-gray-800 dark:text-gray-100">
			<div class="bg-slate-100 dark:bg-footer py-16">
				<div class="max-w-7xl mx-auto px-4 ml:px-6 lg:px-8">
					<Footer />
				</div>
			</div>
			<div class="py-7">
				<div class="max-w-7xl mx-auto px-4 ml:px-6 lg:px-8 text-center">
					Copyright © 2024 - <a class="text-rose-500 font-black" rel="noopener" href="https://www.garfaludica.it" target="_blank">Garfaludica APS</a> - Some Rights Reserved. (<Link class="hover:underline" :href="route('license')">MIT License</Link>)<br>Images and logos: All Rights Reserved.
				</div>
			</div>
		</footer>

	</div>
</template>
