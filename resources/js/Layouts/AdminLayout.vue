<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { mdiBackburger, mdiForwardburger, mdiLogout } from '@mdi/js';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import DarkModeSwitcher from '@/Components/DarkModeSwitcher.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import AsideNavLink from '@/Components/AsideNavLink.vue';
import Copyright from '@/Components/Copyright.vue';

const isMenuOpen = ref(false);

const page = usePage();

const admin = computed(() => page.props.auth.admin);
</script>

<template>
		<div class="min-h-screen w-screen lg:w-auto pt-14 bg-gray-100 dark:bg-slate-800 text-black dark:text-slate-100" :class="{ 'pl-72': isMenuOpen, 'lg:pl-72': !isMenuOpen }">
		<nav class="top-0 inset-x-0 fixed bg-gray-100 h-14 z-30 w-screen lg:w-auto dark:bg-slate-800" :class="{ 'pl-72': isMenuOpen, 'lg:pl-80': !isMenuOpen }">
			<div class="flex lg:items-stretch justify-end max-w-7xl mx-auto shadow-lg lg:shadow-none">
				<div class="flex lg:hidden py-2 px-3 items-center cursor-pointer dark:text-white dark:hover:text-slate-400" @click="isMenuOpen = !isMenuOpen">
					<span class="inline-flex justify-center items-center w-8 h-8">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
							<path :d="isMenuOpen ? mdiBackburger : mdiForwardburger" />
						</svg>
					</span>
				</div>
				<div class="flex flex-1 items-stretch">
					<span class="h-14 py-2 pr-4 font-semibold flex items-center align-center" :class="isMenuOpen ? 'hidden lg:flex' : ''">
						{{ $t('Welcome back, :name', { name: admin.username }) }}

					</span>
				</div>
				<div class="flex flex-1 justify-end items-stretch space-x-2" :class="isMenuOpen ? 'hidden lg:flex' : ''">
					<DarkModeSwitcher class="h-14 w-14 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-350 dark:hover:text-gray-200 focus:outline-none focus:text-gray-300 transition duration-150 ease-in-out" />
					<LanguageSwitcher class="h-14 w-14 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-350 dark:hover:text-gray-200 focus:outline-none focus:text-gray-300 transition duration-150 ease-in-out" />
					<Link :href="route('auth.logout')" method="post" as="button" class="h-14 w-14 py-3 text-gray-500 hover:text-gray-700 dark:text-gray-350 dark:hover:text-gray-200 focus:outline-none focus:text-gray-300 transition duration-150 ease-in-out">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentcolor" class="h-full w-auto mx-auto">
							<path :d="mdiLogout" />
						</svg>
					</Link>
				</div>
			</div>
		</nav>
		<aside class="lg:py-2 lg:pl-2 w-72 fixed flex z-40 top-0 h-screen transition-[left]" :class="{ '-left-72 lg:left-0': !isMenuOpen, 'left-0': isMenuOpen }">
			<nav class="lg:rounded-2xl flex-1 flex flex-col overflow-hidden text-slate-100 bg-slate-900">
				<div class="flex flex-row h-14 items-center justify-between text-center dark:bg-slate-900">
					<ApplicationLogo class="h-10 w-auto mx-auto" />
				</div>
				<div class="flex flex-row h-6 items-center align-center text-center text-lg font-bold dark:bg-slate-900 mb-4">
					<span class="mx-auto">{{ $t('Admin Panel') }}</span>
				</div>
				<ul class="flex-1 overflow-y-auto overflow-x-hidden">
					<li>
						<AsideNavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
							<template #icon>
								 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="inline-block w-16 min-w-16 h-6">
									<path fill-rule="evenodd" d="M2.25 5.25a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3V15a3 3 0 0 1-3 3h-3v.257c0 .597.237 1.17.659 1.591l.621.622a.75.75 0 0 1-.53 1.28h-9a.75.75 0 0 1-.53-1.28l.621-.622a2.25 2.25 0 0 0 .659-1.59V18h-3a3 3 0 0 1-3-3V5.25Zm1.5 0v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5Z" clip-rule="evenodd" />
								</svg>
							</template>
							{{ $t('Dashboard') }}
						</AsideNavLink>
					</li>
					<li v-for="hotel in page.props.auth.admin.hotels">
						<AsideNavLink :href="route('admin.hotel.presentation.show', { hotel: hotel })" :active="route().current('admin.hotel.presentation.show', { hotel: hotel })">
							<template #icon>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="inline-block w-16 min-w-16 h-6">
								<path d="M11.584 2.376a.75.75 0 0 1 .832 0l9 6a.75.75 0 1 1-.832 1.248L12 3.901 3.416 9.624a.75.75 0 0 1-.832-1.248l9-6Z" />
								<path fill-rule="evenodd" d="M20.25 10.332v9.918H21a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1 0-1.5h.75v-9.918a.75.75 0 0 1 .634-.74A49.109 49.109 0 0 1 12 9c2.59 0 5.134.202 7.616.592a.75.75 0 0 1 .634.74Zm-7.5 2.418a.75.75 0 0 0-1.5 0v6.75a.75.75 0 0 0 1.5 0v-6.75Zm3-.75a.75.75 0 0 1 .75.75v6.75a.75.75 0 0 1-1.5 0v-6.75a.75.75 0 0 1 .75-.75ZM9 12.75a.75.75 0 0 0-1.5 0v6.75a.75.75 0 0 0 1.5 0v-6.75Z" clip-rule="evenodd" />
								<path d="M12 7.875a1.125 1.125 0 1 0 0-2.25 1.125 1.125 0 0 0 0 2.25Z" />
							</svg>

							</template>
							{{ $t('hotel_name_' + hotel.name) }}
						</AsideNavLink>
					</li>
					<li>
						<AsideNavLink :href="route('admin.admins.index')" :active="route().current('admin.admins.index')">
							<template #icon>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="inline-block w-16 min-w-16 h-6">
									<path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z" />
								</svg>
							</template>
							{{ $t('Administrators') }}
						</AsideNavLink>
					</li>
				</ul>
				<ul>
					<li>
						<Link :href="route('auth.logout')" method="post" as="button" class="w-full flex cursor-pointer py-3 focus:outline-none font-bold border-blue-600 dark:border-blue-500 ring-blue-300 dark:ring-blue-700 bg-blue-600 dark:bg-blue-500 text-white  hover:bg-blue-700 hover:border-blue-700 hover:dark:bg-blue-600 hover:dark:border-blue-600 transition duration-150 ease-in-out">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-16 h-6">
								<path :d="mdiLogout" />
							</svg>
							<span class="grow text-ellipsis line-clamp-1 pr-12 text-left">
								{{ $t('Logout') }}
							</span>
						</Link>
					</li>
				</ul>
			</nav>
		</aside>
		<div class="max-w-7xl mx-auto mt-10">
			<header class="mb-10 py-2 px-8" v-if="$slots.header">
				<slot name="header" />
			</header>
			<main class="py-2 px-8">
				<slot />
			</main>
		</div>
	</div>
	<footer class="bg-gray-100 dark:bg-slate-800 text-gray-800 dark:text-gray-100 shadow-md">
		<Copyright />
	</footer>
</template>
