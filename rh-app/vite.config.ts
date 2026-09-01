import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  base: '/rh/',
  plugins: [react()],
  define: {
    global: 'globalThis',
    'global.Buffer': ['buffer', 'Buffer'],
  },
  resolve: {
    alias: {
      buffer: 'buffer',
    },
  },
})
