# Change Log for OXID twig engine component

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.2.0] - Unreleased

### Deprecated
- Method:
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

[1.1.0]: https://github.com/OXID-eSales/twig-component/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/OXID-eSales/twig-component/releases/tag/v1.0.0
