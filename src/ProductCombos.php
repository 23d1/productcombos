<?php
namespace modules\productcombos;

use Craft;

use yii\base\Event;
use yii\base\Module;

use craft\events\RegisterComponentTypesEvent;
use craft\commerce\services\OrderAdjustments;

use modules\productcombos\adjusters\OrderAdjuster;

class ProductCombos extends Module
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this module class so that it can be accessed via
     * ProductCombos::$instance
     *
     * @var ProductCombos
     */
    public static $instance;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * Called after the module class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * Set our $instance static property to this class so that it can be accessed via
     * Utils::$instance
     *
     */
    public function init()
    {
        parent::init();
        self::$instance = $this;

        // The order you add these is important! Adjusters run on the current total(s)
        // of the order, so if you are working with percentage-based discounts, be
        // careful how you organize these. Below, we're “unshift-ing” them (in argument
        // order) onto the `$event->types` array. LineItem adjustments will happen
        // before the complete Order adjustment is calculated.
        Event::on(
            OrderAdjustments::class,
            OrderAdjustments::EVENT_REGISTER_ORDER_ADJUSTERS,
            function(RegisterComponentTypesEvent $event) {
                array_unshift(
                    $event->types,
                    OrderAdjuster::class
                );

                return $event->types;
            });
    }
}
