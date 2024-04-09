<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NavButton from '@/Components/NavButton.vue';
import DarkModeSwitcher from '@/Components/DarkModeSwitcher.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import Footer from '@/Components/Footer.vue';
import Copyright from '@/Components/Copyright.vue';
import CookiePolicy from '@/Components/CookiePolicy.vue';
import { getActiveLanguage } from 'laravel-vue-i18n';

const page = usePage();

const showingNavigationDropdown = ref(false);

function localizeRoutes(lang, replace = true)
{
	Ziggy.routeNamePrefix = lang === 'it' ? '' : (lang + '.');
	if (!replace)
		return;
	const cur = route().current();
	const base = cur.startsWith('en.') ? cur.substring(3) : cur;
	const newRoute = lang === 'it' ? base : (lang + '.' + base);
	let newUrl;
	try {
		newUrl = route(newRoute, undefined, false);
	} catch(e) {
		newUrl = route(base, undefined, false);
	}
	const st = window.history.state;
	st.url = newUrl;
	window.history.replaceState(st, document.title, newUrl);
}

const scrollElem = ref(null);
const header = ref(null);

function onScroll(e)
{
	if (e.target.scrollTop > 80) {
		header.value.classList.remove('ml:bg-transparent', 'dark:ml:bg-transparent', 'ml:shadow-none');
		header.value.classList.add('is-scrolled');
	} else {
		header.value.classList.add('ml:bg-transparent', 'dark:ml:bg-transparent', 'ml:shadow-none');
		header.value.classList.remove('is-scrolled');
	}
}

onMounted(() => {
	localizeRoutes(getActiveLanguage(), false);

	if (scrollElem.value.scrollTop > 80) {
		header.value.classList.remove('ml:bg-transparent', 'dark:ml:bg-transparent', 'ml:shadow-none');
		header.value.classList.add('is-scrolled');
	}
	scrollElem.value.addEventListener('scroll', onScroll);

	if (localStorage.getItem('cookie-policy') !== 'accepted') {
		setTimeout(() => {
			cookieBannerVisible.value = true;
		}, 1000);
	}
});

const cookieBannerVisible = ref(false);

const acceptCookies = () => {
	cookieBannerVisible.value = false;
	localStorage.setItem('cookie-policy', 'accepted');
};

onUnmounted(() => {
	scrollElem.removeEventListener('scroll', onScroll);
});
</script>

<template>
	<div ref="scrollElem" class="min-h-screen min-w-full bg-gobcon-poster bg-cover bg-center bg-no-repeat overflow-y-scroll h-screen scroll-smooth" scroll-region>
		<div class="min-h-screen min-w-full bg-overlay flex items-center justify-center">
			<header ref="header" class="fixed inset-x-0 top-0 z-50 pt-1 bg-white dark:bg-slate-900 ml:bg-transparent dark:ml:bg-transparent shadow-md ml:shadow-none transition-background duration-500 group">
				<div>
					<nav class="max-w-7xl mx-auto px-4 ml:px-6 lg:px-8">

						<div class="flex justify-between h-12">

							<div class="shrink-9 flex items-center">
								<Link :href="lroute('home')">
									<ApplicationLogo class="h-12 w-auto" titleClasses="h-full w-auto hidden 2xs:max-ml:inline-block lg:inline-block" />
								</Link>
							</div>

							<div class="hidden space-x-8 ml:-my-px ml:ms-10 ml:flex ml:ml-auto">
								<NavLink :href="lroute('home')" :active="currentRoute('home')">
									{{ $t('Home') }}
								</NavLink>
								<NavLink :href="lroute('about')" :active="currentRoute('about')">
									{{ $t('About') }}
								</NavLink>
								<NavLink :href="lroute('hotels')" :active="currentRoute('hotels')">
									{{ $t('Hotels') }}
								</NavLink>
								<NavLink :href="lroute('venue')" :active="currentRoute('venue')">
									{{ $t('Venue') }}
								</NavLink>
								<NavLink :href="lroute('organization')" :active="currentRoute('organization')">
									{{ $t('Garfaludica') }}
								</NavLink>
								<NavLink :href="lroute('contact')" :active="currentRoute('contact')">
									{{ $t('Contact') }}
								</NavLink>
								<NavButton v-if="page.props.settings.portalOpen" :href="lroute('book')" :active="currentRoute('book')">
									{{ $t('Book') }}
								</NavButton>
								<!--sse-->
								<Link :href="lroute('admin.index')" class="inline-flex items-center absolute py-1 px-1 top-3 right-3 ml:max-2xl:mt-12 text-sm font-medium leading-5 text-gray-350 group-[.is-scrolled]:text-gray-500 group-[.is-scrolled]:dark:text-gray-350 hover:text-gray-200 group-[.is-scrolled]:hover:text-gray-700 group-[.is-scrolled]:dark:hover:text-gray-200 focus:outline-none focus:text-gray-300 group-[.is-scrolled]:focus:text-gray-700 group-[.is-scrolled]:dark:focus:text-gray-300 group-[.is-scrolled]:dark:focus:text-gray-200 transition duration-150 ease-in-out">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 mr-1">
										<path fill-rule="evenodd" d="M8 1a3.5 3.5 0 0 0-3.5 3.5V7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7V4.5A3.5 3.5 0 0 0 8 1Zm2 6V4.5a2 2 0 1 0-4 0V7h4Z" clip-rule="evenodd" />
									</svg>
									{{ $t('Admin Panel') }}
								</Link>
								<!--/sse-->
							</div>

							<div class="-me-2 flex items-center px-4 space-x-6">
								<DarkModeSwitcher class="h-8 w-8 text-gray-350 group-[.is-scrolled]:text-gray-500 group-[.is-scrolled]:dark:text-gray-350 hover:text-gray-200 group-[.is-scrolled]:hover:text-gray-700 group-[.is-scrolled]:dark:hover:text-gray-200 hover:border-gray-500 focus:outline-none focus:text-gray-300 group-[.is-scrolled]:focus:text-gray-700 group-[.is-scrolled]:dark:focus:text-gray-300 focus:border-gray-500 group-[.is-scrolled]:dark:focus:text-gray-200 transition duration-150 ease-in-out" />
								<LanguageSwitcher class="h-8 w-8 text-gray-350 group-[.is-scrolled]:text-gray-500 group-[.is-scrolled]:dark:text-gray-350 hover:text-gray-200 group-[.is-scrolled]:hover:text-gray-700 group-[.is-scrolled]:dark:hover:text-gray-200 hover:border-gray-500 focus:outline-none focus:text-gray-300 group-[.is-scrolled]:focus:text-gray-700 group-[.is-scrolled]:dark:focus:text-gray-300 focus:border-gray-500 group-[.is-scrolled]:dark:focus:text-gray-200 transition duration-150 ease-in-out" @switchLang="localizeRoutes" />

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
								<ResponsiveNavLink :href="lroute('home')" :active="currentRoute('home')">
									{{ $t('Home') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="lroute('about')" :active="currentRoute('about')">
									{{ $t('About') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="lroute('hotels')" :active="currentRoute('hotels')">
									{{ $t('Hotels') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="lroute('venue')" :active="currentRoute('venue')">
									{{ $t('Venue') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="lroute('organization')" :active="currentRoute('organization')">
									{{ $t('Garfaludica') }}
								</ResponsiveNavLink>
								<ResponsiveNavLink :href="lroute('contact')" :active="currentRoute('contact')">
									{{ $t('Contact') }}
								</ResponsiveNavLink>
								<NavButton v-if="page.props.settings.portalOpen" :href="lroute('book')" :active="currentRoute('book')">
									{{ $t('Book') }}
								</NavButton>
								<!--sse-->
								<Link :href="lroute('admin.index')" class="inline-flex items-center py-1 px-1 float-right text-sm font-medium leading-5 text-gray-500 dark:text-gray-350 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 tansition duration-150 ease-in-out">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 mr-1">
										<path fill-rule="evenodd" d="M8 1a3.5 3.5 0 0 0-3.5 3.5V7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7V4.5A3.5 3.5 0 0 0 8 1Zm2 6V4.5a2 2 0 1 0-4 0V7h4Z" clip-rule="evenodd" />
									</svg>
									{{ $t('Admin Panel') }}
								</Link>
								<!--/sse-->
							</div>
						</div>

					</nav>
				</div>
			</header>

			<main class="z-10 py-20">
				<slot />
			</main>
		</div>

		<footer class="bg-gray-300 dark:bg-slate-900 text-gray-800 dark:text-gray-100 shadow-md">
			<div class="bg-slate-100 dark:bg-footer py-16">
				<div class="max-w-7xl mx-auto px-4 ml:px-6 lg:px-8">
					<Footer />
				</div>
			</div>
			<Copyright />
		</footer>

	</div>
	<CookiePolicy :visible="cookieBannerVisible" @accept="acceptCookies"/>
</template>
