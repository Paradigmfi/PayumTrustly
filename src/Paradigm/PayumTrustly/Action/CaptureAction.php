<?php
namespace Paradigm\PayumTrustly\Action;

use Paradigm\PayumTrustly\Request\Api\Deposit;
use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\RenderTemplate;

class CaptureAction extends GatewayAwareAction
{
    /**
     * @var
     */
    private $depositTemplate;

    /**
     * @param string $depositTemplate
     */
    public function __construct($depositTemplate)
    {
        $this->depositTemplate = $depositTemplate;
    }

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ($model['returned']) {
            return;
        }

        // TODO check if still need to call deposit.

        if (false == $model['orderid']) {
            $deposit = new Deposit($request->getToken());
            $deposit->setModel($model);

            $this->gateway->execute($deposit);
        }

        $this->gateway->execute($httpRequest = new GetHttpRequest());
        if (isset($httpRequest->query['returning'])) {
            $model['returned'] = true;

            // user is comming back from the trustly side. just processed to done action.

            return;
        }

        $renderTemplate = new RenderTemplate($this->depositTemplate, array(
            'url' => $model['url'],
        ));
        $this->gateway->execute($renderTemplate);

        throw new HttpResponse($renderTemplate->getResult());
    }
    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}