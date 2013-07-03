<?php
 /*
 * This file is part of the Duo Criativa software.
 * Este arquivo é parte do software da Duo Criativa.
 *
 * (c) Paulo Ribeiro <paulo@duocriativa.com.br>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BFOS\PagamentoBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegistrarGatewaysPagamentoPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('bfos_pagamento.registro_gateway_pagamento')) {
            return;
        }

        $gateways = array();

        $def = $container->findDefinition('bfos_pagamento.registro_gateway_pagamento');
        foreach ($container->findTaggedServiceIds('bfos_pagamento.gateway_pagamento') as $id => $attr) {
            $identificador = $attr[0]['identificador'];
            $etiqueta = $attr[0]['etiqueta'];
            $def->addMethodCall('registrar', array($identificador, new Reference($id), $etiqueta));

            $gateways[$identificador] = $etiqueta;
        }

        $container->setParameter('bfos_pagamento.gateways', $gateways);
    }
}
