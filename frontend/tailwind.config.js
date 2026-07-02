/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        background: 'rgb(var(--bg-background) / <alpha-value>)',
        surface: 'rgb(var(--bg-surface) / <alpha-value>)',
        'surface-elevated': 'rgb(var(--bg-surface-elevated) / <alpha-value>)',
        content: 'rgb(var(--text-content) / <alpha-value>)',
        muted: 'rgb(var(--text-muted) / <alpha-value>)',
        inverse: 'rgb(var(--text-inverse) / <alpha-value>)',
        subtle: 'rgb(var(--border-subtle) / <alpha-value>)',
        primary: 'rgb(var(--color-primary-rgb) / <alpha-value>)',
        secondary: 'rgb(var(--color-secondary-rgb) / <alpha-value>)',
        accent: 'rgb(var(--color-accent-rgb) / <alpha-value>)',
        success: 'rgb(var(--color-success-rgb) / <alpha-value>)',
        warning: 'rgb(var(--color-warning-rgb) / <alpha-value>)',
        danger: 'rgb(var(--color-danger-rgb) / <alpha-value>)',
        info: 'rgb(var(--color-info-rgb) / <alpha-value>)',
      },
      fontFamily: {
        brand: ['var(--font-brand)', 'sans-serif'],
        body: ['var(--font-body)', 'sans-serif'],
        mono: ['var(--font-mono)', 'monospace'],
      },
      borderRadius: {
        none: 'var(--radius-none)',
        sm: 'var(--radius-sm)',
        DEFAULT: 'var(--radius-md)',
        md: 'var(--radius-md)',
        lg: 'var(--radius-lg)',
        full: 'var(--radius-full)',
      },
      boxShadow: {
        base: 'var(--elevation-base)',
        raised: 'var(--elevation-raised)',
        overlay: 'var(--elevation-overlay)',
        modal: 'var(--elevation-modal)',
      },
      transitionDuration: {
        fast: 'var(--transition-fast)',
        normal: 'var(--transition-normal)',
        slow: 'var(--transition-slow)',
      },
      transitionTimingFunction: {
        standard: 'var(--ease-standard)',
        bounce: 'var(--ease-bounce)',
      }
    },
  },
  plugins: [],
}
