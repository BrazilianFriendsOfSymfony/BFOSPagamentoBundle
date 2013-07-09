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


use BFOS\PagamentoBundle\GatewayPagamento\Exception\FuncaoNaoSuportadaException;
use BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface;
use BFOS\PagamentoBundle\Model\TransacaoFinanceiraInterface;

abstract class AbstractGatewayPagamento implements GatewayPagamentoInterface
{
    /**
     * @inheritdoc
     */
    public function aprovar(TransacaoFinanceiraInterface $transaccao, $jahTentada)
    {
        throw new FuncaoNaoSuportadaException('Método aprovar() não é suportado pelo meio de pagamento');
    }

    /**
     * @inheritdoc
     */
    public function aprovarEDepositar(TransacaoFinanceiraInterface $transacao, $jahTentada)
    {
        throw new FuncaoNaoSuportadaException('Método aprovarEDepositar() não é suportado pelo meio de pagamento');
    }

    /**
     * @inheritdoc
     */
    public function depositar(TransacaoFinanceiraInterface $transacao, $jahTentada)
    {
        throw new FuncaoNaoSuportadaException('Método depositar() não é suportado pelo meio de pagamento');
    }

    /**
     * @inheritdoc
     */
    public function validarInstrucaoPagamento(InstrucaoPagamentoInterface $instrucaoPagamento)
    {
        throw new FuncaoNaoSuportadaException('Método validarInstrucaoPagamento() não é suportado pelo meio de pagamento');
    }

    /**
     * @inheritdoc
     */
    public function getConfiguracaoCheckoutFormType()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function ehConfiguravelNoCheckout()
    {
        return false;
    }


}
