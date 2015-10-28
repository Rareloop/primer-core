# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## 2.0.0 (?)
### Feature
- Added support for multiple template engines (Handlebars & Twig)
- Each pattern folder can also include an optional `init.php` that gets loaded before any data making it easier to scope view composers
- Patterns now include raw template rendering
- Patterns now show the data needed to render them
- Added `View::composer($name, $callable)` as syntactic sugar for `Event::listen("view.$name", $callable)`

### Breaking
- Primer specific data moved to `primer` namespace in `data.json` files
- New Handlebars engine drops support for `.handlebars` extensions, only `.hbs` now supported out of the box
- Removed `Pattern::composer`

## 1.1.0 (2015-08-16)
### Feature
- Added `getTemplates()` function to enable easy access to the list of templates currently available in the Primer install

## 1.0.1 (2015-07-29)
### Bug fixes
- Fixed minor rendering bugs in IE8

## 1.0.0 (2015-07-23)
- Initial Release
