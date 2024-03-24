<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import DarkModeSwitcher from '@/Components/DarkModeSwitcher.vue';
import Copyright from '@/Components/Copyright.vue';

defineProps({
	title: {
		type: String,
		default: 'Login',
	},
});
</script>

<template>
	<Head :title="$t(title)" />
	<div class="flex items-center justify-center min-h-screen bg-gradient-to-tr from-gray-400 via-emerald-400 to-slate-400 dark:from-gray-800 dark:via-rose-700 dark:to-slate-800">
		<DarkModeSwitcher class="absolute top-0 right-0 mt-4 mr-4 h-8 w-8 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 dark:hover:text-gray-200 dark:focus:text-gray-200 transition duration-150 ease-in-out" />
		<div class="w-full flex p-6 flex-col items-center justify-between min-h-screen">
			<main class="w-max my-auto animate-fade-in">
				<ApplicationLogo class="w-max max-w-screen h-20 xs:h-24 sm:h-28 mx-auto" />
				<!--sse-->
				<slot />
				<!--/sse-->
				<div class="text-center mt-10">
					<Link :href="lroute('home')" class="text-lg font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200">
						{{ $t('Back to Home') }}
					</Link>
				</div>
			</main>
			<footer class="text-gray-800 dark:text-gray-100">
				<Copyright />
			</footer>
		</div>
	</div>
</template>
