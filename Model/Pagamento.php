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


class Pagamento implements PagamentoInterface
{
    protected $id;
    protected $valorAprovado;
    protected $valorAprovando;
    protected $valorDepositando;
    protected $valorDepositado;
    protected $dataVencimento;
    protected $instrucaoPagamento;
    protected $situacao;
    protected $valorEsperado;
    protected $precisaDeAtencao;
    protected $vencido;
    protected $transacoes;

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


    public function getValorAprovado()
    {
        return $this->valorAprovado;
    }

    public function getTransacaoDeAprovacao()
    {
        // TODO: Implement getTransacaoDeAprovacao() method.
    }

    public function getValorAprovando()
    {
        // TODO: Implement getValorAprovando() method.
    }

    public function getValorDepositado()
    {
        // TODO: Implement getValorDepositado() method.
    }

    public function getValorDepositando()
    {
        // TODO: Implement getValorDepositando() method.
    }

    public function getTransacoesDeDeposito()
    {
        // TODO: Implement getTransacoesDeDeposito() method.
    }

    public function setValorAprovado($valor)
    {
        // TODO: Implement setValorAprovado() method.
    }

    public function setValorAprovando($valor)
    {
        // TODO: Implement setValorAprovando() method.
    }

    public function setValorDepositado($valor)
    {
        // TODO: Implement setValorDepositado() method.
    }

    public function setValorDepositando($valor)
    {
        // TODO: Implement setValorDepositando() method.
    }

    public function getDataVencimento()
    {
        // TODO: Implement getDataVencimento() method.
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
    public function setInstrucaoPagamento(InstrucaoPagamentoInterface $instrucaoPagamento)
    {
        $this->instrucaoPagamento = $instrucaoPagamento;
        return $this;
    }


    public function getTransacaoPendente()
    {
        /** @var TransacaoFinanceiraInterface $transacao */
        foreach ($this->transacoes as $transacao) {
            if (TransacaoFinanceiraInterface::SITUACAO_PENDENTE === $transacao->getSituacao()) {
                return $transacao;
            }
        }

        return null;
    }

    public function getSituacao()
    {
        // TODO: Implement getSituacao() method.
    }

    /**
     * @inheritdoc
     */
    public function getValorEsperado()
    {
        return $this->valorEsperado;
    }

    /**
     * @inheritdoc
     */
    public function setValorEsperado($valor)
    {
        $this->valorEsperado = $valor;
        return $this;
    }


    public function temTransacaoPendente()
    {
        // TODO: Implement temTransacaoPendente() method.
    }

    public function precisaDeAtencao()
    {
        // TODO: Implement precisaDeAtencao() method.
    }

    public function estahVencido()
    {
        // TODO: Implement estahVencido() method.
    }

    public function setPrecisaDeAtencao($boolean)
    {
        // TODO: Implement setPrecisaDeAtencao() method.
    }

    public function setDataVencimento(\DateTime $date)
    {
        // TODO: Implement setDataVencimento() method.
    }

    public function setVencido($boolean)
    {
        // TODO: Implement setVencido() method.
    }

    public function setSituacao($situacao)
    {
        // TODO: Implement setSituacao() method.
    }


}
