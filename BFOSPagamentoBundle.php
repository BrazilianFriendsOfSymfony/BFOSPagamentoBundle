<?php

namespace BFOS\PagamentoBundle;

use BFOS\PagamentoBundle\DependencyInjection\Compiler\RegistrarGatewaysPagamentoPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BFOSPagamentoBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegistrarGatewaysPagamentoPass());
    }
}
