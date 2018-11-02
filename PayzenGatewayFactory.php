<?php

namespace Ekyna\Component\Payum\Payzen;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Payum\Core\GatewayFactoryInterface;

/**
 * Class PayzenGatewayFactory
 * @package Ekyna\Component\Payum\Payzen
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class PayzenGatewayFactory extends GatewayFactory
{
    /**
     * Builds a new factory.
     *
     * @param array                   $defaultConfig
     * @param GatewayFactoryInterface $coreGatewayFactory
     *
     * @return PayzenGatewayFactory
     */
    public static function build(array $defaultConfig, GatewayFactoryInterface $coreGatewayFactory = null)
    {
        return new static($defaultConfig, $coreGatewayFactory);
    }

    /**
     * @inheritDoc
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name'  => 'payzen',
            'payum.factory_title' => 'Payzen',

            'payum.action.capture'         => new Action\CaptureAction(),
            'payum.action.convert_payment' => new Action\ConvertPaymentAction(),
            'payum.action.api_request'     => new Action\Api\ApiRequestAction(),
            'payum.action.api_response'    => new Action\Api\ApiResponseAction(),
            'payum.action.sync'            => new Action\SyncAction(),
            'payum.action.refund'          => new Action\RefundAction(),
            'payum.action.status'          => new Action\StatusAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'site_id'     => '',
                'certificate' => '',
                'ctx_mode'    => '',
                'directory'   => '',
                'debug'       => false,
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['site_id', 'certificate', 'ctx_mode', 'directory', 'debug'];

            $config['payum.api'] = function (ArrayObject $config) {
                $debug  = $config['debug'];
                $config['debug'] = true;
                $config->validateNotEmpty($config['payum.required_options']);
                $payzenConfig = [
                    'site_id' => $config['site_id'],
                    'certificate' => $config['certificate'],
                    'ctx_mode' => $config['ctx_mode'],
                    'directory' => $config['directory'],
                    'debug' => $debug,
                ];
                $api = new Api\Api();
                $api->setConfig($payzenConfig);
                return $api;
            };
        }
    }
}
