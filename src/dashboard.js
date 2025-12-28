import { loadState } from '@nextcloud/initial-state'
import { translate, translatePlural } from '@nextcloud/l10n'
import { createApp } from 'vue'
import Dashboard from './components/Dashboard.vue'
import '../css/dashboard.scss'

document.addEventListener('DOMContentLoaded', () => {
	OCA.Dashboard.register('dashlink', (el, { widget }) => {
		const initialLinks = loadState('dashlink', 'links', [])
		const hoverEffect = loadState('dashlink', 'hoverEffect', 'blur')

		const app = createApp(Dashboard, {
			initialLinks,
			hoverEffect,
		})

		// Add translation methods globally
		app.mixin({
			methods: {
				t: translate,
				n: translatePlural,
			},
		})

		app.mount(el)
	})
})
