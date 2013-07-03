<?php

namespace BFOS\PagamentoBundle;

use BFOS\PagamentoBundle\Entity\DadosAdicionaisPagamentoType;
use BFOS\PagamentoBundle\DependencyInjection\Compiler\RegistrarGatewaysPagamentoPass;
use BFOS\PagamentoBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\DBAL\Types\Type;

class BFOSPagamentoBundle extends Bundle
{
    public function boot()
    {
        if (false === Type::hasType(DadosAdicionaisPagamentoType::NAME)) {
            DadosAdicionaisPagamentoType::setServicoDeCriptografia($this->container->get('bfos_pagamento.servico_de_criptografia'));
            Type::addType(DadosAdicionaisPagamentoType::NAME, 'BFOS\PagamentoBundle\Entity\DadosAdicionaisPagamentoType');

            if ($this->container->has('doctrine.dbal.default_connection')) {
                $platform = $this->container->get('doctrine.dbal.default_connection')->getDatabasePlatform();
                $platform->markDoctrineTypeCommented(Type::getType(DadosAdicionaisPagamentoType::NAME));
            }
        }
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegistrarGatewaysPagamentoPass());
    }
}
