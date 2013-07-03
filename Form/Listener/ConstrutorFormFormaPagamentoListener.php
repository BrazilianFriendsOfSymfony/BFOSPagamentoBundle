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

use BFOS\PagamentoBundle\GatewayPagamento\Registro\RegistroGatewayPagamentoInterface;
use BFOS\PagamentoBundle\Model\FormaPagamentoInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ConstrutorFormFormaPagamentoListener implements EventSubscriberInterface
{
    /**
     * @var RegistroGatewayPagamentoInterface $registro
     */
    protected $registro;

    /**
     * @var FormFactoryInterface $factory
     */
    protected $factory;

    public function __construct(RegistroGatewayPagamentoInterface $registro, FormFactoryInterface $factory)
    {
        $this->registro = $registro;
        $this->factory = $factory;
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
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
         * @var FormaPagamentoInterface $formaPagto
         */
        $formaPagto = $event->getData();

        if (null === $formaPagto || null === $formaPagto->getGatewayPagamento()) {
            return;
        }

        $this->adicionaConfiguracaoDosCampos(
            $event->getForm(),
            $formaPagto->getGatewayPagamento(),
            $formaPagto->getConfiguracao()
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

        if (empty($data) || !array_key_exists('gatewayPagamento', $data)) {
            return;
        }

        $this->adicionaConfiguracaoDosCampos($event->getForm(), $data['gatewayPagamento']);
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
        $gateway = $this->registro->get($identificador);

        if (true !== $gateway->ehConfiguravel()) {
            return;
        }

        /** @var FormInterface $configuracaoDoCampo */
        $configuracaoDoCampo = $this->factory->createNamed(
            'configuracao',
            $gateway->getConfiguracaoFormType(),
            $data,
            array('label' => 'bfos_pagamento.form.configuracao')
        );

        $form->add($configuracaoDoCampo);
    }

}
