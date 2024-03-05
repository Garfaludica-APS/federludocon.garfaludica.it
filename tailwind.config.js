import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
	content: [
		'./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
		'./vendor/laravel/jetstream/**/*.blade.php',
		'./storage/framework/views/*.php',
		'./resources/views/**/*.blade.php',
		'./resources/js/**/*.vue',
	],

	darkMode: 'selector',

	theme: {
		screens: {
			'2xs': '410px',
			'xs': '475px',
			'sm': '640px',
			'md': '768px',
			'ml': '920px',
			'lg': '1085px',
			'xl': '1280px',
			'2xl': '1536px',
			'3xl': '1670px',
			'4xl': '1900px',
		},
		extend: {
			colors: {
				overlay: 'rgba(6, 12, 34, 0.8)',
				footer: '#040919',
				gray: {
					350: '#b6bbc1',
				}
			},
			animation: {
				'fade-in': 'fadeIn 0.5s ease-in-out',
			},
			keyframes: {
				fadeIn: {
					'0%': { opacity: '0' },
					'100%': { opacity: '1' },
				},
			},
			fontFamily: {
				sans: ['Figtree', ...defaultTheme.fontFamily.sans],
			},
			backgroundImage: {
				'gobcon-poster': "url('/storage/images/bg.jpg')",
			}
		},
	},

	plugins: [forms, typography],
};
