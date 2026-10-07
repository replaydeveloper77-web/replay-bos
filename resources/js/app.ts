import './bootstrap';
import { createApp, h, type DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

const appEl = document.getElementById('app');
const isInertia = !!document.querySelector('[data-page]') || !!appEl?.dataset?.page;

if (isInertia) {
    createInertiaApp({
        resolve: (name: string) => {
            const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue', { eager: true });
            const page = pages[`./Pages/${name}.vue`];
            if (!page) {
                throw new Error(`Inertia page not found: ./Pages/${name}.vue`);
            }
            return page;
        },
        setup({ el, App, props, plugin }) {
            createApp({ render: () => h(App, props) })
                .use(plugin)
                .mount(el);
        },
    });
} else if (appEl) {
    // Legacy support for Blade views using <div id="app">
    const legacyApp = createApp({});
    const components = import.meta.glob<{ default: DefineComponent }>('./components/**/*.vue', { eager: true });
    Object.entries(components).forEach(([path, definition]) => {
        const componentName = path.split('/').pop()?.replace(/\.\w+$/, '');
        if (componentName && definition.default) {
            legacyApp.component(componentName, definition.default);
        }
    });
    legacyApp.mount('#app');
}
