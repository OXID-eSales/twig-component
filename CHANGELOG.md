# Change Log for OXID twig engine component

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.0.0] - 2022-10-28

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
- Classes:
  - `Resolver\TemplateNameResolver`
- Methods:
    - `TwigContextInterface::`
        - `getCacheDir()`
        - `getTemplateDirectories()`
    - `TwigEngine::getDefaultFileExtension()`
- Redundant `is_safe` from extension initiation options

### Deprecated
- `Loader\CmsLoader`,
- `Loader\CmsTemplateNameParser`

## [1.2.0] - Unreleased

### Fixed
- Various coding style improvements [PR-2](https://github.com/OXID-eSales/twig-component/pull/2)
- Fix not working include_dynamic tag [PR-3](https://github.com/OXID-eSales/twig-component/pull/3)

### Deprecated
- Methods:
    - `TwigContextInterface::`
      - `getCacheDir()`
      - `getTemplateDirectories()`

## [1.1.0] - 2022-09-08

### Deprecated
- `Resolver\TemplateNameResolver`
- Method:
    - `TwigEngine::getDefaultFileExtension()`

### Fixed
- Finding templates (problem found in admin for EE)
- Filter regex_replace now returns empty string on null

## [1.0.0] - 2019-11-21

### Added
- Add Twig themes requirements to composer.json

[2.0.0]: https://github.com/OXID-eSales/twig-component/compare/v1.1.0...v2.0.0
[1.1.0]: https://github.com/OXID-eSales/twig-component/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/OXID-eSales/twig-component/releases/tag/v1.0.0
