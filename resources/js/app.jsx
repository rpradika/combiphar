import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

// Lazy glob = code splitting: each page becomes its own chunk, so the initial
// load only downloads the current page (plus shared chunks) instead of the
// whole site. The SSR entry (ssr.jsx) stays eager on purpose — the daemon
// loads once at boot and eager resolution keeps renders synchronous.
const pages = import.meta.glob('./Pages/**/*.jsx');

createInertiaApp({
    resolve: (name) => pages[`./Pages/${name}.jsx`](),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});