import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/app.tsx',
      refresh: true,
    }),
  ],
  build: {
    // No data: URLs: each flag (Utilities/flagUrl.ts) and font file is a
    // separate file that the browser loads only when it needs it.
    assetsInlineLimit: 0,
  },
});
