# modernroad-theme

Child theme of Hello Elementor for the ModernRoad B2B site. Business logic
lives in the `icycode-b2b` plugin; this theme only handles how pages look.

## Structure

```
inc/pages.php                the MR_ pages (definitions + lookups)
inc/installer.php            Appearance → ModernRoad: Install / Restore
inc/shortcodes.php           [modernroad_home] and page headers on MR_ pages
inc/helpers.php              shared helpers (icons, status badge, logo, prices)
header.php, footer.php       new header/footer when installed, parent's (Elementor) otherwise
template-parts/              header and footer markup
assets/css/base.css          design tokens + shared components (mr-card, mr-btn, mr-info-grid, ...)
assets/css/layout.css        header, footer, page frame (installed only)
assets/css/tables.css        DataTables and account tables
assets/css/order-forms.css   the icycode-b2b order forms
assets/css/pages/*.css       one file per page, loaded only on that page
woocommerce/                 WooCommerce overrides that always apply (thank-you page)
templates/woocommerce/       WooCommerce overrides that apply only when the Woo page is an MR_ page
```

## Install (one click)

1. Upload the theme folder to `wp-content/themes/modernroad-theme` (and the latest `icycode-b2b` plugin)
2. First time only: activate **ModernRoad** in Appearance → Themes (Hello Elementor must stay installed)
3. Appearance → **ModernRoad** → **تثبيت التصميم الجديد**

Install creates the missing `MR_` pages, sets the front page, the WooCommerce checkout and
My Account pages, creates **MR Menu** for the header, and switches on the new header and footer.
The settings it replaces are saved; **استعادة السابق** on the same screen puts them back.
Old pages are never modified.

Before Install, the site looks exactly as before (except the thank-you page).

Never copy the local database to live: it would overwrite live orders and customers.

## Pages

| Page | MR_ page | Content | Assigned as |
|---|---|---|---|
| Home | `MR_Home` | `[modernroad_home]` | Front page |
| New order | `MR_NewOrder` | the three order-form shortcodes (each group sees its own) | MR Menu |
| Orders | `MR_Orders` | `[IcyCode_orders_table]` | MR Menu |
| Car report | `MR_CarReport` | `[IcyCode_car_report]` | MR Menu (only users with the car report) |
| My Account | `MR_MyAccount` | `[woocommerce_my_account]` | Woo My Account page |
| Checkout | `MR_Checkout` | `[woocommerce_checkout]` | Woo Checkout page |
| Thank you | — | part of checkout | — |

`MR_` titles are never shown on the site. Don't edit `MR_` pages with Elementor, and keep their
content as a Shortcode block (WooCommerce's Checkout/Account blocks ignore theme templates).

Not redesigned: order details (page 1024) and quotation (page 2053) print pages; the plugin
links to them by page ID.

After a WooCommerce update, check WooCommerce → Status → Templates for outdated overrides.
