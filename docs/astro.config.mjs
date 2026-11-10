// @ts-check
import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';

const SITE = 'https://nenuadrian.github.io';
const BASE = '/secret-republic-browser-based-cyber-security-rpg-v3';
const REPO = 'https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3';

export default defineConfig({
	site: SITE,
	base: BASE,
	trailingSlash: 'always',
	vite: {
		// Screenshots live in the repository-level screens/ folder.
		server: { fs: { allow: ['..'] } },
	},
	integrations: [
		starlight({
			title: 'Secret Republic V3',
			description:
				'Secret Republic V3 is a free, open source browser-based cyber security RPG with missions, servers, organizations, a world map and an in-game mission designer. PHP 8.5 with MySQL or SQLite, self-hosted with Docker in minutes.',
			logo: { src: './src/assets/logo.png', alt: 'Secret Republic' },
			favicon: '/favicon.png',
			titleDelimiter: '·',
			social: [{ icon: 'github', label: 'GitHub', href: REPO }],
			editLink: { baseUrl: `${REPO}/edit/master/docs/` },
			lastUpdated: true,
			customCss: [
				'@fontsource/josefin-sans/400.css',
				'@fontsource/josefin-sans/600.css',
				'@fontsource/josefin-sans/700.css',
				'@fontsource-variable/jetbrains-mono',
				'./src/styles/theme.css',
			],
			expressiveCode: {
				themes: ['github-dark-default'],
				styleOverrides: {
					borderRadius: '6px',
					borderColor: 'rgba(32, 158, 255, 0.25)',
					codeFontFamily: "'JetBrains Mono Variable', ui-monospace, monospace",
					frames: { shadowColor: 'rgba(32, 158, 255, 0.12)' },
				},
			},
			components: {
				Head: './src/components/Head.astro',
				ThemeProvider: './src/components/ThemeProvider.astro',
				ThemeSelect: './src/components/ThemeSelect.astro',
			},
			head: [
				{ tag: 'meta', attrs: { name: 'theme-color', content: '#05070d' } },
				{ tag: 'meta', attrs: { name: 'color-scheme', content: 'dark' } },
				{ tag: 'link', attrs: { rel: 'apple-touch-icon', href: `${BASE}/apple-touch-icon.png` } },
			],
			sidebar: [
				{
					label: 'Getting started',
					items: [
						{ slug: 'getting-started/introduction' },
						{ slug: 'getting-started/quick-start' },
						{ slug: 'getting-started/manual-install' },
						{ slug: 'getting-started/configuration' },
						{ slug: 'getting-started/cron-jobs' },
					],
				},
				{
					label: 'Playing',
					items: [
						{ slug: 'game/features' },
						{ slug: 'game/missions' },
						{ slug: 'game/screenshots' },
					],
				},
				{
					label: 'Running a server',
					items: [
						{ slug: 'admin/admin-panel' },
						{ slug: 'admin/mission-designer' },
						{ slug: 'admin/deployment' },
						{ slug: 'admin/troubleshooting' },
					],
				},
				{
					label: 'Development',
					items: [
						{ slug: 'development/architecture' },
						{ slug: 'development/game-content' },
						{ slug: 'development/testing' },
						{ slug: 'development/contributing' },
					],
				},
				{
					label: 'About',
					items: [{ slug: 'about/history' }],
				},
			],
		}),
	],
});
