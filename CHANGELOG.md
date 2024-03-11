# Change Log for OXID Twig engine component

## v2.3.0 - 2024-03-11

### Changed
- Getting cache configuration parameter using templating cache service instead of context

## v2.2.0 - 2023-11-16

### Deprecated
- `SmartyCycleExtension` will be removed

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

## v1.2.0 - Unreleased

### Fixed
- Various coding style improvements [PR-2](https://github.com/OXID-eSales/twig-component/pull/2)
- Fix not working include_dynamic tag [PR-3](https://github.com/OXID-eSales/twig-component/pull/3)

### Deprecated
- `TwigContextInterface::getCacheDir()` and `TwigContextInterface::getTemplateDirectories()`

## v1.1.0 - 2022-09-08

### Deprecated
- `Resolver\TemplateNameResolver`
- `TwigEngine::getDefaultFileExtension()`

### Fixed
- Finding templates (problem found in admin for EE)
- Filter regex_replace now returns empty string on null

## v1.0.0 - 2019-11-21

### Added
- Add Twig themes requirements to composer.json
