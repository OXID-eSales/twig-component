# Change Log for OXID Twig engine component

## v2.2.0 - unreleased

### Added
- PHPUnit v10 support

### Removed
- PHPUnit v9 support

### Deprecated
- `SmartyCycleExtension` will be removed
- Appending missing file extensions (`".html.twig"`) to template names will be discontinued.
Since v3, the component will support only full template names.

## v2.1.0 - 2023-05-04

### Added
- Service Parameter to disable template caching `oxid_esales.templating.disable_twig_template_caching`

### Removed
- Dependency to `webmozart/path-util`

### Fixed
- Loading of shop templates when a theme inheritance is used
- Can't extend ` include_dynamic` template [#0007418](https://bugs.oxid-esales.com/view.php?id=7418)

### Changed
- `Loader\FilesystemLoader` reloads template directories on admin mode change
- `TwigContext::getActiveThemeId()` throws exception instead of type error when no theme is configured
- License updated

## v2.0.1 - 2022-11-23

### Fixed
- Warnings reported with stricter `error_reporting` level

## v2.0.0 - 2022-10-28

### Added
- Twig templates multi inheritance for modules
- Support for PHP v8
- `{{ content() }}` function to load content from DB
- `{% include_content %}` tag which includes template from DB

### Changed
- Switched to Twig v3

### Removed
- Support for PHP v7
- Support for `assign_adv` plugin
- `Resolver\TemplateNameResolver`
- `TwigContextInterface::getCacheDir()`, `TwigContextInterface::getTemplateDirectories()` and `TwigEngine::getDefaultFileExtension()`
- Redundant `is_safe` from extension initiation options

### Deprecated
- `Loader\CmsLoader`,
- `Loader\CmsTemplateNameParser`
