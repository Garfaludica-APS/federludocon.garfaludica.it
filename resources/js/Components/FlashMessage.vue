<script setup>
</script>

<template>
	<div v-if="$page.props.flash.message" :class="{'text-green-500': $page.props.flash.style === 'success', 'text-amber-500': $page.props.flash.style === 'warning', 'text-red-500': $page.props.flash.style === 'error', 'text-indigo-500': $page.props.flash.style === 'info', 'text-gray-900 dark:text-white': $page.props.flash.style === 'default'}">
		{{ $page.props.flash.message }}
	</div>
</template>
