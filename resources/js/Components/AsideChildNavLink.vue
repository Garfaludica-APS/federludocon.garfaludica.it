<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
	href: String,
	active: Boolean,
});

const classes = computed(() => {
	return props.active
		? 'flex cursor-pointer pl-6 py-1 bg-slate-600 text-white dark:text-slate-100 focus:outline-none font-bold transition duration-150 ease-in-out'
		: 'flex cursor-pointer pl-6 py-1 hover:bg-slate-600 dark:text-slate-300 dark:hover:text-white focus:outline-none transition duration-150 ease-in-out';
});

const emit = defineEmits(['click']);

function emitClick() {
	emit('click');
}
</script>

<template>
	<Link v-if="href" :href="href" :class="classes">
		<slot v-if="$slots.icon" name="icon" />
		<span class="grow text-ellipsis line-clamp-1 pr-12">
			<slot />
		</span>
	</Link>
	<a v-else :class="classes" @click.prevent="emitClick">
		<slot v-if="$slots.icon" name="icon" />
		<span class="grow text-ellipsis line-clamp-1 pr-12">
			<slot />
		</span>
	</a>
</template>
