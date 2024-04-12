import './bootstrap';
import '../css/app.css';

import { createSSRApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { modal } from 'inertia-modal';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18nVue } from 'laravel-vue-i18n';

const appName = import.meta.env.VITE_APP_NAME || 'GobCon 2024 Garfagnana';
const orgName = import.meta.env.VITE_ORG_NAME || 'Garfaludica APS';

createInertiaApp({
	title: (title) => title ? `${title} - ${appName} - ${orgName}` : `${appName} - ${orgName}`,
	resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
	setup({ el, App, props, plugin }) {
		return createSSRApp({ render: () => h(App, props) })
			.use(modal, {
				resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
			})
			.use(plugin)
			.use(ZiggyVue)
			.use(i18nVue, {
				resolve: async lang => {
					const langs = import.meta.glob('../../lang/*.json');
					return await langs[`../../lang/${lang}.json`]();
				}
			})
			.mount(el);
	},
	progress: {
		color: '#4F46E5',
	},
});
