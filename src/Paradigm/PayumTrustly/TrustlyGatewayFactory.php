<?php
namespace Paradigm\PayumTrustly;

use Paradigm\PayumTrustly\Action\CaptureAction;
use Paradigm\PayumTrustly\Action\ConvertPaymentAction;
use Paradigm\PayumTrustly\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class TrustlyGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults(array(
            'payum.factory_name' => 'trustly',
            'payum.factory_title' => 'Trustly',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.status' => new StatusAction(),
        ));

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = array();

            $config['payum.api'] = function (ArrayObject $config) {
                throw new \LogicException('Not implemented');
            };
        }
    }
}