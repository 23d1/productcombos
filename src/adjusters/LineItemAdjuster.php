<?php
namespace modules\productcombos\adjusters;

use craft\commerce\base\AdjusterInterface;
use craft\commerce\elements\Order;
use craft\commerce\models\OrderAdjustment;

use modules\productcombos\ProductCombos as ProductCombos;

class LineItemAdjuster implements AdjusterInterface
{
    // Constants
    // =========================================================================

    /**
     * The discount adjustment type.
     */
    const ADJUSTMENT_TYPE = 'Line Item Adjuster';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function adjust(Order $order): array
    {
        $adjustments = [];
        foreach ($order->lineItems as $lineItem)
        {
            $adjustment = $this->_getEmptyOrderAdjustmentFor($order);
            $adjustment->lineItemId = $lineItem->id;

            // Calculate Price difference:
            $basePrice = $lineItem->getSubtotal();
            $discountAmount = $basePrice * -0.05;

            $adjustment->amount = $discountAmount;

            $adjustments[] = $adjustment;
        }

        return $adjustments;
    }

    // Private Methods
    // =========================================================================

    private function _getEmptyOrderAdjustmentFor(Order $order)
    {
        $adjustment = new OrderAdjustment();
        $adjustment->type = self::ADJUSTMENT_TYPE;
        $adjustment->name = '5% Discount';
        $adjustment->orderId = $order->id;
        $adjustment->description = 'A discount for nice people, like you!';
        $adjustment->sourceSnapshot = [
            'PrivateProp' => 'Criteria you want to make sure you have access to, in case you have to recalculate, later!'
        ];

        return $adjustment;
    }
}
