import { createSSRApp, h } from 'vue';
import { renderToString } from '@vue/server-renderer';
import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { modal } from 'inertia-modal';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18nVue } from 'laravel-vue-i18n';

const appName = import.meta.env.VITE_APP_NAME || 'FederludoCon 2025 Garfagnana';
const orgName = import.meta.env.VITE_ORG_NAME || 'Garfaludica APS';

createServer((page) =>
	createInertiaApp({
		page,
		render: renderToString,
		title: (title) => title ? `${title} - ${appName} - ${orgName}` : `${appName} - ${orgName}`,
		resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
		setup({ App, props, plugin }) {
			return createSSRApp({ render: () => h(App, props) })
				.use(modal, {
					resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
				})
				.use(plugin)
				.use(ZiggyVue, {
					...page.props.ziggy,
					location: new URL(page.props.ziggy.location),
				})
				.use(i18nVue, {
					lang: 'en',
					resolve: lang => {
						const langs = import.meta.glob('../../lang/*.json', { eager: true });
						return langs[`../../lang/${lang}.json`].default;
					},
				});
		},
	}),
	import.meta.env.VITE_INERTIA_SSR_PORT || 13714
);
