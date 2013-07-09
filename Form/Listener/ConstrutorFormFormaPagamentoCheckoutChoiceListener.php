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

namespace BFOS\PagamentoBundle\Form\Listener;

use BFOS\PagamentoBundle\FormaPagamento\GerenteFormaPagamentoInterface;
use BFOS\PagamentoBundle\GatewayPagamento\Registro\RegistroGatewayPagamentoInterface;
use BFOS\PagamentoBundle\Model\FormaPagamentoInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ConstrutorFormFormaPagamentoCheckoutChoiceListener implements EventSubscriberInterface
{
    /**
     * @var RegistroGatewayPagamentoInterface $registro
     */
    protected $registro;

    /**
     * @var FormFactoryInterface $factory
     */
    protected $factory;

    protected $gerente;
    protected $configuracoes;

    public function __construct(
        RegistroGatewayPagamentoInterface $registro,
        GerenteFormaPagamentoInterface $gerente,
        FormFactoryInterface $factory,
        $configuracoes
    ) {
        $this->registro = $registro;
        $this->gerente = $gerente;
        $this->factory = $factory;
        $this->configuracoes = $configuracoes;
    }


    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_BIND => 'preBind'
        );
    }


    /**
     * Adiciona a configuração do gateway se houver.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /**
         * @var FormaPagamentoInterface $data
         */
        $data = $event->getData();

        if (null === $data || null === $data->getGatewayPagamento()) {
            return;
        }

        $this->adicionaConfiguracaoDosCampos(
            $event->getForm(),
            $data['formaPagamentoId'],
            isset($data['configuracao']) ? $data['configuracao'] : null
        );
    }

    /**
     * Adiciona configuração se houver.
     *
     * @param FormEvent $event
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('formaPagamentoId', $data)) {
            return;
        }

        $this->adicionaConfiguracaoDosCampos($event->getForm(), $data['formaPagamentoId']);
    }

    /**
     * Adiciona a configuração dos campos do gateway
     *
     * @param FormInterface $form
     * @param string $identificador
     * @param array $data
     */
    protected function adicionaConfiguracaoDosCampos(FormInterface $form, $identificador, array $data = array())
    {
        /** @var FormaPagamentoInterface $formaPagamento */
        $formaPagamento = $this->gerente->getFormaPagamentoById($identificador);

        if (!isset($this->configuracoes[$formaPagamento->getGatewayPagamento()]) || !isset($this->configuracoes[$formaPagamento->getGatewayPagamento()]['configuracao_checkout_form']) ) {
            return;
        }

        /** @var FormInterface $configuracaoDoCampo */
        $configuracaoDoCampo = $this->factory->createNamed(
            'configuracao',
            $this->configuracoes[$formaPagamento->getGatewayPagamento()]['configuracao_checkout_form'],
            $data,
            array('label' => 'bfos_pagamento.form.configuracao_checkout')
        );

        $form->add($configuracaoDoCampo);
    }

}
