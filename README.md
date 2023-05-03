# Twig component

[![Actions Status](https://github.com/OXID-eSales/twig-component/workflows/Build/badge.svg)](https://github.com/OXID-eSales/twig-component/actions)

Includes Twig template engine for OXID eShop

## Compatibility

* b-7.0.x branch is compatible with OXID eShop compilation 7.x
* 1.1.x versions (or b-6.5.x branch) are compatible with OXID eShop compilation 6.5.x

## Installation

To install the component run:
```bash
composer require oxid-esales/twig-component
```

## Configuration

- To put Twig into developer mode and prevent template cache generation, set the corresponding value in your project's YAML:
```yaml
parameters:
  oxid_esales.templating.disable_twig_template_caching: true
```

## License

See LICENSE file for details.

## Bugs and Issues

If you experience any bugs or issues, please report them in the section **OXID eShop (all versions)** under category **Twig engine** of https://bugs.oxid-esales.com
