<script>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { trans } from 'laravel-vue-i18n';

export default {
	layout: (h, page) => h(BaseLayout, { title: 'Edit Hotel Presentation Page' }, () => h(AdminLayout, {}, {
		default: () => page,
		header: () => h('h1', { class: 'font-extrabold, text-3xl' }, trans('Hotel\'s Presentation Page')),
	})),
}
</script>

<script setup>
import { onBeforeMount } from 'vue';
import { useForm } from '@inertiajs/vue3';
import TextArea from '@/Components/TextArea.vue';
import ActionButton from '@/Components/ActionButton.vue';
import { TabGroup, Tab, TabList, TabPanels, TabPanel } from '@headlessui/vue';

const props = defineProps({
	hotel: Object,
	locales: Array,
	presentation: Object,
	startTabIndex: Number,
});

const form = useForm({
	presentation: {},
});

const updatePresentation = () => {
	form.patch(route('admin.hotel.presentation.update', { hotel: props.hotel }));
};

onBeforeMount(() => {
	form.presentation = props.presentation;
});
</script>

<template>
	<div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl overflow-hidden p-8">
		<h2 class="font-bold text-xl">{{ $t(':hotel - Presentation Page', { hotel: hotel.name }) }}</h2>
		<form class="dark:text-white py-2 mt-4" @submit.prevent="updatePresentation">
			<TabGroup :defaultIndex="startTabIndex">
				<TabList class="flex space-x-1 rounded-xl bg-blue-900/20 h-12 p-1 w-fit">
					<Tab
						v-for="locale in locales" :key="locale"
						as="template"
						v-slot="{ selected }"
					>
						<button
							:class="[
								'px-4 rounded-lg py-2.5 font-bold leading-5',
								'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
								selected
									? 'bg-white dark:bg-gray-900 text-blue-700 dark:text-blue-100 shadow'
									: 'text-blue-700 dark:text-blue-100 hover:bg-white/[0.12] dark:hover:bg-black/[0.12] hover:text-blue-800 dark:hover:text-white'
							]"
						>
							{{ $t('locale_name_' + locale) }}
						</button>
					</Tab>
				</TabList>
				<TabPanels>
					<TabPanel v-for="locale in locales" :key="locale">
						<div class="flex flex-col mt-4 items-center justify-center">
							<TextArea
									v-model="form.presentation[locale]"
									:error="form.errors.presentation"
									class="mt-1 w-full min-h-20 max-h-[70vh]"
									:placeholder="$t('Write a presentation for your hotel - you can use HTML')"
									autofocus
							>
							</TextArea>
							<ActionButton :disabled="form.processing" class="mt-6 mx-auto px-8 py-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 focus:ring-indigo-500 disabled:bg-indigo-600/50 disabled:text-gray-100 ml-auto" classes="text-xl rounded-md font-bold" type="submit">
								{{ $t('Save') }}
							</ActionButton>
						</div>
					</TabPanel>
				</TabPanels>
			</TabGroup>
		</form>
	</div>
</template>
