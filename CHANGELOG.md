# Changelog

All notable changes to the Wikit Semantics Chat plugin will be documented in this file.

## [3.0.0] - 2026-10-20

Versions 3.x are dedicated to GLPI 12 (2.x: GLPI 11, 1.x: GLPI 10).

### Added
- GLPI 12.0 compatibility

### Changed
- Configuration form rendered with a Twig template
- Database table created and upgraded with GLPI migrations instead of an SQL file
- Plugin URLs without the `/public` prefix (deprecated in GLPI 12)
- Maximum GLPI version set to 12.0.x, consistent with the plugins directory compatibility
- Development tooling aligned with the Teclib plugin template: php-cs-fixer, rector, phpstan, twigcs, eslint, licence headers; development files are no longer shipped in the release archives
- Unit tests (PHPUnit, GLPI test framework): widget configuration and profile rights

### Fixed
- Homepage URL and Composer autoload namespace
- No more `exit` in the AJAX endpoint (forbidden since GLPI 11)

### Removed
- GLPI 11 support: use the 2.x versions with GLPI 11
- `csrf_compliant` hook, removed from GLPI 12
- Manual prerequisites checks, replaced by the requirements declared to GLPI


## [2.0.3] - 2026-05-09

### Fix
- Fix multiple instances of bubble.


## [2.0.2] - 2026-03-13

### Added
- Support for Simplified interface ex : Self-service profile


## [2.0.1] - 2026-02-14

### Bug fix
- Fix a bug that ignored read and write permissions on profiles.


## [2.0.0] - 2026-01-13

### Added
- Full compatibility with GLPI 11.0+
- Customizable chat button appearance (color, size, icon)
- Flexible button positioning system (percentage-based)
- Custom tooltip text configuration
- User context awareness (passes user info to AI assistant)
- LLM App Slug configuration option for multiple app support
- Comprehensive internationalization (French and English)
- Database migration system for upgrades
- Profile-based permission system


## [1.0.0] - 2026-01-13

### Added
- Full compatibility with GLPI 10.0+
- Customizable chat button appearance (color, size, icon)
- Flexible button positioning system (percentage-based)
- Custom tooltip text configuration
- User context awareness (passes user info to AI assistant)
- LLM App Slug configuration option for multiple app support
- Comprehensive internationalization (French and English)
- Database migration system for upgrades
- Profile-based permission system
