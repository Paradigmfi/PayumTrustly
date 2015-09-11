<?php
namespace Paradigm\PayumTrustly;

use Paradigm\PayumTrustly\Action\Api\DepositAction;
use Paradigm\PayumTrustly\Action\CaptureAction;
use Paradigm\PayumTrustly\Action\ConvertPaymentAction;
use Paradigm\PayumTrustly\Action\NotifyAction;
use Paradigm\PayumTrustly\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Bridge\Twig\TwigFactory;
use Payum\Core\GatewayFactory;

class TrustlyGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'trustly',
            'payum.factory_title' => 'Trustly',
            'payum.template.deposit' => '@ParadigmTrustly/Action/deposit.html.twig',
        ]);

        $config->defaults([
            'payum.action.capture' => new CaptureAction($config['payum.template.deposit']),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.api.deposit' => new DepositAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'rsa_private_key' => null,
                'username' => null,
                'password' => null,
                'sandbox' => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = array(
                'rsa_private_key',
                'username',
                'password'
            );

            $config['payum.api'] = function (ArrayObject $config) {
                return new \Trustly_Api_Signed(
                    $config['rsa_private_key'],
                    $config['username'],
                    $config['password'],
                    $config['sandbox'] ? 'test.trustly.com' : 'trustly.com'
                );
            };
        }
    }
}