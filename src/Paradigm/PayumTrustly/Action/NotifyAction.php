<?php
namespace Paradigm\PayumTrustly\Action;

use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;

class NotifyAction extends GatewayAwareAction implements ApiAwareInterface
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

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request Notify */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if('POST' != $httpRequest->method) {
            throw new HttpResponse('The notification is invalid. Code 1', 400);
        }

        if(false == $httpRequest->content) {
            throw new HttpResponse('The notification is invalid. Code 2', 400);
        }

        $notification = $this->api->handleNotification($httpRequest->content);

        if ($model['orderid'] != $notification->getData('orderid')) {
            throw new HttpResponse('The notification is invalid. Code 3', 400);
        }

        $model[$notification->getMethod()] = true;
        if($model->offsetExists('notifications')){
            $notifications = $model['notifications'];
        }
        $notifications[] = [
            'method' => $notification->getMethod(),
            'data' => $notification->getData(),
            'receivedAt' => time(),
        ];
        $model['notifications'] = $notifications;

        $json = $this->api->notificationResponse($notification, true)->json();

        throw new HttpResponse($json, 200, [
            'Content-Type' => 'application/json',
        ]);
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
