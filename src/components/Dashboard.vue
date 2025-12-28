<template>
	<div class="dashlink-dashboard">
		<div v-if="displayLinks.length === 0" class="empty-state">
			<p>No links available</p>
		</div>
		<div
			v-else
			class="links-grid"
			:class="{ 'two-column': useTwoColumns }"
			:style="gridStyle">
			<LinkCard
				v-for="(link, index) in displayLinks"
				:key="link.id"
				:link="link"
				:effect="hoverEffect"
				:class="{ 'full-width': isFullWidth(index) }"
				:style="{ height: rowHeight + 'px' }" />
		</div>
	</div>
</template>

<script>
import { defineComponent, computed } from 'vue'
import LinkCard from './LinkCard.vue'

export default defineComponent({
	name: 'Dashboard',
	components: {
		LinkCard,
	},
	props: {
		initialLinks: {
			type: Array,
			default: () => [],
		},
		hoverEffect: {
			type: String,
			default: 'blur',
		},
	},
	setup(props) {
		const EFFECTIVE_HEIGHT = 400 // 504px - padding
		const MIN_ROW_HEIGHT = 70
		const MAX_LINKS = 10
		const GAP = 4 // Gap between grid items in pixels

		// Limit to 10 links
		const displayLinks = computed(() => {
			return props.initialLinks.slice(0, MAX_LINKS)
		})

		const linkCount = computed(() => displayLinks.value.length)

		// Determine if we should use 2-column layout
		const useTwoColumns = computed(() => {
			if (linkCount.value <= 5) return false
			const singleColumnHeight = EFFECTIVE_HEIGHT / linkCount.value
			return singleColumnHeight <= MIN_ROW_HEIGHT
		})

		// Calculate how many links should be full-width (span 2 columns)
		const fullWidthCount = computed(() => {
			if (!useTwoColumns.value) return 0

			const count = linkCount.value
			// For odd numbers: 1 full-width
			if (count % 2 === 1) return 1
			// For even numbers: (10 - count) full-width
			return Math.max(0, 10 - count)
		})

		// Calculate total number of rows
		const totalRows = computed(() => {
			if (!useTwoColumns.value) return linkCount.value

			const remaining = linkCount.value - fullWidthCount.value
			const twoColumnRows = Math.ceil(remaining / 2)
			return fullWidthCount.value + twoColumnRows
		})

		// Calculate height per row (accounting for gaps)
		const rowHeight = computed(() => {
			const totalGapHeight = (totalRows.value - 1) * GAP
			const availableHeight = EFFECTIVE_HEIGHT - totalGapHeight
			return Math.floor(availableHeight / totalRows.value)
		})

		// Grid style
		const gridStyle = computed(() => {
			if (!useTwoColumns.value) {
				return {
					gridTemplateColumns: '1fr',
					gap: GAP + 'px'
				}
			}
			return {
				gridTemplateColumns: 'repeat(2, 1fr)',
				gap: GAP + 'px'
			}
		})

		// Check if a link at given index should be full-width
		function isFullWidth(index) {
			return useTwoColumns.value && index < fullWidthCount.value
		}

		return {
			displayLinks,
			links: displayLinks,
			useTwoColumns,
			rowHeight,
			gridStyle,
			isFullWidth,
		}
	},
})
</script>

<style lang="scss" scoped>
.dashlink-dashboard {
	padding: 0;
	width: 288px;
	height: 400px;
}

.links-grid {
	display: grid;
	width: 100%;
	height: 100%;

	&.two-column {
		.full-width {
			grid-column: span 2;
		}
	}
}

.empty-state {
	text-align: center;
	padding: 40px 20px;
	color: var(--color-text-maxcontrast);
}
</style>
