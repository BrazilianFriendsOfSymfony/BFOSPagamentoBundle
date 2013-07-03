<?php

namespace BFOS\PagamentoBundle\Form\Type;

use BFOS\PagamentoBundle\Form\Listener\ConstrutorFormFormaPagamentoListener;
use BFOS\PagamentoBundle\GatewayPagamento\GatewayPagamentoInterface;
use BFOS\PagamentoBundle\GatewayPagamento\Registro\RegistroGatewayPagamentoInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormaPagamentoType extends AbstractType
{
    protected $dataClass;

    /**
     * @var RegistroGatewayPagamentoInterface $registro
     */
    protected $registro;

    public function __construct($dataClass, RegistroGatewayPagamentoInterface $registro)
    {
        $this->dataClass = $dataClass;
        $this->registro = $registro;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(
            new ConstrutorFormFormaPagamentoListener($this->registro, $builder->getFormFactory())
        );
        $builder
            ->add('ativo')
            ->add(
                    'gatewayPagamento',
                    'bfos_pagamento_gateway_pagamento_choice',
                    array(
                        'label' => 'bfos_pagamento.form.gateway',
                    )
                )
        ;

        $prototypes = array();
        $gateways = $this->registro->getTodos();

        /** @var GatewayPagamentoInterface $gateway */
        foreach ($gateways as $nome => $gateway) {
            $prototypes[$nome] = $builder->create(
                                     'configuracao',
                                     $gateway->getConfiguracaoFormType(),
                                     array('label' => 'bfos_pagamento.form.configuracao')
                                 )->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);

    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->vars['prototypes'] = array();

        foreach ($form->getConfig()->getAttribute('prototypes') as $nome => $prototypes) {
            $view->vars['prototypes'][$nome] = $prototypes->createView($view);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass
        ));
    }

    public function getName()
    {
        return 'bfos_pagamento_forma_pagamento_type';
    }
}
