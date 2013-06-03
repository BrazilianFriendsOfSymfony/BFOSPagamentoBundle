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

namespace BFOS\PagamentoBundle\MeioPagamento;

use BFOS\PagamentoBundle\MeioPagamento\Exception\InstrucaoPagamentoInvalidaException;
use BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface;
use BFOS\PagamentoBundle\Model\TransacaoFinanceiraInterface;


/**
 * Este interface deve ser implementada por todos os meios de pagamentos gerenciados pelo bundle.
 *
 * Se a transação não fizer sentido para o meio de pagamento implementado,
 * você pode disparar a exceção FuncaoNaoSuportadaException
 *
 * @author Paulo Ribeiro <paulo@duocriativa.com.br>
 */
interface MeioPagamentoInterface
{
    /**
     * Executa uma transação de aprovação.
     *
     * Em uma aprovação, a quantia é reservada mas não é realmente transferida.
     * Uma transação de depósito subsequente deve ser realizada para que
     * a transferência seja feita.
     *
     * Um cenário típico seria pagamento por cartão de crédito onde a quantia
     * é primeiro autorizada.
     *
     * @param TransacaoFinanceiraInterface $transaccao
     * @param boolean $jahTentada Se esta é uma transação que já foi tentada anteriormente.
     * @return void
     */
    public function aprovar(TransacaoFinanceiraInterface $transaccao, $jahTentada);

    /**
     * This method executes a deposit transaction without prior approval
     * (aka "sale", or "authorization with capture" transaction).
     *
     * A typical use case for this method is an electronic check payments
     * where authorization is not supported. It can also be used to deposit
     * money in only one transaction, and thus saving processing fees for
     * another transaction.
     *
     * @param TransacaoFinanceiraInterface $transacao
     * @param boolean $jahTentada
     * @return void
     */
    public function aprovarEDepositar(TransacaoFinanceiraInterface $transacao, $jahTentada);

    /**
     * This method checks whether all required parameters exist in the given
     * PaymentInstruction, and whether they are syntactically correct.
     *
     * This method is meant to perform a fast parameter validation; no connection
     * to any payment back-end system should be made at this stage.
     *
     * In case, this method is not implemented. The PaymentInstruction will
     * be considered to be valid.
     *
     * @param InstrucaoPagamentoInterface $instrucaoPagamento
     * @throws InstrucaoPagamentoInvalidaException if the the PaymentInstruction is not valid
     * @return void
     */
    public function validarInstrucaoPagamento(InstrucaoPagamentoInterface $instrucaoPagamento);
}
