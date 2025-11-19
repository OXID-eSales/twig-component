# Change Log for OXID Twig engine component

## v3.0.0-alpha.2 - Unreleased

### Removed
- Deprecated Twig APIs

### Changed
- `TemplateChain` DTO `getByModuleId` and `getParent` methods return `null` when no matching result is found
- Escaper registration uses `EscaperRuntime`
- Token parsing aligned with Twig deprecations

## v3.0.0-alpha.1 - 2025-02-03

### Removed
- `SmartyCycleExtension` was removed
- Short template names (without file extensions `".html.twig"`) are no longer supported.
- Deprecated `DateFormatExtension`
