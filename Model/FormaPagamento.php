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

namespace BFOS\PagamentoBundle\Model;


class FormaPagamento implements FormaPagamentoInterface
{
    /**
     * Identificador do método de entrega
     *
     * @var mixed
     */
    protected $id;

    /**
     * O método está ativo?
     *
     * @var Boolean
     */
    protected $ativo;
    /**
     * Calculadora nome.
     *
     * @var string
     */
    protected $gatewayPagamento;
    /**
     * Todas as configurações extras.
     *
     * @var array
     */
    protected $configuracao;
    /**
     * Data de criação.
     *
     * @var \DateTime
     */
    protected $criadoEm;
    /**
     * Data da última atualização.
     *
     * @var \DateTime
     */
    protected $atualizadoEm;

    /**
     * Construtor.
     */
    public function __construct()
    {
        $this->ativo = true;
        $this->criadoEm = new \DateTime();
        $this->configuracao = array();
    }

    /**
     * @inheritdoc
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * @inheritdoc
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAtualizadoEm()
    {
        return $this->atualizadoEm;
    }

    /**
     * @inheritdoc
     */
    public function setAtualizadoEm($atualizadoEm)
    {
        $this->atualizadoEm = $atualizadoEm;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguracao()
    {
        return $this->configuracao;
    }

    /**
     * @inheritdoc
     */
    public function setConfiguracao($configuracao)
    {
        $this->configuracao = $configuracao;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCriadoEm()
    {
        return $this->criadoEm;
    }

    /**
     * @inheritdoc
     */
    public function setCriadoEm($criadoEm)
    {
        $this->criadoEm = $criadoEm;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGatewayPagamento()
    {
        return $this->gatewayPagamento;
    }

    /**
     * @inheritdoc
     */
    public function setGatewayPagamento($gatewayPagamento)
    {
        $this->gatewayPagamento = $gatewayPagamento;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


}
