<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Dependency\Service;

use DateTime;
use DateTimeInterface;
use FondOfSpryker\Service\ConditionalAvailability\ConditionalAvailabilityServiceInterface;

class ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityServiceBridge implements
    ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityServiceInterface
{
    /**
     * @var \FondOfSpryker\Service\ConditionalAvailability\ConditionalAvailabilityServiceInterface
     */
    protected $conditionalAvailabilityService;

    /**
     * @param \FondOfSpryker\Service\ConditionalAvailability\ConditionalAvailabilityServiceInterface $conditionalAvailabilityService
     */
    public function __construct(ConditionalAvailabilityServiceInterface $conditionalAvailabilityService)
    {
        $this->conditionalAvailabilityService = $conditionalAvailabilityService;
    }

    /**
     * @param \DateTime $deliveryDate
     *
     * @return \DateTimeInterface
     */
    public function generateLatestOrderDateByDeliveryDate(DateTime $deliveryDate): DateTimeInterface
    {
        return $this->conditionalAvailabilityService->generateLatestOrderDateByDeliveryDate($deliveryDate);
    }
}
