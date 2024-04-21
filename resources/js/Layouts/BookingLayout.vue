<script setup>
import Copyright from '@/Components/Copyright.vue'
</script>

<template>
	<div class="flex flex-col min-h-screen min-w-full bg-slate-100 dark:bg-slate-900">
		<header class="grow-0 shrink basis-auto my-4 max-w-screen-2xl mx-auto p-2">
			Garfaludica APS - header
		</header>
		<main class="py-8 px-2 grow shrink basis-auto max-w-screen-2xl mx-auto">
			<slot />
		</main>
		<footer class="grow-0 shrink basis-[156px] bg-gray-300 dark:bg-slate-900 text-gray-800 dark:text-gray-100 shadow-md mt-12">
			<Copyright />
		</footer>
	</div>
</template>
