<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faCodeBranch, faBug } from '@fortawesome/free-solid-svg-icons';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
	repository: {
		type: String,
		default: 'https://github.com/Garfaludica-APS/gobcon.garfaludica.it',
	},
	bugReportEmail: {
		type: String,
		default: 'info@garfaludica.it',
	},
	bugReportSubject: {
		type: String,
		default: 'Bug Report - GobCon Garfagnana Booking Portal',
	},
});

const bugReportMailto = computed(() => {
	return 'mailto:' + props.bugReportEmail
		+ '?subject=' + encodeURIComponent(props.bugReportSubject)
		+ '&body=' + encodeURIComponent(trans('Please describe the bug you found in our application. Try to explain how to reproduce the bug and, if possible, attach a screenshot.'));
});
</script>
<template>
	<div class="pt-8">
		<div class="max-w-7xl mx-auto px-4 ml:px-6 lg:px-8 text-center">
			Copyright © 2024 - <a class="text-rose-500 font-black" rel="noopener" href="https://www.garfaludica.it" target="_blank">Garfaludica APS</a> - Some Rights Reserved. (<Link class="hover:underline" :href="route('modal.license', {redirect: route().current() })" preserve-state preserve-scroll>MIT License</Link>)<br>Images and logos: All Rights Reserved.
			<div class="flex justify-center items-center mx-auto space-x-3">
			<a rel="external noopener" :href="repository" target="_blank" class="flex mt-2 w-fit cursor-pointer text-center items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-full py-2 px-4 text-xs text-gray-600 dark:text-gray-300 focus:outline-none hover:bg-[#646c70] focus:bg-[#646c70] hover:text-white focus:text-white transition duration-500 ease-in-out">
				<FontAwesomeIcon :icon="faCodeBranch" />
				<span class="ml-2">GitHub</span>
			</a>
			<a rel="external noopener" :href="bugReportMailto" target="_blank" class="flex mt-2 w-fit cursor-pointer text-center items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-full py-2 px-4 text-xs text-gray-600 dark:text-gray-300 focus:outline-none hover:bg-rose-600 focus:bg-rose-600 hover:text-white focus:text-white transition duration-500 ease-in-out">
				<FontAwesomeIcon :icon="faBug" />
				<span class="ml-2">{{ $t('Segnala Bug') }}</span>
			</a>
			</div>
			<!--sse-->
			<p class="mt-4 text-stone-700 dark:text-stone-400 text-sm [&>a]:text-rose-500 [&>a]:font-black" v-html="$t('footer_dev_text')"></p>
			<!--/sse-->
		</div>
	</div>
</template>
