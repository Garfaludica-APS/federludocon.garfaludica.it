import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite';
import path from 'path';

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
		i18n(),
	],
	resolve: {
		alias: {
			'inertia-modal': path.resolve('vendor/emargareten/inertia-modal'),
		},
	},
});
