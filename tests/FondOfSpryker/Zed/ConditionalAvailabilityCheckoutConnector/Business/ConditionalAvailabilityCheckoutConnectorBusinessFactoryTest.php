<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Business;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Business\Model\AvailabilitiesCheckerInterface;
use FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\ConditionalAvailabilityCheckoutConnectorDependencyProvider;
use FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Dependency\Facade\ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface;
use Spryker\Zed\Kernel\Container;

class ConditionalAvailabilityCheckoutConnectorBusinessFactoryTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Business\ConditionalAvailabilityCheckoutConnectorBusinessFactory
     */
    protected $conditionalAvailabilityCheckoutConnectorBusinessFactory;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Kernel\Container
     */
    protected $containerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Zed\ConditionalAvailabilityCheckoutConnector\Dependency\Facade\ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface
     */
    protected $conditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterfaceMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->conditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterfaceMock = $this->getMockBuilder(ConditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->conditionalAvailabilityCheckoutConnectorBusinessFactory = new ConditionalAvailabilityCheckoutConnectorBusinessFactory();
        $this->conditionalAvailabilityCheckoutConnectorBusinessFactory->setContainer($this->containerMock);
    }

    /**
     * @return void
     */
    public function testCreateAvailabilitiesChecker(): void
    {
        $this->containerMock->expects($this->atLeastOnce())
            ->method('has')
            ->willReturn(true);

        $this->containerMock->expects($this->atLeastOnce())
            ->method('get')
            ->with(ConditionalAvailabilityCheckoutConnectorDependencyProvider::FACADE_CONDITIONAL_AVAILABILITY)
            ->willReturn($this->conditionalAvailabilityCheckoutConnectorToConditionalAvailabilityFacadeInterfaceMock);

        $this->assertInstanceOf(
            AvailabilitiesCheckerInterface::class,
            $this->conditionalAvailabilityCheckoutConnectorBusinessFactory->createAvailabilitiesChecker()
        );
    }
}
