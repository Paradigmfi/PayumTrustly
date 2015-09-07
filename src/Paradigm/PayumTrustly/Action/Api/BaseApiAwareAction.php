<?php
namespace Paradigm\PayumTrustly\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\UnsupportedApiException;

abstract class BaseApiAwareAction extends GatewayAwareAction implements ApiAwareInterface
{
    /**
     * @var \Trustly_Api_Signed
     */
    protected $api;

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (false == $api instanceof \Trustly_Api_Signed) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }
}