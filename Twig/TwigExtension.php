<?php

namespace BFOS\PagamentoBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use BFOS\PagamentoBundle\Utils\ParcelamentoConfiguracao;
use BFOS\PagamentoBundle\Utils\ParcelamentoUtils;

class TwigExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    private $container;

    /**
     * @var \Twig_Environment
     */
    protected $env;

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->env = $environment;
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'bfos_pagamento_opcoes_parcelamento'    => new \Twig_Function_Method($this, 'opcoesParcelamento', array('is_safe' => array('html'))),
        );
    }


    public function opcoesParcelamento(ParcelamentoConfiguracao $configuracao, $valor, $opcoes = array())
    {
        $defaults = array(
            'colunas' => 2,
            'mostrarParcelas' => array()
        );
        $opcoes = array_merge($defaults, $opcoes);
        $colunas = $opcoes['colunas'];
        $quantidadePorColuna = round($configuracao->getQuantidadeMaximaParcelas()/$colunas);

        $opcoesParcelamento = ParcelamentoUtils::obterOpcoesDeParcelamento($configuracao, $valor);
        if(count($opcoes['mostrarParcelas'])){
            foreach ($opcoesParcelamento as $key=>$opcao) {
                if(!in_array($opcao['parcelas'], $opcoes['mostrarParcelas'])){
                    unset($opcoesParcelamento[$key]);
                }
            }

        }

        return $this->container->get('twig')->render('BFOSPagamentoBundle::opcoesParcelamento.html.twig',
            array('opcoesParcelamento'=>$opcoesParcelamento,
                'colunas'=>$colunas,
                'quantidadePorColuna'=>$quantidadePorColuna
            ));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bfos_pagamento';
    }
}


