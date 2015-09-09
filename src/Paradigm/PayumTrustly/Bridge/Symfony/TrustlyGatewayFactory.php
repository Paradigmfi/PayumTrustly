<?php
namespace Paradigm\PayumTrustly\Bridge\Symfony;

use Payum\Bundle\PayumBundle\DependencyInjection\Factory\Gateway\AbstractGatewayFactory;
use Paradigm\PayumTrustly\TrustlyGatewayFactory as PayumTrustlyGatewayFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class TrustlyGatewayFactory extends AbstractGatewayFactory
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'trustly';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        parent::addConfiguration($builder);

        $builder->children()
            ->scalarNode('rsa_private_key')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('username')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('password')->isRequired()->cannotBeEmpty()->end()
            ->booleanNode('sandbox')->defaultTrue()->end()
        ->end();
    }

    /**
     * {@inheritDoc}
     */
    protected function getPayumGatewayFactoryClass()
    {
        return PayumTrustlyGatewayFactory::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getComposerPackage()
    {
        return 'paradigm/payum-trustly';
    }
}