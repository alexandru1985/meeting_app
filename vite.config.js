import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'node:path'
import autoprefixer from 'autoprefixer'
import laravel from 'laravel-vite-plugin';
export default defineConfig(() => {
  return {
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/main.js',
            ],
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
    ],
    base: './',
    css: {
      postcss: {
        plugins: [
          autoprefixer({}), // add options if needed
        ],
      },
      preprocessorOptions: {
        scss: {
          quietDeps: true,
        },
      },
    },
    resolve: {
      alias: [
        // webpack path resolve to vitejs
        {
          find: /^~(.*)$/,
          replacement: '$1',
        },
        {
          find: '@/',
          replacement: `${path.resolve(__dirname, 'resources/js')}/`,
        },
        {
          find: '@',
          replacement: path.resolve(__dirname, '/resources/js'),
        },
      ],
      extensions: ['.mjs', '.js', '.ts', '.jsx', '.tsx', '.json', '.vue', '.scss'],
    },
    server: {
      port: 3000,
      proxy: {
        // https://vitejs.dev/config/server-options.html
      },
    },
  }
})