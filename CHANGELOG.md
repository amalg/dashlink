# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-12-28

### Added

**Core Dashboard Widget:**
- Dashboard widget displaying external links with customizable icons
- Three modular hover effects: Blur Overlay, 3D Card Flip, Slide Panel
- Group-based visibility filtering (show links to specific user groups)
- Responsive grid layout (1-4 columns based on viewport)
- Full dark mode support with Nextcloud CSS variables

**Admin Panel:**
- Complete CRUD operations for link management
- Drag & drop reordering with visual position badges
- Modern Nextcloud UI components (toggle switches, modals, pickers)
- Live preview panel with group filter simulation
- Icon management: upload, delete, download from URLs
- Group picker with autocomplete for visibility control

**Import/Export System:**
- Export links to JSON with absolute Nextcloud URLs
- Import links from JSON with duplicate detection (by title or URL)
- Automatic icon downloading from external URLs during import
- Position management (imported links appended after existing)

**Technical Implementation:**
- PHP 8.1+ backend with Nextcloud App Framework
- Vue 3 with Composition API
- 15 RESTful API endpoints
- Modular effect system (easily extensible)
- Database migration with proper indexing
- Icon storage in Nextcloud app data directory (supports PNG, JPG, GIF, SVG, WebP up to 2MB)

[1.0.0]: https://github.com/lexioj/dashlink/releases/tag/v1.0.0
