<script setup>
import { computed } from 'vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import ApplicationTitle from '@/Components/ApplicationTitle.vue';

const props = defineProps({
	markClasses: {
		type: String,
		default: () => 'inline-block h-full w-auto mr-2',
	},
	titleClasses: {
		type: String,
		default: () => 'inline-block h-full w-auto',
	}
});

</script>

<template>
	<div class="whitespace-nowrap">
		<ApplicationMark :class="$props.markClasses"/>
		<ApplicationTitle :class="$props.titleClasses"/>
	</div>
</template>
