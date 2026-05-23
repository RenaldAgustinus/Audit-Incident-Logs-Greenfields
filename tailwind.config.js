/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'gf-green': '#1B4D3E', // Warna custom Greenfields
      }
    },
  },
  plugins: [],
}