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


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class InstrucaoPagamento implements InstrucaoPagamentoInterface
{
    protected $id;
    protected $referencia;
    protected $gatewayPagamento;
    protected $situacao;
    protected $valorTotal;
    protected $valorAprovando;
    protected $valorAprovado;
    protected $valorDepositado;
    protected $pagamentos;

    public function __construct()
    {
        $this->situacao = InstrucaoPagamentoInterface::SITUACAO_NOVA;
        $this->valorTotal = 0.0;
        $this->pagamentos = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param string $gatewayPagamento
     * @return InstrucaoPagamentoInterface
     */
    public function setGatewayPagamento($gatewayPagamento)
    {
        $this->gatewayPagamento = $gatewayPagamento;
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
     * @param string $referencia
     * @return InstrucaoPagamentoInterface
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * @param Collection $pagamentos
     * @return InstrucaoPagamentoInterface
     */
    public function setPagamentos($pagamentos)
    {
        $this->pagamentos = $pagamentos;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPagamentos()
    {
        return $this->pagamentos;
    }

    /**
     * @inheritdoc
     */
    public function getTransacaoPendente()
    {
        /** @var PagamentoInterface $pagamento */
        foreach ($this->pagamentos as $pagamento) {
            if (null !== $transaction = $pagamento->getTransacaoPendente()) {
                return $transaction;
            }
        }

        /*foreach ($this->credits as $credit) {
            if (null !== $transaction = $credit->getPendingTransaction()) {
                return $transaction;
            }
        }*/

        return null;
    }


    /**
     * @param int $situacao
     * @return InstrucaoPagamentoInterface
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
     * @param float $valorTotal
     * @return InstrucaoPagamentoInterface
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * @inheritdoc
     */
    public function setValorAprovado($valorAprovado)
    {
        $this->valorAprovado = $valorAprovado;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValorAprovado()
    {
        return $this->valorAprovado;
    }

    /**
     * @inheritdoc
     */
    public function setValorAprovando($valorAprovando)
    {
        $this->valorAprovando = $valorAprovando;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValorAprovando()
    {
        return $this->valorAprovando;
    }

    /**
     * @inheritdoc
     */
    public function setValorDepositado($valorDepositado)
    {
        $this->valorDepositado = $valorDepositado;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValorDepositado()
    {
        return $this->valorDepositado;
    }


}
