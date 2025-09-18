import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { ZiggyVue } from "ziggy-js";

createInertiaApp({
    title: (title) => `Starter Kit | ${title}`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', {
            eager: true
        });
        let page = pages[`./Pages/${name}.vue`];

        page.default.layout = page.default.layout || null;
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component("Head", Head)
            .component("Link", Link)
            .mount(el)
    },
})
