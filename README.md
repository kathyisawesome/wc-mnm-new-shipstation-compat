# WooCommerce Mix and Match: New ShipStation Compat

## Quickstart

This is a developmental repo, but no build steps are required to run the plugin.
OR    
|[Download latest release](https://github.com/kathyisawesome/wc-mnm-new-shipstation-compat/releases/latest)|
|---|

### What's This?

Experimental mini-extension for [WooCommerce Mix and Match Products](https://woocommerce.com/products/woocommerce-mix-and-match-products/) that alters the way the plugin integrates with Shipstation. Traditionally bundles that are packed _together_ have all the child product configuration compiled as meta on the container product, but the child product line items themselves are not imported to shipstation.

This should transform the imported items at Shipstation from selections imported as description meta:

![image](https://github.com/user-attachments/assets/eef2e4fe-cd3f-417a-bc5e-5eb131f3b610)


to a list where all shippable line items are imported:

![image](https://github.com/user-attachments/assets/a8196039-ac33-4785-96de-2f15e848d11a)


>**Warning**

1. This is provided _as is_ and does not receive priority support.
2. Please test thoroughly before using in production.

### Automatic plugin updates

Plugin updates can be enabled by installing the [Git Updater](https://git-updater.com/) plugin.
