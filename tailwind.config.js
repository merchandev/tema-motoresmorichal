/** @type {import('tailwindcss').Config} */
export default {
  content: {
    relative: true,
    files: [
      './*.php',
      './inc/**/*.php',
      './template-parts/**/*.php',
      './assets/js/**/*.js',
      './js/**/*.js',
      './src/**/*.{js,ts}',
    ],
  },
  theme: {
    extend: {
      colors: {
        toyota: {
          red: '#EB0A1E',
          black: '#0A0A0A',
          black2: '#111111',
        },
      },
    },
  },
  plugins: [],
}
