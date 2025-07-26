// tailwind.config.js
import forms from '@tailwindcss/forms'

export default {
  content: ['./resources/**/*.{js,ts,vue,blade.php}'],
  theme: {
    extend: {},
  },
  plugins: [forms],
}
