<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Business\Model;

use ArrayObject;
use DateTime;
use FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Dependency\Facade\ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ConditionalAvailabilityCriteriaFilterTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class AvailabilitiesChecker implements AvailabilitiesCheckerInterface
{
    protected const ERROR_TYPE_CONDITIONAL_AVAILABILITY = 'Conditional Availability';
    protected const MESSAGE_UNAVAILABLE_PRODUCT = 'conditional_availability_checkout_connector.product.unavailable';
    protected const ERROR_CODE_UNAVAILABLE_PRODUCT = 4102;
    protected const PARAMETER_PRODUCT_SKU = '%sku%';

    /**
     * @var \FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Dependency\Facade\ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface
     */
    protected $conditionalAvailabilityFacade;

    /**
     * @param \FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Dependency\Facade\ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface $conditionalAvailabilityFacade
     */
    public function __construct(
        ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface $conditionalAvailabilityFacade
    ) {
        $this->conditionalAvailabilityFacade = $conditionalAvailabilityFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function check(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool
    {
        $isPassed = true;
        $groupedQuoteItemTransferMap = $this->groupQuoteItemsBySku($quoteTransfer);

        $conditionalAvailabilityCriteriaFilterTransfer = (new ConditionalAvailabilityCriteriaFilterTransfer())
            ->setSkus(array_keys($groupedQuoteItemTransferMap->getArrayCopy()));

        $groupedConditionalAvailabilityTransferMap = $this->conditionalAvailabilityFacade
            ->findGroupedConditionalAvailabilities($conditionalAvailabilityCriteriaFilterTransfer);

        foreach ($groupedQuoteItemTransferMap as $sku => $groupedQuoteItemTransferCollection) {
            foreach ($groupedQuoteItemTransferCollection as $quoteItemTransfer) {
                $isQuoteItemAvailable = $this->isQuoteItemAvailable(
                    $quoteItemTransfer,
                    $groupedConditionalAvailabilityTransferMap
                );

                if ($isQuoteItemAvailable) {
                    continue;
                }

                $this->addErrorToCheckoutResponse($checkoutResponseTransfer, $sku);
                $isPassed = false;
            }
        }

        return $isPassed;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \ArrayObject $groupedConditionalAvailabilityTransferMap
     *
     * @throws
     *
     * @return bool
     */
    protected function isQuoteItemAvailable(
        ItemTransfer $quoteItemTransfer,
        ArrayObject $groupedConditionalAvailabilityTransferMap
    ): bool {
        $sku = $quoteItemTransfer->getSku();

        if (!$groupedConditionalAvailabilityTransferMap->offsetExists($sku)) {
            return false;
        }

        $concreteDeliveryDate = new DateTime($quoteItemTransfer->getConcreteDeliveryDate());
        $quantity = $quoteItemTransfer->getQuantity();

        foreach ($groupedConditionalAvailabilityTransferMap->offsetGet($sku) as $conditionalAvailabilityTransfer) {
            /** @var \Generated\Shared\Transfer\ConditionalAvailabilityTransfer $conditionalAvailabilityTransfer */
            $conditionalAvailabilityPeriodCollectionTransfer = $conditionalAvailabilityTransfer
                ->getConditionalAvailabilityPeriodCollection();

            if ($conditionalAvailabilityPeriodCollectionTransfer === null) {
                continue;
            }

            $conditionalAvailabilityPeriodTransfers = $conditionalAvailabilityPeriodCollectionTransfer
                ->getConditionalAvailabilityPeriods();

            foreach ($conditionalAvailabilityPeriodTransfers as $conditionalAvailabilityPeriodTransfer) {
                $startAt = new DateTime($conditionalAvailabilityPeriodTransfer->getStartAt());
                $endAt = new DateTime($conditionalAvailabilityPeriodTransfer->getEndAt());
                $availableQuantity = $conditionalAvailabilityPeriodTransfer->getQuantity();

                if ($concreteDeliveryDate < $startAt || $concreteDeliveryDate > $endAt || $availableQuantity < $quantity) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \ArrayObject<string,\Generated\Shared\Transfer\ConditionalAvailabilityTransfer[]>
     */
    protected function groupQuoteItemsBySku(QuoteTransfer $quoteTransfer): ArrayObject
    {
        $groupedQuoteItemTransferMap = new ArrayObject();

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $sku = $itemTransfer->getSku();

            if (!$groupedQuoteItemTransferMap->offsetExists($sku)) {
                $groupedQuoteItemTransferMap->offsetSet($sku, new ArrayObject());
            }

            $groupedQuoteItemTransferMap->offsetGet($sku)->append($itemTransfer);
        }

        return $groupedQuoteItemTransferMap;
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     * @param string $sku
     *
     * @return void
     */
    protected function addErrorToCheckoutResponse(CheckoutResponseTransfer $checkoutResponse, string $sku): void
    {
        $checkoutErrorTransfer = (new CheckoutErrorTransfer())
            ->setErrorCode(static::ERROR_CODE_UNAVAILABLE_PRODUCT)
            ->setMessage(static::MESSAGE_UNAVAILABLE_PRODUCT)
            ->setErrorType(static::ERROR_TYPE_CONDITIONAL_AVAILABILITY)
            ->setParameters([
                static::PARAMETER_PRODUCT_SKU => $sku,
            ]);

        $checkoutResponse
            ->addError($checkoutErrorTransfer)
            ->setIsSuccess(false);
    }
}
