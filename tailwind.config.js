import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/**
 * 主題配色（專業藍 + slate 冷灰）
 * - 直接覆寫 `gray` 與 `indigo`，既有 Blade 中的 class 不需修改即可套用新配色，
 *   避免大量改動視圖而影響功能。
 * - 新程式碼可改用語意化的 `brand-*`（與 indigo 相同色票）。
 */
const brand = {
    50:  '#eff6ff',
    100: '#dbeafe',
    200: '#bfdbfe',
    300: '#93c5fd',
    400: '#5b9cf5',
    500: '#2f74e0',
    600: '#1d5fd0',
    700: '#184cad',
    800: '#173f8a',
    900: '#16326b',
    950: '#0f2048',
};

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                gray: colors.slate,
                indigo: brand,
                brand: brand,
            },
            fontFamily: {
                sans: ['Figtree', '"Noto Sans TC"', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                card: '0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.06)',
            },
        },
    },

    plugins: [forms, typography],
};
