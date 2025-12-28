<template>
	<div class="effect-flip-container" :class="{ 'is-flipped': isHovered }">
		<!-- Front Side (default card content is handled by LinkCard) -->
		<div class="effect-flip-front">
			<slot name="front" />
		</div>

		<!-- Back Side -->
		<div class="effect-flip-back" :style="backgroundStyle">
			<div class="flip-back-content">
				<p v-if="link.description" class="effect-flip-description">
					{{ link.description }}
				</p>
			</div>
		</div>
	</div>
</template>

<script>
import { defineComponent, computed } from 'vue'

export default defineComponent({
	name: 'EffectFlip',
	props: {
		link: {
			type: Object,
			required: true,
		},
		isHovered: {
			type: Boolean,
			default: false,
		},
	},
	setup(props) {
		const backgroundStyle = computed(() => {
			if (props.link.iconUrl) {
				return {
					backgroundImage: `url(${props.link.iconUrl})`,
				}
			}
			return {}
		})

		return { backgroundStyle }
	},
})
</script>

<style lang="scss" scoped>
.effect-flip-container {
	position: absolute;
	inset: 0;
	width: 100%;
	height: 100%;
	transform-style: preserve-3d;
	transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
	z-index: 10;
	border-radius: inherit;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

	&.is-flipped {
		transform: rotateY(180deg);
		box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
	}
}

.effect-flip-front,
.effect-flip-back {
	position: absolute;
	inset: 0;
	backface-visibility: hidden;
	-webkit-backface-visibility: hidden;
	-moz-backface-visibility: hidden;
	border-radius: inherit;
	overflow: hidden;
}

.effect-flip-front {
	z-index: 2;
	background: transparent;
	display: flex;
	align-items: stretch;
	transform: rotateY(0deg);
}

.effect-flip-back {
	transform: rotateY(180deg);
	z-index: 1;
	background: transparent;
	background-size: cover;
	background-position: center;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 16px;

	&::before {
		content: '';
		position: absolute;
		inset: 0;
		background: linear-gradient(
			135deg,
			var(--color-main-background) 0%,
			var(--color-background-hover) 100%
		);
		opacity: 0.95;
		backdrop-filter: blur(8px);
		-webkit-backdrop-filter: blur(8px);
		border-radius: inherit;
	}
}

// Firefox-specific fix: ensure back face is on top when flipped
.effect-flip-container.is-flipped .effect-flip-back {
	z-index: 3;
}

.effect-flip-container.is-flipped .effect-flip-front {
	z-index: 1;
}

.flip-back-content {
	position: relative;
	z-index: 1;
	padding: 12px 16px;
	text-align: center;
}

.effect-flip-description {
	color: var(--color-main-text);
	font-size: 13px;
	line-height: 1.4;
	margin: 0;
	display: -webkit-box;
	-webkit-line-clamp: 3;
	-webkit-box-orient: vertical;
	overflow: hidden;
}
</style>
