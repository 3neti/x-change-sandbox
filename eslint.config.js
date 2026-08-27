import stylistic from '@stylistic/eslint-plugin';
import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';
import prettier from 'eslint-config-prettier/flat';
import importPlugin from 'eslint-plugin-import';
import vue from 'eslint-plugin-vue';

const controlStatements = [
    'if',
    'return',
    'for',
    'while',
    'do',
    'switch',
    'try',
    'throw',
];
const paddingAroundControl = [
    ...controlStatements.flatMap((stmt) => [
        { blankLine: 'always', prev: '*', next: stmt },
        { blankLine: 'always', prev: stmt, next: '*' },
    ]),
];

export default defineConfigWithVueTs(
    vue.configs['flat/essential'],
    vueTsConfigs.recommended,
    {
        plugins: {
            import: importPlugin,
        },
        settings: {
            'import/resolver': {
                typescript: {
                    alwaysTryTypes: true,
                    project: './tsconfig.json',
                },
                node: true,
            },
        },
        rules: {
            'vue/multi-word-component-names': 'off',
            '@typescript-eslint/no-explicit-any': 'off',
            '@typescript-eslint/consistent-type-imports': [
                'error',
                {
                    prefer: 'type-imports',
                    fixStyle: 'separate-type-imports',
                },
            ],
            'import/order': [
                'error',
                {
                    groups: ['builtin', 'external', 'internal', 'parent', 'sibling', 'index'],
                    alphabetize: {
                        order: 'asc',
                        caseInsensitive: true,
                    },
                },
            ],
            'import/consistent-type-specifier-style': [
                'error',
                'prefer-top-level',
            ],
        },
    },
    {
        plugins: {
            '@stylistic': stylistic,
        },
        rules: {
            '@stylistic/brace-style': ['error', '1tbs', { allowSingleLine: false }],
            '@stylistic/padding-line-between-statements': [
                'error',
                ...paddingAroundControl,
            ],
        },
    },
    {
        ignores: [
            'vendor',
            'node_modules',
            'public',
            'bootstrap/ssr',
            'tailwind.config.js',
            'vite.config.ts',
            // Package-owned build projections are verified by x-change:doctor
            // and overwritten by x-change:publish. Their source packages own
            // lint and type correctness.
            'resources/js/cockpit/**',
            'resources/js/pages/x-change/**',
            'resources/js/components/x-change/**',
            'resources/js/components/x-rider/**',
            'resources/js/layouts/x-change/**',
            'resources/js/experience/**',
            'resources/js/pages/form-flow/**',
            'resources/js/pages/x-rider/**',
            'resources/js/vendor/x-ray/**',
            'resources/js/components/financial/**',
            'resources/js/components/ui/alert-dialog/**',
            'resources/js/components/ui/phone-input/**',
            'resources/js/components/ui/tabs/**',
            'resources/js/components/ui/textarea/**',
            'resources/js/config/**',
            'resources/js/data/**',
            'resources/js/components/NumberInputWithKeypad.vue',
            'resources/js/components/NumericKeypad.vue',
            'resources/js/layouts/PublicLayout.vue',
            'resources/js/composables/useClipboard.ts',
            'resources/js/composables/useFormFlowSummary.ts',
            'resources/js/composables/usePayCodeApi.ts',
            'resources/js/composables/usePayCodeCostEstimate.ts',
            'resources/js/composables/usePayCodeForm.ts',
            'resources/js/composables/useTheme.ts',
            'resources/js/composables/useVoucherPreview.ts',
            'resources/js/composables/useXChangeDashboardApi.ts',
            'resources/js/composables/useXChangeRoutes.ts',
            'resources/js/actions/**',
            'resources/js/components/ui/*',
            'resources/js/routes/**',
            'resources/js/wayfinder/**',
        ],
    },
    prettier, // Turn off all rules that might conflict with Prettier
    {
        plugins: {
            '@stylistic': stylistic,
        },
        rules: {
            curly: ['error', 'all'],
            '@stylistic/brace-style': ['error', '1tbs', { allowSingleLine: false }],
        },
    },
);
