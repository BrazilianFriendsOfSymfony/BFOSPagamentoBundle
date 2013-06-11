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

namespace BFOS\PagamentoBundle\Command;


use BFOS\PagamentoBundle\GatewayPagamento\GatewayPagamentoInterface;
use BFOS\PagamentoBundle\GatewayPagamento\Registro\RegistroGatewayPagamentoInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListarCommand extends ContainerAwareCommand
{
    /** @var  RegistroGatewayPagamentoInterface $registro */
    protected $registro;

    protected function configure()
    {
        $this
        ->setName('bfos:pagamento:listar-gateways')
        ->setDescription('Lista todos os gateways de pagamentos disponiveis no projeto.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->registro = $this->getContainer()->get('bfos_pagamento.registro_gateway_pagamento');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Listando todos os <comment>gateways de pagamento</comment>.');
        $output->writeln('');

        /**
         * @var GatewayPagamentoInterface $gateway
         * @var string $identificador
         */
        $todos = $this->registro->getTodos();
        if(count($todos)){
            foreach ($todos as $identificador => $gateway) {
                $output->writeln(sprintf('%s - %s', $identificador, $this->registro->getEtiqueta($identificador)));
            }
        } else {
            $output->writeln('<comment>Nenhum</comment> meio de pagamento encontrado.');
            $output->writeln('');
        }

    }
}
