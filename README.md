# Product Combos
## Product Combo Module for Craft 3 Commerce 2

This is early alpha stages, only use as a starting point.

The intent of this module is to check cart contents against a category group with the handle `productPacks`. Then based on the fields in each of the categories (specific product packs) apply a discount.

The way the categories are set up is that they have a numeric field with the handle `productPackDiscountAmount`, which corresponds to the amount of discount.

Then there is a `relatedProducts` Commerce Products field that is checked against which products are related. Then there is a `productVariants` Commerce Variants field that has all the variants from the products that one wants to be enabled for the combo discount.

Very basic checking and comparing carts contents with categories that are essentially 'packs'.
