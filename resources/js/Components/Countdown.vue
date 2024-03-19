<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const emit = defineEmits(['countdown-end']);

const props = defineProps({
	startSeconds: Number,
});

const seconds = ref(props.startSeconds);
const minutes = computed(() => Math.floor(seconds.value / 60));
const hours = computed(() => Math.floor(minutes.value / 60));
const days = computed(() => Math.floor(hours.value / 24));

function decrement() {
	if (seconds.value <= 0) {
		emit('countdown-end');
		clearInterval(interval);
		return;
	}
	seconds.value--;
};

var interval;
onMounted(() => {
	interval = setInterval(decrement, 1000);
});

onUnmounted(() => {
	clearInterval(interval);
});
</script>

<template>
	<div class="grid mx-auto w-[40%] grid-cols-7 text-lg xs:text-2xl md:text-4xl xl:text-6xl [&>div]:leading-3 md:[&>div]:leading-6 text-center font-bold motion-safe:animate-bounce">
		<div>
			{{ days.toLocaleString(undefined, {minimumIntegerDigits: 2}) }}<br>
			<span class="hidden xs:inline-block text-xs md:text-base">{{ $t('days') }}</span>
		</div>
		<div>:</div>
		<div>
			{{ (hours % 24).toLocaleString(undefined, {minimumIntegerDigits: 2}) }}<br>
			<span class="hidden xs:inline-block text-xs md:text-base">{{ $t('hours') }}</span>
		</div>
		<div>:</div>
		<div>
			{{ (minutes % 60).toLocaleString(undefined, {minimumIntegerDigits: 2}) }}<br>
			<span class="hidden xs:inline-block text-xs md:text-base">{{ $t('minutes') }}</span>
		</div>
		<div>:</div>
		<div>
			{{ (seconds % 60).toLocaleString(undefined, {minimumIntegerDigits: 2}) }}<br>
			<span class="hidden xs:inline-block text-xs md:text-base">{{ $t('seconds') }}</span>
		</div>
	</div>
</template>
