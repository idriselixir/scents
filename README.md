# After Note

A storefront preview for Arabic fragrances (Afnan and Lattafa), built around
finding a perfume by the notes you like. Built with Laravel 13 and Livewire 4.

You can browse the collection, filter by scent family, search by bottle, house
or note, compare up to three fragrances, use the scent finder, and build a bag.
There is no checkout, and the prices in KSh are illustrative.

## Requirements

- PHP 8.3 or newer, with the SQLite extension
- Composer

Node is not needed. The styles and scripts are plain files in `public/assets`.

## Getting started

```bash
git clone git@github.com:idriselixir/scents.git
cd scents
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Then open http://localhost:8000.

`migrate --seed` creates the tables and loads the 12 fragrances and 4 scent
families. The seeder can be re-run at any time without creating duplicates:

```bash
php artisan db:seed
```

## Tests

```bash
php artisan test --compact
```

Tests run on an in-memory SQLite database, so they do not touch your local data.

Format PHP before committing:

```bash
vendor/bin/pint --dirty
```

## Where things are

| Path | What it holds |
| --- | --- |
| `app/Livewire/Core/Shop/Storefront.php` | The storefront page: filters, search, compare, scent finder, bag actions |
| `app/Services/FragranceCatalogService.php` | Listing, searching and scent-finder matches |
| `app/Services/BagService.php` | The bag, stored in the session (99 per fragrance at most) |
| `app/Models`, `app/Enums` | `Fragrance`, `ScentFamily` and their enums |
| `database/seeders/FragranceCatalogSeeder.php` | The catalogue data |
| `resources/views/livewire/core/shop/` | The page view |
| `resources/views/components/shop/` | Card, product detail, comparison and bag partials |
| `public/assets/` | Stylesheets, fonts, photos and `shop.js` (dialog and focus handling) |
| `tests/Feature/Shop/` | Pest tests for the page and the bag |

## Changing the catalogue

Edit `database/seeders/FragranceCatalogSeeder.php` and run `php artisan db:seed`.
A fragrance's `slug` also names its photo, so a new bottle needs a
`public/assets/{slug}.webp` image.

## Contributing

Work on a `feat/<thing>` branch and open a pull request. Do not commit straight
to `main`.
