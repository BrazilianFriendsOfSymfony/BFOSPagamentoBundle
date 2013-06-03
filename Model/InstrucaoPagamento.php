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
    protected $referencia;
    protected $meioPagamento;
    protected $situacao;
    protected $valorTotal;
    protected $pagamentos;

    public function __construct()
    {
        $this->situacao = InstrucaoPagamentoInterface::SITUACAO_NOVA;
        $this->valorTotal = 0.0;
        $this->pagamentos = new ArrayCollection();
    }

    /**
     * @param string $meioPagamento
     * @return InstrucaoPagamentoInterface
     */
    public function setMeioPagamento($meioPagamento)
    {
        $this->meioPagamento = $meioPagamento;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMeioPagamento()
    {
        return $this->meioPagamento;
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
}
