<?php

namespace BFOS\PagamentoBundle\Form\Type;

use BFOS\PagamentoBundle\Form\Listener\ConstrutorFormFormaPagamentoCheckoutChoiceListener;
use BFOS\PagamentoBundle\FormaPagamento\GerenteFormaPagamentoInterface;
use BFOS\PagamentoBundle\GatewayPagamento\GatewayPagamentoInterface;
use BFOS\PagamentoBundle\GatewayPagamento\Registro\RegistroGatewayPagamentoInterface;
use BFOS\PagamentoBundle\Model\FormaPagamentoInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class GatewayPagamentoCheckoutChoiceType
 *
 * Este FormType foi idealizado para ser utilizado no processo de checkout
 * de uma loja virtual, onde o visitante pode escolher como quer pagar
 * e tem a oportunidade de inserir os dados referentes a cada gateway de
 * pagamento.
 *
 *
 * @package BFOS\PagamentoBundle\Form\Type
 */
class FormaPagamentoCheckoutChoiceType extends AbstractType
{
    protected $dataClass;

    /**
     * @var RegistroGatewayPagamentoInterface $registro
     */
    protected $registro;

    /**
     * @var array $formasPagamento
     */
    protected $formasPagamento;

    protected $gerente;

    public function __construct(RegistroGatewayPagamentoInterface $registro, GerenteFormaPagamentoInterface $gerente, $formasPagamento = null)
    {
//        $this->dataClass = $dataClass;
        $this->registro = $registro;
        if (is_null($formasPagamento)){
            $formasPagamento = $gerente->getAtivas();
        }
        $this->formasPagamento = $formasPagamento;
        $this->gerente = $gerente;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configuracoes = array();
        if(isset($options['configuracoes'])){
            $configuracoes = $options['configuracoes'];
        }
        $choices = array();
        /** @var FormaPagamentoInterface $forma */
        foreach ($this->formasPagamento as $forma) {
            $identGateway = $forma->getGatewayPagamento();
            $choices[$forma->getId()] = $this->registro->getEtiqueta($identGateway);
            unset($identGateway);
        }

        $builder->addEventSubscriber(
            new ConstrutorFormFormaPagamentoCheckoutChoiceListener($this->registro, $this->gerente, $builder->getFormFactory(), $configuracoes)
        );
        $builder
            ->add(
                    'formaPagamentoId',
                    'choice',
                    array(
                        'label' => 'bfos_pagamento.form.gateway',
                        'choices' => $choices,
                        'expanded' => true,
                        'attr' => array('class' => 'js_opcao_forma_pagamento')
                    )
                )
        ;

        $prototypes = array();
        $gateways = $this->registro->getTodos();

        /** @var GatewayPagamentoInterface $gateway */
        foreach ($gateways as $nome => $gateway) {
            if (isset($configuracoes[$nome]) && isset($configuracoes[$nome]['configuracao_checkout_form'])) {
                $prototypes[$nome] = $builder->create(
                                         'configuracao',
                                         $configuracoes[$nome]['configuracao_checkout_form'],
                                         array('label' => 'bfos_pagamento.form.configuracao_checkout')
                                     )->getForm();
            } elseif($gateway->ehConfiguravelNoCheckout()) {
                $prototypes[$nome] = $builder->create(
                                         'configuracao',
                                         $gateway->getConfiguracaoCheckoutFormType(),
                                         array('label' => 'bfos_pagamento.form.configuracao_checkout')
                                     )->getForm();
            }
        }

        $prototypesForma = array();
        foreach ($this->formasPagamento as $forma) {
            if (isset($prototypes[$forma->getGatewayPagamento()])) {
                $prototypesForma[$forma->getId()] = $prototypes[$forma->getGatewayPagamento()];
            }
        }

        $builder->setAttribute('prototypes', $prototypesForma);

    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->vars['prototypes'] = array();
        $this->vars['attr'] = array('class'=>'js_bfos_pagamento_forma_pagamento_checkout_choice');

        foreach ($form->getConfig()->getAttribute('prototypes') as $nome => $prototypes) {
            $view->vars['prototypes'][$nome] = $prototypes->createView($view);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        /*$resolver->setDefaults(array(
            'data_class' => $this->dataClass
        ));*/
        $resolver->setOptional(array('configuracoes'));
    }

    public function getName()
    {
        return 'bfos_pagamento_forma_pagamento_checkout_choice';
    }
}
