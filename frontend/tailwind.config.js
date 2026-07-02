/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts}'],
  theme: {
    extend: {
      colors: {
        abyss:    '#0a0a0a',
        slate:    '#111111',
        parchment: {
          DEFAULT: '#e8e4d9',
          dark:    '#c4bfb0',
          muted:   '#9a9287',
        },
        crimson: {
          DEFAULT: '#8a0303',
          light:   '#b00404',
          dark:    '#5a0202',
          glow:    '#c00505',
        },
        rust: {
          DEFAULT: '#5c3d2e',
          light:   '#7a5240',
          dark:    '#3d2920',
        },
        gold: {
          DEFAULT: '#c8a951',
          light:   '#e0c06a',
          dark:    '#9a7c30',
          dim:     '#7a6030',
        },
        safe:   '#2d6b3c',
        danger: '#8a0303',
      },
      fontFamily: {
        gothic: ['"Cinzel Decorative"', 'serif'],
        cinzel: ['Cinzel', 'serif'],
        body:   ['"Crimson Text"', 'Georgia', 'serif'],
      },
      boxShadow: {
        'crimson-glow': '0 0 20px rgba(138, 3, 3, 0.6)',
        'gold-glow':    '0 0 12px rgba(200, 169, 81, 0.4)',
        'inner-dark':   'inset 0 2px 12px rgba(0, 0, 0, 0.9)',
        'panel':        '0 4px 24px rgba(0, 0, 0, 0.8)',
      },
      backgroundImage: {
        'vignette': 'radial-gradient(ellipse at center, transparent 40%, rgba(0,0,0,0.85) 100%)',
      },
      animation: {
        'flicker': 'flicker 3s infinite',
        'fade-in': 'fadeIn 0.4s ease-in',
        'slide-down': 'slideDown 0.3s ease-out',
      },
      keyframes: {
        flicker: {
          '0%, 100%': { opacity: '1' },
          '50%':      { opacity: '0.85' },
        },
        fadeIn: {
          from: { opacity: '0' },
          to:   { opacity: '1' },
        },
        slideDown: {
          from: { opacity: '0', transform: 'translateY(-8px)' },
          to:   { opacity: '1', transform: 'translateY(0)' },
        },
      },
    },
  },
  plugins: [],
}
