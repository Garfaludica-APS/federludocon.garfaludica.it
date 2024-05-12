<script setup>
import { Link, Head } from '@inertiajs/vue3';
</script>

<template>
	<Head :title="$t('Order Confirmed')" />
	<div class="flex flex-col min-h-screen min-w-full bg-slate-100 dark:bg-slate-900 text-black dark:text-white">
		<DarkModeSwitcher class="fixed top-0 right-0 mt-4 mr-4 h-8 w-8 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 dark:hover:text-gray-200 dark:focus:text-gray-200 transition duration-150 ease-in-out" />
		<header class="grow-0 shrink basis-auto mt-4 mb-2 max-w-screen-2xl mx-auto p-2">
			<ApplicationLogo class="h-20 mx-auto w-max max-w-screen" />
			<p class="text-center text-xs">{{ $t('Third Sector Organization (RUNTS rep. n. 113019)') }}</p>
			<p class="text-center mt-3 text-xl font-semibold">{{ $t('GobCon 2024 Garfagnana - Booking Portal') }}</p>
		</header>
		<main class="py-8 px-2 grow shrink basis-auto max-w-screen-2xl mx-auto">
			<MazIcon name="check-circle" path="/storage/icons" class="h-72 w-72 text-green-500 dark:text-green-400 mx-auto" />
			<h1 class="text-5xl text-center text-green-500 dark:text-green-400 font-semibold">{{ $t('Order Confirmed!') }}</h1>
			<p class="text-center mt-3">{{ $t('Your order has been successfully processed.') }}</p>
			<p class="text-center">{{ $t('You should receive a confirmation email shortly.') }}</p>
			<p class="text-center">{{ $t('If you don\'t receive the email, please reach us at info@garfaludica.it.') }}</p>
			<p class="text-center">{{ $t('See you at the GobCon!') }}</p>
			<Link :href="route('home')" class="block text-center mt-8 text-blue-500 dark:text-blue-400 hover:underline">{{ $t('Back to Home') }}</Link>
		</main>
		<footer class="grow-0 shrink basis-[156px] bg-gray-300 dark:bg-slate-900 text-gray-800 dark:text-gray-100 shadow-md mt-12">
			<Copyright />
		</footer>
	</div>
</template>
