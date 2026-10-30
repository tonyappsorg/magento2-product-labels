# Tonyapps_ProductLabels

Show rule-driven badges on product listings and product pages. Magento Open Source provides catalog price rules, but it has no general product-label feature for promotional text such as “New”, “−20%”, “Low stock”, or “Free gift”.

## Scope and decisions

| Feature | Decision |
|---|---|
| Rule conditions | Reuse Magento's catalog-rule product condition builder. Conditions are evaluated against the catalog product, not cart contents. |
| Badge content | Each rule has a short text label, foreground/background colors, sort order, active flag, and store-view scope. |
| Display | Show all matching labels on category/search product cards and product detail pages. Lower sort order appears first. |
| Rule conflict | No exclusivity: several rules may match and all render in configured order. |
| Cache behavior | Labels depend on product data, active rule configuration, and store view. Rule saves clear the full-page and block HTML caches. |
| Inventory | “Low stock” is supported only through product attributes that are available to catalog rules. Dynamic salable quantity and configurable child inventory are out of phase 1. |
| Price | A displayed “−20%” is administrator-authored text; labels do not calculate or promise a discount. |
| Security | Admin CRUD is ACL-protected and POST-only for mutations. Escape label text and validate CSS colors before output. |

## Phases

1. **Rule engine and storefront:** catalog-rule conditions, scoped rule storage/admin editing, matching and ordered badges on listing and detail views, and focused unit tests.
2. **Manual verification:** install/upgrade, create representative rules, inspect category/search and product pages, verify cache invalidation and multiple-label ordering.

## 2026-10-30 review fixes

- A rule condition on an attribute that was later deleted no longer causes a 500 on every storefront product collection. Attribute codes that no longer exist are dropped before `addAttributeToSelect()`.
- Condition attributes are added only to product-listing collections (the `catalog_block_product_list_collection` event), not to every frontend product collection such as the cart or cross-sells.
- Active rules are loaded once per request instead of once per product. The `category_ids` condition still runs one cached core lookup per product.

## Install

```sh
bin/magento module:enable Tonyapps_ProductLabels
bin/magento setup:upgrade
```

## Tests

```sh
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/Tonyapps/ProductLabels/Test/Unit
```
