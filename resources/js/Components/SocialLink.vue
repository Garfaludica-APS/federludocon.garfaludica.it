<script setup>
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faFacebook, faInstagram, faTelegram, faGithub, faDiscord } from '@fortawesome/free-brands-svg-icons';
import { faRobot, faLocationDot, faGlobe } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
	href: String,
	icon: String,
});

const faIcon = computed(() => {
	switch (props.icon) {
	case 'web':
	case 'website':
		return faGlobe;
	case 'facebook':
		return faFacebook;
	case 'instagram':
		return faInstagram;
	case 'telegram':
		return faTelegram;
	case 'github':
		return faGithub;
	case 'discord':
		return faDiscord;
	case 'location':
	case 'map':
	case 'position':
		return faLocationDot;
	case 'robot':
	case 'bot':
		return faRobot;
	default:
		throw new Error('Unknown icon: ' + props.icon);
	}
});

const hoverColor = computed(() => {
	switch (props.icon) {
	case 'web':
	case 'website':
		return 'hover:bg-slate-300 focus:bg-slate-300 hover:text-black focus:text-black';
	case 'facebook':
		return 'hover:bg-[#3b5998] focus:bg-[#3b5998] hover:text-white focus:text-white';
	case 'instagram':
		return 'hover:bg-[#d62976] focus:bg-[#d62976] hover:text-white focus:text-white';
	case 'robot':
	case 'bot':
	case 'telegram':
		return 'hover:bg-[#08c] focus:bg-[#08c] hover:text-white focus:text-white';
	case 'github':
		return 'hover:bg-[#646c70] focus:bg-[#646c70] hover:text-white focus:text-white';
	case 'discord':
		return 'hover:bg-[#5865f2] focus:bg-[#5865f2] hover:text-white focus:text-white';
	case 'location':
	case 'map':
	case 'position':
		return 'hover:bg-[#ea4335] focus:bg-[#ea4335] hover:text-white focus:text-white';
	default:
		throw new Error('Unknown icon: ' + props.icon);
	}
});
</script>

<template>
	<a rel="external noopener" :href="href" target="_blank" class="flex h-10 w-10 text-center items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-full p-2 text-xl text-gray-600 dark:text-gray-300 focus:outline-none transition duration-500 ease-in-out" :class="hoverColor">
		<FontAwesomeIcon :icon="faIcon" class="" />
	</a>
</template>
