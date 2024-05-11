<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
	href: String,
	active: Boolean,
});

const classes = computed(() => {
	return props.active
		? 'flex cursor-pointer py-3 dark:text-slate-300 focus:outline-none font-bold brightness-200 transition duration-150 ease-in-out'
		: 'flex cursor-pointer py-3 dark:text-slate-300 dark:hover:text-white focus:outline-none transition duration-150 ease-in-out';
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
