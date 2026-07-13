/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./template-parts/**/*.php",
    "./assets/js/**/*.js",
    "./src/ts/**/*.ts"
  ],
  theme: {
    extend: {
      colors: {
        toyota: {
          red: "#EB0A1E",
          black: "#0A0A0A",
          black2: "#111111"
        }
      }
    },
  },
  plugins: [],
}

