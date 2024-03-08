import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { i18nVue } from 'laravel-vue-i18n';

const appName = import.meta.env.VITE_APP_NAME || 'GobCon 2024 Garfagnana';
const orgName = import.meta.env.VITE_ORG_NAME || 'Garfaludica APS';

createInertiaApp({
	title: (title) => title ? `${title} - ${appName} - ${orgName}` : `${appName} - ${orgName}`,
	resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
	setup({ el, App, props, plugin }) {
		return createApp({ render: () => h(App, props) })
			.use(plugin)
			.use(ZiggyVue)
			.mixin({
				methods: {
					lroute: function (name, params, absolute, config)
					{
						if (name && Ziggy.routeNamePrefix !== undefined)
							name = Ziggy.routeNamePrefix + name;
						return route(name, params, absolute, config);
					},
					currentRoute: function (name)
					{
						var r = route().current();
						if (r.startsWith('en.'))
							r = r.substr(3);
						if (!name)
							return r;
						return r === name;

					},
				},
			})
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
