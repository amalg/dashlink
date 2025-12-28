import { ref } from 'vue'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'

export function useGroups() {
	const groups = ref([])
	const loading = ref(false)

	async function fetchGroups() {
		loading.value = true
		try {
			const response = await axios.get(generateUrl('/apps/dashlink/api/v1/admin/groups'))
			groups.value = response.data
		} catch (error) {
			console.error('Failed to fetch groups:', error)
			throw error
		} finally {
			loading.value = false
		}
	}

	return {
		groups,
		loading,
		fetchGroups,
	}
}
