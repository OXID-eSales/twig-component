# Change Log for OXID Twig engine component

## v8.0.0-alpha.3 - Unreleased
*Compilation release*

### Changed
- Template chain cache is invalidated on `ThemeConfigurationChangedEvent`

### Removed
- PHP v8.3 support

## v8.0.0-alpha.2 - 2026-02-12
*Compilation release*

### Changed
- `TemplateChain` DTO `getByModuleId` and `getParent` methods return `null` when no matching result is found
- Escaper registration uses `EscaperRuntime`
- Token parsing aligned with Twig deprecations

### Removed
- Deprecated Twig APIs

## v3.0.0-alpha.1 - 2025-02-03

### Removed
- `SmartyCycleExtension` was removed
- Short template names (without file extensions `".html.twig"`) are no longer supported.
- Deprecated `DateFormatExtension`
