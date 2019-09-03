<?php
namespace modules\productcombos\adjusters;

use craft\commerce\base\AdjusterInterface;
use craft\commerce\elements\Order;
use craft\commerce\models\OrderAdjustment;

use modules\productcombos\ProductCombos as ProductCombos;

class OrderAdjuster implements AdjusterInterface
{
    // Constants
    // =========================================================================

    /**
     * The discount adjustment type.
     */
    const ADJUSTMENT_TYPE = 'Discount';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function adjust(Order $order): array
    {
        $adjustments = [];

        $items = [];
        $cartvariants = [];
        $qty = [];

        foreach ($order->lineItems as $lineItem) {
            $items[] = $lineItem->purchasable->product;
            $cartvariants[] = $lineItem->purchasable->id;
            $qty[] = $lineItem->qty;
        }

        $categories = \craft\elements\Category::find()
            ->group('productPacks')
            ->all();

        foreach ($categories as $category) {
            $adjustment = $this->_getEmptyOrderAdjustmentFor($order);
            $discount = 0;

            $relateditems = [];
            $relatedvariants = [];

            foreach ($category->relatedProducts->all() as $product) {
                array_push($relateditems, $product);
            }
            foreach ($category->productVariants->all() as $variant) {
                array_push($relatedvariants, $variant->id);
            }

            if (!array_diff($relateditems, $items) && !array_diff($cartvariants, $relatedvariants)) {
                $multiplier = 0;
                if ($qty > 1) {
                    $multiplier = min($qty);
                }

                $adjustment->description = $category->title;
                $discount = $category->productPackDiscountAmount;

                // Set discount amount
                $adjustment->amount = -$discount * $multiplier;

                $adjustments[] = $adjustment;
            }
        }

        return $adjustments;
    }

    // Private Methods
    // =========================================================================

    private function _getEmptyOrderAdjustmentFor(Order $order)
    {
        $adjustment = new OrderAdjustment();
        $adjustment->type = self::ADJUSTMENT_TYPE;
        $adjustment->name = 'Combo';
        $adjustment->orderId = $order->id;
        $adjustment->description = 'Product combination discount.';
        $adjustment->sourceSnapshot = [
            'PrivateProp' => 'Criteria you want to make sure you have access to, in case you have to recalculate, later!'
        ];

        return $adjustment;
    }
}
