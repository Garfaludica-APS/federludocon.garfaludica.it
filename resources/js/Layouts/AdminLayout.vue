<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import DarkModeSwitcher from '@/Components/DarkModeSwitcher.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import AsideNavLink from '@/Components/AsideNavLink.vue';

defineProps({
	title: {
		type: String,
		default: 'Admin Panel'
	}
});
</script>

<template>
	<Head :title="$t(title)" />

	<div class="min-h-screen w-screen lg:w-auto xl:pl-60 pt-14 bg-gray-100 dark:bg-slate-800 text-black dark:text-slate-100">
		<nav class="top-0 inset-x-0 fixed bg-gray-100 h-14 z-30 w-screen lg:w-auto dark:bg-slate-800 xl:pl-60">
			<div class="flex lg:items-stretch justify-end max-w-7xl mx-auto shadow-lg lg:shadow-none">
				<div class="flex flex-1 items-stretch">
					<span class="h-14 py-2 pr-4 font-semibold flex items-center align-center">
						Logged in as: John Doe
					</span>
				</div>
				<div class="flex flex-1 justify-end items-stretch space-x-2">
					<DarkModeSwitcher class="h-14 w-14 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-350 dark:hover:text-gray-200 focus:outline-none focus:text-gray-300 transition duration-150 ease-in-out" />
					<LanguageSwitcher class="h-14 w-14 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-350 dark:hover:text-gray-200 focus:outline-none focus:text-gray-300 transition duration-150 ease-in-out" />
				</div>
			</div>
		</nav>
		<aside class="lg:py-2 lg:pl-2 w-60 fixed flex z-40 top-0 h-screen -left-60 lg:left-0 lg:hidden xl:flex">
			<nav class="lg:rounded-2xl flex-1 flex flex-col overflow-hidden dark:bg-slate-900">
				<div class="flex flex-row h-14 items-center justify-between text-center dark:bg-slate-900">
					<ApplicationLogo class="h-10 w-auto mx-auto" />
				</div>
				<div class="flex flex-row h-6 items-center align-center text-center text-lg font-bold dark:bg-slate-900 mb-4">
					<span class="mx-auto">{{ $t('Admin Panel') }}</span>
				</div>
				<ul class="flex-1 overflow-y-auto overflow-x-hidden">
					<li>
						<AsideNavLink :href="route('admin')" :active="route().current('admin')">
							<template #icon>
								 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="inline-block w-16 h-6">
									<path fill-rule="evenodd" d="M2.25 5.25a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3V15a3 3 0 0 1-3 3h-3v.257c0 .597.237 1.17.659 1.591l.621.622a.75.75 0 0 1-.53 1.28h-9a.75.75 0 0 1-.53-1.28l.621-.622a2.25 2.25 0 0 0 .659-1.59V18h-3a3 3 0 0 1-3-3V5.25Zm1.5 0v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5Z" clip-rule="evenodd" />
								</svg>
							</template>
							{{ $t('Dashboard') }}
						</AsideNavLink>
						<AsideNavLink :href="route('admin.admins')" :active="route().current('admin.admins')">
							<template #icon>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="inline-block w-16 h-6">
									<path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z" />
								</svg>
							</template>
							{{ $t('Administrators') }}
						</AsideNavLink>
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
			<footer>
			</footer>
		</div>
	</div>
</template>
