import './bootstrap';
import { createApp } from 'vue';

const app = createApp({});

// Automatically register all Vue components inside ./components/
const components = import.meta.glob('./components/**/*.vue', { eager: true });
Object.entries(components).forEach(([path, definition]) => {
    const componentName = path.split('/').pop().replace(/\.\w+$/, '');
    app.component(componentName, definition.default);
});

// Mount to #app if available on the page
if (document.getElementById('app')) {
    app.mount('#app');
}
