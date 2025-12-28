# Effect System - Implementation Details

This document contains detailed code examples for the modular hover effects system in DashLink v1.0.0.

## Overview

The effect system is a plugin-based architecture that allows easy addition of new hover effects without modifying core code:
- **3 Built-in Effects**: Blur Overlay, 3D Card Flip, Slide Panel
- **Modular Structure**: Each effect in its own folder with metadata
- **Dynamic Loading**: Effects loaded on-demand via `getEffectComponent()`
- **Fallback System**: Unknown effects automatically fall back to 'blur'
- **Extensible**: Add new effects by creating a folder and registering

## Effect Registry (src/effects/index.js)

```javascript
/**
 * DashLink Effect Registry
 *
 * Central registry for all hover effects.
 * To add a new effect:
 * 1. Create a new folder: effect_yourname/
 * 2. Create index.js with metadata and EffectYourname.vue
 * 3. Import and register here
 */

import EffectBlur from './effect_blur'
import EffectFlip from './effect_flip'
import EffectSlide from './effect_slide'

// Effect registry - add new effects here
const effects = {
    blur: EffectBlur,
    flip: EffectFlip,
    slide: EffectSlide,
}

/**
 * Get all available effects for dropdown
 * @returns {Array<{id: string, name: string, description: string}>}
 */
export function getAvailableEffects() {
    return Object.entries(effects).map(([id, effect]) => ({
        id,
        name: effect.name,
        description: effect.description,
    }))
}

/**
 * Get effect component by ID
 * @param {string} effectId - Effect identifier
 * @returns {Component} Vue component
 */
export function getEffectComponent(effectId) {
    const effect = effects[effectId]
    if (!effect) {
        console.warn(`Unknown effect: ${effectId}, falling back to blur`)
        return effects.blur.component
    }
    return effect.component
}

/**
 * Get effect metadata by ID
 * @param {string} effectId
 * @returns {Object} Effect metadata
 */
export function getEffect(effectId) {
    return effects[effectId] || effects.blur
}

export default effects
```

## Effect 1: Blur Overlay (`effect_blur`)

**Description**: The logo becomes a blurred background image while the description fades in with a dark overlay.

```javascript
// src/effects/effect_blur/index.js
import EffectBlur from './EffectBlur.vue'

export default {
    id: 'blur',
    name: 'Blur Overlay',
    description: 'Description appears over a blurred logo background',
    component: EffectBlur,
}
```

```vue
<!-- src/effects/effect_blur/EffectBlur.vue -->
<template>
    <Transition name="effect-blur">
        <div
            v-if="isHovered && link.description"
            class="effect-blur-overlay"
            :style="backgroundStyle"
        >
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
            required: true
        },
        isHovered: {
            type: Boolean,
            default: false
        }
    },
    setup(props) {
        const backgroundStyle = computed(() => {
            if (props.link.iconUrl) {
                return {
                    backgroundImage: `url(${props.link.iconUrl})`
                }
            }
            return {}
        })

        return { backgroundStyle }
    }
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

    &::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(0, 0, 0, 0.85) 0%,
            rgba(0, 0, 0, 0.75) 100%
        );
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
    color: #ffffff;
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
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.effect-blur-enter-from,
.effect-blur-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>
```

## Effect 2: 3D Card Flip (`effect_flip`)

**Description**: The entire card flips 180° to reveal the description on the back side.

```javascript
// src/effects/effect_flip/index.js
import EffectFlip from './EffectFlip.vue'

export default {
    id: 'flip',
    name: '3D Card Flip',
    description: 'Card flips to reveal description on the back',
    component: EffectFlip,
}
```

```vue
<!-- src/effects/effect_flip/EffectFlip.vue -->
<template>
    <div class="effect-flip-container" :class="{ 'is-flipped': isHovered }">
        <!-- Front Side (default card content is handled by LinkCard) -->
        <div class="effect-flip-front">
            <slot name="front" />
        </div>

        <!-- Back Side -->
        <div class="effect-flip-back">
            <div class="flip-back-content">
                <div v-if="link.iconUrl" class="flip-back-icon">
                    <img :src="link.iconUrl" :alt="link.title" />
                </div>
                <h4 class="flip-back-title">{{ link.title }}</h4>
                <p v-if="link.description" class="flip-back-description">
                    {{ link.description }}
                </p>
                <span class="flip-back-hint">
                    {{ t('dashlink', 'Click to open') }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'EffectFlip',
    props: {
        link: {
            type: Object,
            required: true
        },
        isHovered: {
            type: Boolean,
            default: false
        }
    }
})
</script>

<style lang="scss" scoped>
.effect-flip-container {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);

    &.is-flipped {
        transform: rotateY(180deg);
    }
}

.effect-flip-front,
.effect-flip-back {
    position: absolute;
    inset: 0;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    border-radius: inherit;
}

.effect-flip-front {
    z-index: 2;
}

.effect-flip-back {
    transform: rotateY(180deg);
    background: linear-gradient(
        135deg,
        var(--color-primary-element) 0%,
        var(--color-primary-element-light) 100%
    );
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.flip-back-content {
    text-align: center;
    color: #ffffff;
}

.flip-back-icon {
    width: 40px;
    height: 40px;
    margin: 0 auto 8px;

    img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: var(--border-radius);
        background: rgba(255, 255, 255, 0.2);
        padding: 4px;
    }
}

.flip-back-title {
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.flip-back-description {
    font-size: 12px;
    line-height: 1.4;
    margin: 0 0 8px 0;
    opacity: 0.9;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.flip-back-hint {
    font-size: 11px;
    opacity: 0.7;
}
</style>
```

## Effect 3: Slide Panel (`effect_slide`)

**Description**: A panel slides up from the bottom revealing the description with a gradient background.

```javascript
// src/effects/effect_slide/index.js
import EffectSlide from './EffectSlide.vue'

export default {
    id: 'slide',
    name: 'Slide Panel',
    description: 'Description panel slides up from the bottom',
    component: EffectSlide,
}
```

```vue
<!-- src/effects/effect_slide/EffectSlide.vue -->
<template>
    <Transition name="effect-slide">
        <div
            v-if="isHovered && link.description"
            class="effect-slide-panel"
        >
            <div class="effect-slide-content">
                <div class="slide-header">
                    <img
                        v-if="link.iconUrl"
                        :src="link.iconUrl"
                        :alt="link.title"
                        class="slide-icon"
                    />
                    <span class="slide-title">{{ link.title }}</span>
                </div>
                <p class="slide-description">{{ link.description }}</p>
            </div>
        </div>
    </Transition>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'EffectSlide',
    props: {
        link: {
            type: Object,
            required: true
        },
        isHovered: {
            type: Boolean,
            default: false
        }
    }
})
</script>

<style lang="scss" scoped>
.effect-slide-panel {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(
        to top,
        rgba(0, 0, 0, 0.95) 0%,
        rgba(0, 0, 0, 0.8) 70%,
        transparent 100%
    );
    border-radius: 0 0 inherit inherit;
    padding: 32px 16px 12px;
}

.effect-slide-content {
    color: #ffffff;
}

.slide-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.slide-icon {
    width: 20px;
    height: 20px;
    object-fit: contain;
    border-radius: 4px;
}

.slide-title {
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.slide-description {
    font-size: 12px;
    line-height: 1.4;
    margin: 0;
    opacity: 0.9;
    display: -webkit-box;
    -webkit-line-clamp: 2;
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
```

## Adding New Effects

To add a new hover effect, follow these steps:

### Step 1: Create Effect Folder

```bash
mkdir src/effects/effect_yourname
```

### Step 2: Create Effect Component

```vue
<!-- src/effects/effect_yourname/EffectYourname.vue -->
<template>
    <Transition name="effect-yourname">
        <div v-if="isHovered && link.description" class="effect-yourname">
            <!-- Your effect implementation -->
            <p>{{ link.description }}</p>
        </div>
    </Transition>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'EffectYourname',
    props: {
        link: {
            type: Object,
            required: true
        },
        isHovered: {
            type: Boolean,
            default: false
        }
    }
})
</script>

<style lang="scss" scoped>
.effect-yourname {
    /* Your styles */
}

.effect-yourname-enter-active,
.effect-yourname-leave-active {
    transition: all 0.3s ease;
}

.effect-yourname-enter-from,
.effect-yourname-leave-to {
    opacity: 0;
}
</style>
```

### Step 3: Create Effect Index

```javascript
// src/effects/effect_yourname/index.js
import EffectYourname from './EffectYourname.vue'

export default {
    id: 'yourname',
    name: 'Your Effect Name',           // Displayed in dropdown
    description: 'What this effect does', // Shown as hint
    component: EffectYourname,
}
```

### Step 4: Register in Effect Registry

```javascript
// src/effects/index.js
import EffectBlur from './effect_blur'
import EffectFlip from './effect_flip'
import EffectSlide from './effect_slide'
import EffectYourname from './effect_yourname'  // Add import

const effects = {
    blur: EffectBlur,
    flip: EffectFlip,
    slide: EffectSlide,
    yourname: EffectYourname,  // Add to registry
}
```

### Step 5: Update Backend SettingsService

The backend must also know about the new effect for validation:

```php
// lib/Service/SettingsService.php
private const AVAILABLE_EFFECTS = [
    'blur' => [
        'id' => 'blur',
        'name' => 'Blur Overlay',
        'description' => 'Description appears over a blurred logo background'
    ],
    'flip' => [
        'id' => 'flip',
        'name' => '3D Card Flip',
        'description' => 'Card flips to reveal description on the back'
    ],
    'slide' => [
        'id' => 'slide',
        'name' => 'Slide Panel',
        'description' => 'Description panel slides up from the bottom'
    ],
    'yourname' => [  // Add your effect here
        'id' => 'yourname',
        'name' => 'Your Effect Name',
        'description' => 'What this effect does'
    ],
];
```

That's it! The new effect will automatically appear in:
- Admin panel effect dropdown
- Live preview
- Dashboard widget
- Settings validation

## Backend-Frontend Synchronization

The effect system maintains synchronization between frontend and backend:

### Frontend (src/effects/index.js)
```javascript
const effects = {
    blur: EffectBlur,
    flip: EffectFlip,
    slide: EffectSlide,
}
```

### Backend (lib/Service/SettingsService.php)
```php
private const AVAILABLE_EFFECTS = [
    'blur' => [...],
    'flip' => [...],
    'slide' => [...],
];
```

### Fallback Mechanism

If an invalid effect ID is stored in settings:
1. Backend validates against `AVAILABLE_EFFECTS` in `setHoverEffect()`
2. Frontend `getEffectComponent()` falls back to 'blur' if effect not found
3. Widget always renders with a valid effect component

Example:
```javascript
export function getEffectComponent(effectId) {
    const effect = effects[effectId]
    if (!effect) {
        console.warn(`Unknown effect: ${effectId}, falling back to blur`)
        return effects.blur.component  // Guaranteed to exist
    }
    return effect.component
}
```

## Effect Component Interface

All effects must adhere to this interface:

### Required Props
```javascript
props: {
    link: {
        type: Object,
        required: true,
        // Contains: id, title, url, description, iconUrl, target
    },
    isHovered: {
        type: Boolean,
        default: false
    }
}
```

### Recommended Structure
- Use Vue `<Transition>` for smooth animations
- Show content only when `isHovered && link.description`
- Use Nextcloud CSS variables for theming
- Support dark mode automatically via CSS variables
- Keep animations under 600ms for good UX
- Ensure text remains readable (contrast ratios)

## Performance Considerations

1. **Lazy Loading**: Effects loaded dynamically, not all at once
2. **Component Caching**: Vue caches effect components after first load
3. **Minimal DOM**: Effects render only when hovered
4. **CSS Transforms**: Use transform/opacity for smooth 60fps animations
5. **Transitions**: Vue Transition provides optimized enter/leave animations

## Testing New Effects

When adding a new effect, test:
1. **Hover behavior**: Effect appears smoothly on hover
2. **Dark mode**: Effect looks good in both light and dark themes
3. **No description**: Effect doesn't show when description is empty
4. **Long text**: Description truncates gracefully
5. **Responsive**: Effect works on mobile and tablet viewports
6. **Accessibility**: Focus states work with keyboard navigation
