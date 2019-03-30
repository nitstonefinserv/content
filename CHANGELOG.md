# Change Log

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.2.14] - 2018-02-12
### Changed
- fix publishable fields on tag admin options

## [1.2.13] - 2017-10-10
### Added
- make deleting files from storage optional

## [1.2.12] - 2017-09-28
### Fixed
- pass array to `attach` function `replaceTags` from `TaggableTrait` for backwards compatibility

## [1.2.11] - 2017-09-21
### Fixed
- fix the user roles field to allow validation

## [1.2.10] - 2017-09-20
### Added
- allow optional options to be passed through to user role field

## [1.2.9] - 2017-09-14
### Added
- add Enhanced Image plugin to ckeditor (allows centering of image)

## [1.2.8] - 2017-09-13
### Added
- add color and font config to ckeditor

## [1.2.7] - 2017-08-31
### Changed
- make lead image optional on publishable fields

## [1.2.6] - 2017-08-17
### Changed
- add options to textarea

## [1.2.5] - 2017-08-15
### Changed
- refactor the user roles feature

### Added
- Add function to check if user is super admin
- Add function to check role with id

## [1.2.4] - 2017-07-12
### Fixed
- add hidden field for site settings admin options

## [1.2.3] - 2017-07-10
### Fixed
- close out docblock

## [1.2.2] - 2017-07-05
### Fixed
- apply date field format

## [1.2.1] - 2017-06-19
### Fixed
- Datetime handles resave of blank date

## [1.2.0] - 2017-06-19
### Added
- Date, Datetime fields can return null for blank values
- Date, Datetime fields can mutate to Carbon
- Date, Datetime fields supports Carbon as input (useful for models that set $dates)
- Form constants: OPTIONS_VALIDATION, OPTIONS_NULLABLE, OPTIONS_DATETIME_MUTATE_TO_CARBON
### Fixed
- Datetime field displays blank time field if no time specified
- DB escaping of Content model type in scopeWithContent()
- PHPDoc in Admin\Form and Admin\Form\Widget

## [1.1.4] - 2017-06-06
### Fixed
- previous slugs was only displaying when there were > 1 alternate slugs

## [1.1.3] - 2017-05-24
### Fixed
- content-tag morph
### Changed
- prefer ::class over hardcoded string
- tabs to spaces

## [1.1.2] - 2017-05-19
### Added
- Help text can now be added to image field

## [1.1.1] - 2017-05-05
### Fixed
- FieldSet expects fields and rules to be bare arrays

## [1.1.0] - 2017-05-05
### Added
- CHANGELOG.md
- div, p, and static-content fields
- PublishableTrait can now be slugless

### Changed
- Download file links open in a new window
- Checkbox field has a mutator that always returns a boolean now. Previously null was possible.
- PostgresSearchableTrait->searchExcerpt() now uses PostgreSQL to generate the snippet instead of PHP
- Some tabs to spaces
- Some PHPDoc

### Fixed
- Warning when datetime field's disabled option isn't set
- ckeditor-file-browser uses Request instead of Input facade because of
    [this change](https://github.com/laravel/laravel/commit/2adbbbd91e9d6e2d569cc56f138b7273efe25651) to laravel
