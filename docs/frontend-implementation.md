# Frontend Implementation - Code Examples

This document contains detailed Vue.js frontend implementation examples for DashLink v1.0.0.

## dashboard.js (Entry Point)

**Vue 3 with Composition API and @nextcloud/vue v9.x:**

```javascript
import { loadState } from '@nextcloud/initial-state'
import { createApp } from 'vue'
import Dashboard from './components/Dashboard.vue'

document.addEventListener('DOMContentLoaded', () => {
    OCA.Dashboard.register('dashlink', (el) => {
        const initialLinks = loadState('dashlink', 'links', [])
        const hoverEffect = loadState('dashlink', 'hoverEffect', 'blur')

        const app = createApp(Dashboard, {
            initialLinks,
            hoverEffect
        })

        app.mount(el)
    })
})
```

## admin.js (Entry Point)

```javascript
import { createApp } from 'vue'
import AdminPanel from './components/AdminPanel.vue'

const app = createApp(AdminPanel)
app.mount('#dashlink-admin-panel')
```

## LinkCard.vue

```vue
<template>
    <a
        :href="link.url"
        :target="link.target"
        class="link-card"
        :class="[`effect-${effect}`]"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <!-- Card Content -->
        <div class="card-content">
            <div class="icon-wrapper">
                <img
                    v-if="link.iconUrl"
                    :src="link.iconUrl"
                    :alt="link.title"
                    class="link-icon"
                />
                <div v-else class="icon-placeholder">
                    <LinkIcon :size="24" />
                </div>
            </div>
            <span class="link-title">{{ link.title }}</span>
            <ExternalLinkIcon
                v-if="link.target === '_blank'"
                :size="14"
                class="external-indicator"
            />
        </div>

        <!-- Dynamic Effect Component -->
        <component
            :is="effectComponent"
            :link="link"
            :is-hovered="isHovered"
        />
    </a>
</template>

<script>
import { defineComponent, ref, computed } from 'vue'
import LinkIcon from 'vue-material-design-icons/Link.vue'
import ExternalLinkIcon from 'vue-material-design-icons/OpenInNew.vue'
import { getEffectComponent } from '../effects'

export default defineComponent({
    name: 'LinkCard',
    components: {
        LinkIcon,
        ExternalLinkIcon
    },
    props: {
        link: {
            type: Object,
            required: true
        },
        effect: {
            type: String,
            default: 'blur'
        }
    },
    setup(props) {
        const isHovered = ref(false)

        const effectComponent = computed(() => {
            return getEffectComponent(props.effect)
        })

        return {
            isHovered,
            effectComponent
        }
    }
})
</script>

<style lang="scss" scoped>
.link-card {
    position: relative;
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-radius: var(--border-radius-large, 12px);
    background: var(--color-background-hover);
    text-decoration: none;
    color: var(--color-main-text);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
    min-height: 56px;

    // Flip effect needs perspective on parent
    &.effect-flip {
        perspective: 1000px;
    }

    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    &:focus-visible {
        outline: 2px solid var(--color-primary);
        outline-offset: 2px;
    }
}

.card-content {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    z-index: 1;
}

.icon-wrapper {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.link-icon {
    width: 32px;
    height: 32px;
    object-fit: contain;
    border-radius: var(--border-radius);
}

.icon-placeholder {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary-element-light);
    border-radius: var(--border-radius);
    color: var(--color-primary-element);
}

.link-title {
    flex: 1;
    font-weight: 500;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.external-indicator {
    flex-shrink: 0;
    color: var(--color-text-maxcontrast);
}
</style>
```

## EffectSelector.vue

**Modern @nextcloud/vue v9.x with Vue 3 Composition API:**

```vue
<template>
    <div class="effect-selector">
        <label class="selector-label">
            {{ t('dashlink', 'Hover Effect') }}
            <span class="hint">
                {{ t('dashlink', 'Animation style when hovering over links') }}
            </span>
        </label>

        <NcSelect
            v-model="selectedEffect"
            :options="effects"
            :placeholder="t('dashlink', 'Select effect...')"
            label="name"
            track-by="id"
            @update:modelValue="onSelectionChange"
        >
            <template #option="{ option }">
                <div class="effect-option">
                    <span class="effect-name">{{ option.name }}</span>
                    <span class="effect-description">{{ option.description }}</span>
                </div>
            </template>
        </NcSelect>
    </div>
</template>

<script>
import { defineComponent, ref, watch, onMounted } from 'vue'
import { NcSelect } from '@nextcloud/vue'
import { getAvailableEffects } from '../effects'
import { translate as t } from '@nextcloud/l10n'

export default defineComponent({
    name: 'EffectSelector',
    components: {
        NcSelect
    },
    props: {
        modelValue: {
            type: String,
            default: 'blur'
        }
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const effects = getAvailableEffects()
        const selectedEffect = ref(null)

        // Initialize selection
        onMounted(() => {
            selectedEffect.value = effects.find(e => e.id === props.modelValue) || effects[0]
        })

        watch(() => props.modelValue, (newValue) => {
            selectedEffect.value = effects.find(e => e.id === newValue) || effects[0]
        })

        function onSelectionChange(value) {
            if (value) {
                emit('update:modelValue', value.id)
            }
        }

        return {
            effects,
            selectedEffect,
            onSelectionChange,
            t
        }
    }
})
</script>

<style lang="scss" scoped>
.effect-selector {
    margin-bottom: 24px;
}

.selector-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;

    .hint {
        display: block;
        font-weight: normal;
        font-size: 12px;
        color: var(--color-text-maxcontrast);
    }
}

.effect-option {
    display: flex;
    flex-direction: column;
    gap: 2px;

    .effect-name {
        font-weight: 500;
    }

    .effect-description {
        font-size: 12px;
        color: var(--color-text-maxcontrast);
    }
}
</style>
```

## WidgetPreview.vue

```vue
<template>
    <div class="widget-preview">
        <div class="preview-container">
            <div class="preview-header">
                <LinkIcon :size="16" />
                <span>{{ t('dashlink', 'DashLink') }}</span>
            </div>
            <div class="preview-body">
                <div v-if="links.length === 0" class="empty-state">
                    {{ t('dashlink', 'No links to preview') }}
                </div>
                <div v-else class="preview-grid">
                    <LinkCard
                        v-for="link in links"
                        :key="link.id"
                        :link="link"
                        :effect="effect"
                        class="preview-card"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue'
import LinkIcon from 'vue-material-design-icons/Link.vue'
import LinkCard from './LinkCard.vue'
import { translate as t } from '@nextcloud/l10n'

export default defineComponent({
    name: 'WidgetPreview',
    components: {
        LinkIcon,
        LinkCard
    },
    props: {
        links: {
            type: Array,
            default: () => []
        },
        effect: {
            type: String,
            default: 'blur'
        }
    },
    setup() {
        return { t }
    }
})
</script>

<style lang="scss" scoped>
.widget-preview {
    padding: 16px;
    background: var(--color-main-background);
    border-radius: var(--border-radius-large);
}

.preview-container {
    border: 2px solid var(--color-border);
    border-radius: var(--border-radius-large);
    overflow: hidden;
}

.preview-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: var(--color-background-dark);
    border-bottom: 1px solid var(--color-border);
    font-weight: 500;
}

.preview-body {
    padding: 16px;
}

.preview-grid {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}

.empty-state {
    text-align: center;
    padding: 32px;
    color: var(--color-text-maxcontrast);
}
</style>
```

## Additional Components

The following components are also implemented in DashLink:

### AdminPanel.vue
Complete admin interface with:
- Link list with drag & drop reordering
- Create/Edit link forms
- Export/Import functionality
- Group filter simulation
- Live preview panel

### LinkForm.vue
Form component with:
- Title, URL, description fields
- Icon upload with drag & drop
- Group selection with NcCheckboxRadioSwitch
- Target selection (_blank/_self)
- Form validation

### IconUploader.vue
Icon management component with:
- Drag & drop file upload
- Icon preview with delete option
- Download icon from URL functionality
- File type and size validation (2MB max)
- Support for PNG, JPG, GIF, SVG, WebP

### GroupSelector.vue
Group visibility component with:
- Multiple group selection using NcCheckboxRadioSwitch
- "All users" option
- Fetches available groups from API

## Composables

### useLinks.js
```javascript
import { ref } from 'vue'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export function useLinks() {
    const links = ref([])
    const loading = ref(false)

    async function fetchLinks() {
        loading.value = true
        try {
            const response = await axios.get(generateUrl('/apps/dashlink/api/v1/admin/links'))
            links.value = response.data
        } finally {
            loading.value = false
        }
    }

    async function createLink(data) {
        const response = await axios.post(
            generateUrl('/apps/dashlink/api/v1/admin/links'),
            data
        )
        return response.data
    }

    async function updateLink(id, data) {
        const response = await axios.put(
            generateUrl(`/apps/dashlink/api/v1/admin/links/${id}`),
            data
        )
        return response.data
    }

    async function deleteLink(id) {
        await axios.delete(generateUrl(`/apps/dashlink/api/v1/admin/links/${id}`))
    }

    async function updateOrder(linkIds) {
        await axios.put(
            generateUrl('/apps/dashlink/api/v1/admin/links/order'),
            { linkIds }
        )
    }

    return {
        links,
        loading,
        fetchLinks,
        createLink,
        updateLink,
        deleteLink,
        updateOrder
    }
}
```

### useSettings.js
```javascript
import { ref } from 'vue'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export function useSettings() {
    const hoverEffect = ref('blur')
    const availableEffects = ref([])

    async function fetchSettings() {
        const response = await axios.get(
            generateUrl('/apps/dashlink/api/v1/admin/settings')
        )
        hoverEffect.value = response.data.hoverEffect
        availableEffects.value = response.data.availableEffects
    }

    async function updateHoverEffect(effect) {
        await axios.put(
            generateUrl('/apps/dashlink/api/v1/admin/settings'),
            { hoverEffect: effect }
        )
        hoverEffect.value = effect
    }

    return {
        hoverEffect,
        availableEffects,
        fetchSettings,
        updateHoverEffect
    }
}
```

### useGroups.js
```javascript
import { ref } from 'vue'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export function useGroups() {
    const groups = ref([])

    async function fetchGroups() {
        const response = await axios.get(
            generateUrl('/apps/dashlink/api/v1/admin/groups')
        )
        groups.value = response.data
    }

    return {
        groups,
        fetchGroups
    }
}
```

## Key Implementation Notes

1. **Vue 3 Migration**: Uses @nextcloud/vue v9.x with Vue 3 Composition API
2. **Modern Imports**: Components imported directly from '@nextcloud/vue' instead of dist paths
3. **Event Handling**: Uses `@update:modelValue` instead of `@input` for v-model compatibility
4. **Translation**: Uses `translate as t` from '@nextcloud/l10n' for i18n
5. **Notifications**: Custom notification utility using OC.Notification API (avoids @nextcloud/dialogs conflicts)
6. **Drag & Drop**: Uses @nextcloud/vue's NcButton with drag events for reordering
7. **Icon Components**: Uses vue-material-design-icons for consistent iconography
