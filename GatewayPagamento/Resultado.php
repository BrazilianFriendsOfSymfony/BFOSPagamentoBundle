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

namespace BFOS\PagamentoBundle\GatewayPagamento;


use BFOS\PagamentoBundle\GatewayPagamento\Exception\GatewayPagamentoException;
use BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface;
use BFOS\PagamentoBundle\Model\PagamentoInterface;
use BFOS\PagamentoBundle\Model\TransacaoFinanceiraInterface;

class Resultado implements ResultadoInterface
{

    protected $situacao;
    protected $exception;
    protected $justificativaSituacao;
    protected $instrucaoPagamento;
    protected $pagamento;
    protected $transacao;
    protected $recuperavel;

    /**
     * @inheritdoc
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * @inheritdoc
     */
    public function setException(GatewayPagamentoException $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @inheritdoc
     */
    public function setJustificativaSituacao($justificativa)
    {
        $this->justificativaSituacao = $justificativa;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getJustificativaSituacao()
    {
        return $this->justificativaSituacao;
    }

    /**
     * @inheritdoc
     */
    public function getInstrucaoPagamento()
    {
        return $this->instrucaoPagamento;
    }

    /**
     * @inheritdoc
     */
    public function setInstrucaoPagamento(InstrucaoPagamentoInterface $instrucao)
    {
        $this->instrucaoPagamento = $instrucao;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPagamento()
    {
        return $this->pagamento;
    }

    /**
     * @inheritdoc
     */
    public function setPagamento(PagamentoInterface $pagamento)
    {
        $this->pagamento = $pagamento;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTransacao()
    {
        return $this->transacao;
    }

    /**
     * @inheritdoc
     */
    public function setTransacao(TransacaoFinanceiraInterface $transacao)
    {
        $this->transacao = $transacao;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function ehRecuperavel()
    {
        return $this->recuperavel;
    }

    /**
     * @inheritdoc
     */
    public function setRecuperavel($boolean)
    {
        $this->recuperavel = $boolean;
        return $this;
    }
}
