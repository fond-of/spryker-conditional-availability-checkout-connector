<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Dependency\Facade;

use ArrayObject;
use Generated\Shared\Transfer\ConditionalAvailabilityCriteriaFilterTransfer;

interface ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\ConditionalAvailabilityCriteriaFilterTransfer $conditionalAvailabilityCriteriaFilterTransfer
     *
     * @return \ArrayObject<string,\Generated\Shared\Transfer\ConditionalAvailabilityTransfer[]>
     */
    public function findGroupedConditionalAvailabilities(
        ConditionalAvailabilityCriteriaFilterTransfer $conditionalAvailabilityCriteriaFilterTransfer
    ): ArrayObject;
}
