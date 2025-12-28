<template>
	<Transition name="effect-slide">
		<div
			v-if="isHovered && link.description"
			class="effect-slide-panel"
			:style="backgroundStyle">
			<div class="effect-slide-content">
				<p class="effect-slide-description">{{ link.description }}</p>
			</div>
		</div>
	</Transition>
</template>

<script>
import { defineComponent, computed } from 'vue'

export default defineComponent({
	name: 'EffectSlide',
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
.effect-slide-panel {
	position: absolute;
	inset: 0;
	background-size: cover;
	background-position: center;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: inherit;
	z-index: 10;

	&::before {
		content: '';
		position: absolute;
		inset: 0;
		background: linear-gradient(
			135deg,
			var(--color-main-background) 0%,
			var(--color-background-hover) 100%
		);
		opacity: 0.85;
		backdrop-filter: blur(8px);
		-webkit-backdrop-filter: blur(8px);
		border-radius: inherit;
	}
}

.effect-slide-content {
	position: relative;
	z-index: 1;
	padding: 12px 16px;
	text-align: center;
}

.effect-slide-description {
	color: var(--color-main-text);
	font-size: 13px;
	line-height: 1.4;
	margin: 0;
	display: -webkit-box;
	-webkit-line-clamp: 3;
	-webkit-box-orient: vertical;
	overflow: hidden;
}

// Animation
.effect-slide-enter-active,
.effect-slide-leave-active {
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.effect-slide-enter-from,
.effect-slide-leave-to {
	opacity: 0;
	transform: translateY(100%);
}

.effect-slide-enter-to,
.effect-slide-leave-from {
	opacity: 1;
	transform: translateY(0);
}
</style>
