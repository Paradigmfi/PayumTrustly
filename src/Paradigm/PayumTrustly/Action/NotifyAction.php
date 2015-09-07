<?php
namespace Paradigm\PayumTrustly\Action;

use Payum\Core\Request\Notify;
use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;

class NotifyAction extends GatewayAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request Notify */
        RequestNotSupportedException::assertSupports($this, $request);

        throw new \LogicException('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}