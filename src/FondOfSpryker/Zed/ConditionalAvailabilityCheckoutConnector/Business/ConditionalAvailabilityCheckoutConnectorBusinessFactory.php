<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Business;

use FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Business\Model\AvailabilitiesChecker;
use FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Business\Model\AvailabilitiesCheckerInterface;
use FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\ConditionalAvailabilityCheckoutConnectorDependencyProvider;
use FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Dependency\Facade\ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\ConditionalAvailabilityCheckoutConnectorConfig getConfig()
 */
class ConditionalAvailabilityCheckoutConnectorBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Business\Model\AvailabilitiesCheckerInterface
     */
    public function createAvailabilitiesChecker(): AvailabilitiesCheckerInterface
    {
        return new AvailabilitiesChecker(
            $this->getConditionalAvailabilityFacade()
        );
    }

    /**
     * @throws
     *
     * @return \FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Dependency\Facade\ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface
     */
    protected function getConditionalAvailabilityFacade(): ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface
    {
        return $this->getProvidedDependency(ConditionalAvailabilityCheckoutConnectorDependencyProvider::FACADE_CONDITIONAL_AVAILABILITY);
    }
}
