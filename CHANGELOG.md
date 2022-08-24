# Change Log for OXID twig engine component

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.1.0] - Unreleased

### Deprecated
- `Resolver\TemplateNameResolver`
- Method:
    - `TwigEngine::getDefaultFileExtension()`

### Fixed
- Finding templates (e.g. admin for EE)
- Filter regex_replace now returns empty string on null

## [1.0.0] - 2019-11-21
