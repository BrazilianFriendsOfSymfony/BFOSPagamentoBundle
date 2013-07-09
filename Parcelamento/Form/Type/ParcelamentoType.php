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

namespace BFOS\PagamentoBundle\Parcelamento\Form\Type;

use BFOS\PagamentoBundle\Utils\ParcelamentoConfiguracao;
use BFOS\PagamentoBundle\Utils\ParcelamentoUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ParcelamentoType extends AbstractType
{
    protected $configuracao;
    protected $valor;

    public function __construct(ParcelamentoConfiguracao $configuracao, $valor)
    {
        $this->configuracao = $configuracao;
        $this->valor = $valor;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opcoes = ParcelamentoUtils::obterOpcoesDeParcelamento($this->configuracao, $this->valor);
        $choices = ParcelamentoUtils::choices($opcoes);
        $builder->add('parcelas', 'choice', array('choices'=>$choices));
    }

    public function getName()
    {
        return 'bfos_pagamento_parcelas_type';
    }
}
