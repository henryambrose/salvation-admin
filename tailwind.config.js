// tailwind.config.js
module.exports = {
  content: ['./resources/**/*.blade.php', './resources/**/*.js', './resources/**/*.vue', './resources/js/**/*.vue'],
  theme: {
    extend: {
      maxWidth: {
        md: '28rem',
      },
    },
  },
  plugins: [],
};
