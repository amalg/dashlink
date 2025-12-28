import { translate, translatePlural } from '@nextcloud/l10n'
import { createApp } from 'vue'
import AdminPanel from './components/AdminPanel.vue'
import '../css/admin.scss'

const app = createApp(AdminPanel)

// Add translation methods globally
app.mixin({
	methods: {
		t: translate,
		n: translatePlural,
	},
})

app.mount('#dashlink-admin-root')
