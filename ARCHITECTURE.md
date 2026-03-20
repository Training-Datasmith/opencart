# OpenCart Architecture

## Purpose

OpenCart is a PHP-based open-source e-commerce platform. It provides a traditional
MVC-style storefront, an admin panel, a REST API layer, and an extension marketplace.
OpenCart 4.x uses a modern PHP 8.x codebase with namespace-based autoloading.

## Directory Structure

```
upload/
  system/               # Framework core (no user-facing code)
    engine/             # MVC bootstrap: Registry, Router, Controller, Model, View
    library/            # Cart, Customer, Session, Request, Response, DB, Cache services
    config/             # Default configuration values
    helper/             # Global helper functions (oc_strlen, oc_validate_regex, etc.)
    storage/            # Cache, log, and session storage backends
  catalog/              # Storefront application
    controller/         # Request handlers
      checkout/         # Cart, Checkout, Confirm, Payment, Shipping controllers
      product/          # Product listing and detail controllers
      account/          # Customer account controllers
    model/              # Database query layer (Catalog, Checkout, Account models)
    view/               # Twig templates and compiled view cache
    language/           # Storefront translation files
  admin/                # Admin panel (mirrors catalog structure)
    controller/
    model/
    view/
    language/
  extension/            # Third-party extensions (each extension is a self-contained folder)
```

## Key Design Decisions

- **Registry pattern**: A central `Registry` object holds all services (db, cart, config,
  session, request, response, etc.). Controllers and models access services via `$this->key`
  thanks to `__get`/`__set` magic that proxies to the registry.
- **MVC over DI**: OpenCart uses a pull-based service pattern (registry) rather than
  constructor injection. Extension compatibility is maintained by avoiding DI containers.
- **Event system**: The `Event` class provides a lightweight hook mechanism.
  Extensions attach listeners via `$event->register('catalog/controller/checkout/cart/before', ...)`.
- **Cart session storage**: Cart items are stored in PHP sessions. The `Cart` library class
  (`system/library/cart/cart.php`) manages product lookup, option resolution, and stock
  checking on every page load.
- **Language files**: All user-facing strings live in language array files
  (`catalog/language/{locale}/checkout/cart.php`). The `Language` library loads them on demand.
- **Extensions**: Extensions live in `extension/{extension_name}/` and follow the same
  controller/model/view/language directory layout as the core application.

## Extension Points

- **Events**: Register event listeners for any controller/model action prefix (before/after).
- **OCMOD (XML Modification)**: Modify core files at install time via XML patch files.
- **Override**: Place controller or model files in `extension/{name}/catalog/controller/`
  to override core behaviour without modifying source files.
- **Total extensions**: Custom cart totals (fees, discounts) are loaded via the
  `extension/*/checkout/{code}` controller convention.

## Dependency Flow

```
HTTP Request
  └─> Router (maps URL to controller/action)
        └─> Controller (e.g. Catalog\Controller\Checkout\Cart)
              └─> Registry services: cart, session, config, request, response
              └─> Model (e.g. Catalog\Model\Checkout\Cart)
                    └─> DB (MySQLi/PDO abstraction layer)
              └─> View (Twig template rendering)
                    └─> Response::set_output()
```

## PHP Version Requirements

PHP 8.1+. All new files use `declare(strict_types=1)`. Namespace pattern:
`Opencart\{Catalog|Admin|System}\{Engine|Library|Controller|Model}\...`
