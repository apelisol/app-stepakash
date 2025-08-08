/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./public/**/*.html",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#0f753a',
          50: '#f0f9f4',
          100: '#dcf2e4',
          200: '#bbe5cd',
          300: '#8dd2ac',
          400: '#58b885',
          500: '#0f753a',
          600: '#0d6632',
          700: '#0b5529',
          800: '#094420',
          900: '#07381b',
        },
        secondary: {
          DEFAULT: '#2c8a53',
          50: '#f1f9f4',
          100: '#def2e6',
          200: '#c0e5d1',
          300: '#93d1b0',
          400: '#5fb588',
          500: '#2c8a53',
          600: '#237643',
          700: '#1d6137',
          800: '#194e2e',
          900: '#154025',
        },
        accent: {
          DEFAULT: '#efd050',
          50: '#fefce8',
          100: '#fef9c3',
          200: '#fef08a',
          300: '#fde047',
          400: '#efd050',
          500: '#eab308',
          600: '#ca8a04',
          700: '#a16207',
          800: '#854d0e',
          900: '#713f12',
          hover: '#f4dc74',
        },
        text: {
          DEFAULT: '#333333',
          light: '#666666',
        },
        background: '#e8e8e8',
        'card-bg': '#ffffff',
        success: '#0f753a',
        danger: '#e74c3c',
        warning: '#f39c12',
      },
      fontFamily: {
        'inter': ['Inter', 'sans-serif'],
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '100': '25rem',
        '112': '28rem',
        '128': '32rem',
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
      },
      backdropBlur: {
        xs: '2px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}