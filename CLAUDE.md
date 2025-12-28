# DashLink - External Links Widget for Nextcloud

> **Spec-Driven Development Specification**
> Version: 1.1
> Target Nextcloud Version: 28+

## Project Overview

**DashLink** is a standalone Nextcloud app that provides a dashboard widget allowing administrators to centrally manage external website links for users. The app is completely independent and offers features like custom logos, modular hover animations, group-based visibility, and a live preview in the admin panel.

### Key Features

- Admin panel to manage up to 8 external links
- Custom logo/icon upload for each website
- Description text for each link
- Configurable link opening behavior (same tab `_self` or new tab `_blank`)
- **Modular hover effect system** - Multiple animation styles, easily extensible
- **Group-based visibility** - Show links only to specific Nextcloud groups
- **Live preview in admin panel** - Instant preview with selected hover effect
- Responsive design for desktop and mobile

---

## Technology Stack

| Component | Technology |
|-----------|------------|
| Backend | PHP 8.1+ with Nextcloud App Framework |
| Frontend | Vue.js 3 with Composition API |
| UI Components | @nextcloud/vue |
| Build | Webpack 5, npm |
| Database | Nextcloud OCP Database API (SQLite/MySQL/PostgreSQL) |
| Styling | SCSS with Nextcloud CSS variables |
| Testing | PHPUnit (Backend), Jest (Frontend) |

---

## Architecture

### High-Level Structure

```
├── Backend (PHP)
│   ├── Controllers (LinkController, SettingsController, AdminController)
│   ├── Services (LinkService, SettingsService, IconService)
│   ├── Database (Link entity, LinkMapper)
│   └── Dashboard Widget (DashLinkWidget)
├── Frontend (Vue.js)
│   ├── Components (Dashboard, LinkCard, AdminPanel, EffectSelector)
│   ├── Effects System (Modular effect registry)
│   └── Composables (useLinks, useSettings, useGroups)
└── API (REST endpoints for CRUD + settings)
```

**See**: [docs/setup-config.md](docs/setup-config.md) for full project structure

---

## Database Schema

### Table: `oc_dashlink_links`

```sql
CREATE TABLE oc_dashlink_links (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    url             VARCHAR(2048) NOT NULL,
    description     TEXT,
    icon_path       VARCHAR(512) DEFAULT NULL,
    icon_mime_type  VARCHAR(64) DEFAULT NULL,
    target          VARCHAR(10) DEFAULT '_blank',
    groups_json     TEXT DEFAULT NULL,           -- JSON array of group IDs
    position        INT UNSIGNED DEFAULT 0,
    enabled         TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_position (position),
    INDEX idx_enabled (enabled)
);
```

### Global Settings (App Config)

Stored using Nextcloud's `IConfig` API:

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `hover_effect` | string | `blur` | Active hover effect ID |

---

## Modular Hover Effects System

### Architecture Overview

The hover effects are implemented as a modular plugin system, making it easy to add new effects without modifying core code.

```
src/effects/
├── index.js                 # Central registry
├── EffectBase.vue           # Shared base component
├── effect_blur/             # Effect: Blur Overlay
│   ├── index.js             # { id, name, description, component }
│   └── EffectBlur.vue
├── effect_flip/             # Effect: 3D Card Flip
│   ├── index.js
│   └── EffectFlip.vue
└── effect_slide/            # Effect: Slide Panel
    ├── index.js
    └── EffectSlide.vue
```

### Effect Registry Functions

```javascript
// Get all available effects for dropdown
export function getAvailableEffects()

// Get effect component by ID (with fallback)
export function getEffectComponent(effectId)

// Get effect metadata by ID
export function getEffect(effectId)
```

### Effect Component Interface

All effect components must accept these props:

```javascript
props: {
    link: {
        type: Object,
        required: true,
        // { id, title, url, description, iconUrl, target }
    },
    isHovered: {
        type: Boolean,
        default: false
    }
}
```

### Included Effects

1. **Blur Overlay** (`blur`) - Logo becomes blurred background with description overlay
2. **3D Card Flip** (`flip`) - Card flips to reveal description on back
3. **Slide Panel** (`slide`) - Panel slides up from bottom with gradient

**See**: [docs/effect-system.md](docs/effect-system.md) for full implementation examples and how to add new effects

---

## API Specification

### Base URL
```
/apps/dashlink/api/v1
```

### Link Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/links` | Get links for current user (filtered by groups) | User |
| GET | `/links/{id}/icon` | Get link icon | User |
| GET | `/admin/links` | Get all links | Admin |
| POST | `/admin/links` | Create link | Admin |
| PUT | `/admin/links/{id}` | Update link | Admin |
| DELETE | `/admin/links/{id}` | Delete link | Admin |
| POST | `/admin/links/{id}/icon` | Upload icon | Admin |
| DELETE | `/admin/links/{id}/icon` | Delete icon | Admin |
| PUT | `/admin/links/order` | Update link order | Admin |
| GET | `/admin/groups` | Get available groups | Admin |

### Settings Endpoints

#### Get Settings
```
GET /admin/settings
```
Response:
```json
{
    "hoverEffect": "blur",
    "availableEffects": [
        {
            "id": "blur",
            "name": "Blur Overlay",
            "description": "Description appears over a blurred logo background"
        },
        ...
    ]
}
```

#### Update Settings
```
PUT /admin/settings
Content-Type: application/json

{
    "hoverEffect": "flip"
}
```

---

## Implementation Details

### Backend

- **SettingsService**: Manages global settings (hover effect) via IConfig
- **SettingsController**: REST API for settings (GET/PUT)
- **DashLinkWidget**: Dashboard widget that provides initial state (links + effect)

**See**: [docs/backend-implementation.md](docs/backend-implementation.md) for code examples

### Frontend

- **LinkCard.vue**: Dynamic component that loads effects via `getEffectComponent()`
- **EffectSelector.vue**: Dropdown for selecting hover effect in admin
- **WidgetPreview.vue**: Live preview with selected effect
- **dashboard.js**: Loads initial state for links and hover effect

**See**: [docs/frontend-implementation.md](docs/frontend-implementation.md) for code examples

### Configuration

- **routes.php**: API route definitions
- **info.xml**: App metadata and dependencies
- **Project structure**: Directory layout and organization

**See**: [docs/setup-config.md](docs/setup-config.md) for configuration files

---

## Development Workflow

### Adding a New Hover Effect

1. Create effect folder: `src/effects/effect_yourname/`
2. Create component: `EffectYourname.vue` (must accept `link` and `isHovered` props)
3. Create index: `index.js` with `{ id, name, description, component }`
4. Register in `src/effects/index.js`
5. Update backend `SettingsService::AVAILABLE_EFFECTS` array

The effect will automatically appear in the admin dropdown and be available for selection.

**See**: [docs/effect-system.md](docs/effect-system.md) for detailed instructions

---

## Completion Checklist

### Core Features ✅ COMPLETE
- [x] Database migration created and tested
- [x] Link entity and mapper implemented
- [x] LinkService with group filtering implemented
- [x] IconService for upload/delete implemented
- [x] SettingsService for global settings
- [x] All API endpoints functional (15 total)

### Effect System ✅ COMPLETE
- [x] Effect registry (`src/effects/index.js`) implemented
- [x] EffectBlur component working
- [x] EffectFlip component working
- [x] EffectSlide component working
- [x] Effects load dynamically in LinkCard
- [x] EffectSelector dropdown in admin panel

### Admin Panel ✅ COMPLETE
- [x] Effect selection dropdown
- [x] Links CRUD functions
- [x] Drag & drop sorting with position numbers
- [x] GroupPicker component
- [x] WidgetPreview with live effect preview and group filtering
- [x] Export/Import functionality with JSON
- [x] Modern toggle switches for enabled state

### Quality ✅ COMPLETE
- [x] Responsive design tested (1-4 column grid)
- [x] Dark mode compatible (Nextcloud CSS variables)
- [ ] Unit tests written (>80% coverage) - Future enhancement
- [ ] Integration tests for API - Future enhancement
- [ ] Translations prepared (l10n) - Future enhancement

### Additional Features Implemented (Beyond Original Spec)
- [x] Export links to JSON with absolute Nextcloud URLs
- [x] Import links from JSON with duplicate detection (by title or URL)
- [x] Download and save icons from external URLs during import
- [x] Position-based ordering with drag-and-drop reordering
- [x] Live preview with group filter simulation
- [x] Modern Nextcloud toggle switches (NcCheckboxRadioSwitch)
- [x] Icon management with upload/delete/download capabilities
- [x] Widget title customization (configurable in settings)
- [x] Position number badges in admin panel for visual ordering feedback

---

## Important Implementation Notes

1. **Effect Modularity**: Each effect is self-contained in its own folder
2. **Dynamic Loading**: Effects are loaded dynamically via `getEffectComponent()`
3. **Consistent Interface**: All effects receive same props (`link`, `isHovered`)
4. **Easy Extension**: Add new effects by creating folder and registering
5. **Settings Storage**: Global effect setting stored via Nextcloud IConfig API
6. **Preview Sync**: Admin preview updates immediately when effect is changed
7. **Fallback**: Unknown effects fall back to 'blur' effect
8. **i18n**: All effect names and descriptions should use `t('dashlink', '...')`

---

## Documentation

- [Effect System Implementation](docs/effect-system.md) - Detailed effect code and examples
- [Backend Implementation](docs/backend-implementation.md) - PHP services and controllers
- [Frontend Implementation](docs/frontend-implementation.md) - Vue.js components
- [Setup & Configuration](docs/setup-config.md) - Routes, info.xml, project structure
