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

class RegistrarMeiosPagamentoPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('bfos_pagamento.registro_meio_pagamento')) {
            return;
        }

        $def = $container->findDefinition('bfos_pagamento.registro_meio_pagamento');
        foreach ($container->findTaggedServiceIds('bfos_pagamento.meio_pagamento') as $id => $attr) {
            $def->addMethodCall('registrar', array($attr[0]['identificador'], new Reference($id), $attr[0]['etiqueta']));
        }
    }
}
