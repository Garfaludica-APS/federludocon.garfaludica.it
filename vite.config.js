import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite';
import path from 'path';
import Components from 'unplugin-vue-components/vite';
import { HeadlessUiResolver } from 'unplugin-vue-components/resolvers';
import { UnpluginVueComponentsResolver } from 'maz-ui/resolvers';

export default defineConfig({
	plugins: [
		laravel({
			input: 'resources/js/app.js',
			ssr: 'resources/js/ssr.js',
			refresh: true,
		}),
		vue({
			template: {
				transformAssetUrls: {
					base: null,
					includeAbsolute: false,
				},
			},
		}),
		Components({
			resolvers: [
				UnpluginVueComponentsResolver(),
				HeadlessUiResolver(),
			],
			dts: true,
			dirs: ['resources/js/Components'],
		}),
		i18n(),
	],
	resolve: {
		alias: {
			'inertia-modal': path.resolve('vendor/emargareten/inertia-modal'),
		},
	},
});
