<?php
namespace Paradigm\PayumTrustly\Action\Api;

use League\Url\Url;
use Paradigm\PayumTrustly\Request\Api\Deposit;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;

class DepositAction extends BaseApiAwareAction implements GenericTokenFactoryAwareInterface
{
    /**
     * @var GenericTokenFactoryInterface
     */
    protected $tokenFactory;

    /**
     * @param GenericTokenFactoryInterface $tokenFactory
     *
     * @return void
     */
    public function setGenericTokenFactory(GenericTokenFactoryInterface $tokenFactory = null)
    {
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @param Deposit $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ($model['orderid']) {
            throw new LogicException('Depositing is already started');
        }

        if (empty($model['NotificationURL']) && $request->getToken() && $this->tokenFactory) {
            $notifyToken = $this->tokenFactory->createNotifyToken(
                $request->getToken()->getGatewayName(),
                $request->getToken()->getDetails()
            );

            $model['NotificationURL'] = $notifyToken->getTargetUrl();
        }

        if (false == $model['SuccessURL'] && $request->getToken()) {
            $model['SuccessURL'] = $request->getToken()->getTargetUrl();
        }

        if (false == $model['FailURL'] && $request->getToken()) {
            $model['FailURL'] = $request->getToken()->getTargetUrl();
        }

        if (false == $model['IP']) {
            $this->gateway->execute($httpRequest = new GetHttpRequest());
            $model['IP'] = $httpRequest->clientIp;
        }

        $model->validateNotEmpty(array(
            'NotificationURL',
            'SuccessURL',
            'FailURL',
            'EndUserID',
            'MessageID',
            'Locale',
            'Amount',
            'Currency',
            'Country',
        ));

        $successUrl = Url::createFromUrl($model['SuccessURL']);
        $successUrl->setQuery(['returning' => 1]);
        $model['SuccessURL'] = (string) $successUrl;

        $failUrl = Url::createFromUrl($model['FailURL']);
        $failUrl->setQuery(['returning' => 1]);
        $model['FailURL'] = (string) $failUrl;

        /** @var \Trustly_Data_JSONRPCSignedResponse $deposit */
        $deposit = $this->api->deposit(
            $model['NotificationURL'],
            $model['EndUserID'],
            $model['MessageID'],
            $model['Locale'],
            $model['Amount'],
            $model['Currency'],
            $model['Country'],
            $model['MobilePhone'],
            $model['FirstName'],
            $model['LastName'],
            $model['NationalIdentificationNumber'],
            $model['ShopperStatement'],
            $model['IP'],
            $model['SuccessURL'],
            $model['FailURL'],
            $model['TemplateURL'],
            $model['URLTarget'],
            $model['SuggestedMinAmount'],
            $model['SuggestedMaxAmount'],
            $model['IntegrationModule']
        );

        $model->replace($deposit->getData());
    }
    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Deposit &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}