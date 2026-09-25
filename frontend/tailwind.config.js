/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#2D2926',
          50:  '#F5F3F1',
          100: '#EAE5E0',
          200: '#D4CBC2',
          300: '#BEB1A4',
          400: '#A89786',
          500: '#927D68',
          600: '#7A6454',
          700: '#5C4B3F',
          800: '#3E322A',
          900: '#2D2926',
          950: '#1A1815',
        },
        secondary: {
          DEFAULT: '#B68B5E',
          50:  '#FAF5EF',
          100: '#F4EADC',
          200: '#E9D5B9',
          300: '#DEC096',
          400: '#D3AB73',
          500: '#B68B5E',
          600: '#9A7048',
          700: '#7D5936',
          800: '#614325',
          900: '#452E17',
        },
        accent: {
          DEFAULT: '#C27D52',
          light: '#D4956E',
          dark:  '#A5673E',
        },
        background: '#F8F5F0',
        surface: '#FFFFFF',
        'text-main': '#24211F',
        muted: '#81766D',
        border: '#E8E0D8',
      },
      fontFamily: {
        display: ['"Cormorant Garamond"', 'Georgia', 'serif'],
        body:    ['Inter', 'system-ui', 'sans-serif'],
      },
      fontSize: {
        '2xs': ['0.65rem', { lineHeight: '1rem' }],
      },
      boxShadow: {
        'warm-sm': '0 1px 3px 0 rgba(45,41,38,0.08), 0 1px 2px 0 rgba(45,41,38,0.04)',
        'warm':    '0 4px 16px 0 rgba(45,41,38,0.10), 0 2px 6px 0 rgba(45,41,38,0.06)',
        'warm-lg': '0 10px 32px 0 rgba(45,41,38,0.14), 0 4px 10px 0 rgba(45,41,38,0.08)',
        'warm-xl': '0 24px 64px 0 rgba(45,41,38,0.16), 0 8px 20px 0 rgba(45,41,38,0.10)',
      },
      animation: {
        'fade-in':      'fadeIn 0.4s ease-out',
        'slide-up':     'slideUp 0.4s ease-out',
        'slide-right':  'slideRight 0.35s ease-out',
        'scale-in':     'scaleIn 0.3s ease-out',
      },
      keyframes: {
        fadeIn:    { from: { opacity: 0 },                          to: { opacity: 1 } },
        slideUp:   { from: { opacity: 0, transform: 'translateY(20px)' }, to: { opacity: 1, transform: 'translateY(0)' } },
        slideRight:{ from: { opacity: 0, transform: 'translateX(-20px)' },to: { opacity: 1, transform: 'translateX(0)' } },
        scaleIn:   { from: { opacity: 0, transform: 'scale(0.95)' },to: { opacity: 1, transform: 'scale(1)' } },
      },
      transitionTimingFunction: {
        'warm': 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
      },
      backgroundImage: {
        'warm-gradient': 'linear-gradient(135deg, #F8F5F0 0%, #EEE8E0 100%)',
        'hero-gradient': 'linear-gradient(to right, rgba(45,41,38,0.7) 0%, rgba(45,41,38,0.2) 60%, transparent 100%)',
        'card-gradient': 'linear-gradient(to top, rgba(45,41,38,0.8) 0%, transparent 60%)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
