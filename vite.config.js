import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    build: {
        chunkSizeWarningLimit: 3000, // tamaño en kB
    },
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/app-site.js",
                "resources/css/app-site.css",
                "resources/js/app-admin.js",
                "resources/css/app-admin.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
