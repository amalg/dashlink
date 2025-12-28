<template>
	<Transition name="effect-blur">
		<div
			v-if="isHovered && link.description"
			class="effect-blur-overlay"
			:style="backgroundStyle">
			<div class="effect-blur-content">
				<p class="effect-blur-description">{{ link.description }}</p>
			</div>
		</div>
	</Transition>
</template>

<script>
import { defineComponent, computed } from 'vue'

export default defineComponent({
	name: 'EffectBlur',
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
.effect-blur-overlay {
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

.effect-blur-content {
	position: relative;
	z-index: 1;
	padding: 12px 16px;
	text-align: center;
}

.effect-blur-description {
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
.effect-blur-enter-active,
.effect-blur-leave-active {
	transition: opacity 0.5s ease-in-out;
}

.effect-blur-enter-from,
.effect-blur-leave-to {
	opacity: 0;
}
</style>
