<?php

namespace BFOS\PagamentoBundle;

use BFOS\PagamentoBundle\DependencyInjection\Compiler\RegistrarMeiosPagamentoPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BFOSPagamentoBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegistrarMeiosPagamentoPass());
    }
}
