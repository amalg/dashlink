/**
 * Simple notification utilities using Nextcloud's global OC API
 * Replaces @nextcloud/dialogs to avoid dependency issues
 */

export function showSuccess(message) {
	if (window.OC && window.OC.Notification) {
		window.OC.Notification.showTemporary(message, { type: 'success' })
	} else {
		console.log('[SUCCESS]', message)
	}
}

export function showError(message) {
	if (window.OC && window.OC.Notification) {
		window.OC.Notification.showTemporary(message, { type: 'error' })
	} else {
		console.error('[ERROR]', message)
	}
}

export function showWarning(message) {
	if (window.OC && window.OC.Notification) {
		window.OC.Notification.showTemporary(message, { type: 'warning' })
	} else {
		console.warn('[WARNING]', message)
	}
}

export function showInfo(message) {
	if (window.OC && window.OC.Notification) {
		window.OC.Notification.showTemporary(message, { type: 'info' })
	} else {
		console.info('[INFO]', message)
	}
}
