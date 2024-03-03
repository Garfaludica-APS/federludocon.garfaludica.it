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

	theme: {
		extend: {
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
