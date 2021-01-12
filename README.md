# Laravel Accept-Language to SetLocale (ALSL)

Adds support for iln8 Detection.

Iteration of a user's language preferences set in the `Accept-Language` request header, using order of language preference from users web browser; attempts to find a matching locale available in Laravel `resources/lang` directory.

- Attempts Fallback to a mainland language, honouring the users first preferences.
- Restores saved user preference from an authenticated user  `User::locale` if set.
- Fallback to Laravel configuration `app.locale` else `en` (English).

[![Laravel ALSL](https://img.youtube.com/vi/VrvBUeSVDNE/0.jpg)](https://youtu.be/VrvBUeSVDNE)

## Installation

`composer require marcnewton/laravel-alsl`

## Usage Information

`app()->getLocale()` will return the value of either the users web browser language preference or authenticated user `locale` attribute if present, as long as a matching ISO is found in the `resources/lang` directory else the `app.locale` config value will be used.

## Language Variants

To support variants, simple create language translation folders in `resources/lang`.

For example, supporting **English (United States)** and **English (United Kingdom)**, create folders `en-US` and `en-GB` in addition to `en` (English).

## How detection works.

A Spanish-speaking user has preferences list only for **Spanish (Mexico)** then **Spanish (United States)**;

| Available Server Languages | Matches |
| - |- |
| **en, es_US, es** | `es-US` |
| **en, es_US, es_MX, es** | `es-MX` |
| **en, es** | `es` |
| **en** | `en` |
| **en, en_US, es_US, es_MX, es_CL** | `es-MX` |

If the user only specified **Spanish** then this would match `es` but if the server only has `en` then it would present `en`.

However, if the user specified a preference for **Spanish** first then a Spanish variant, `es` would match first rather than the latter choices for variants unless the server does not have an `es` language folder, a variant folder will match else fallback to a configuration default.

**Underscores** from the `Accept-Language` header are converted to **Hyphens** to align with usages in PHP & Javascript localization methods.

## Deployments
When adding or removing language support in `resources/lang`, you should ensure you run `php artisan cache:forget resources.languages` to clear the cache.
