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


use BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface;
use BFOS\PagamentoBundle\Model\PagamentoInterface;

interface GerenteGatewayPagamentoInterface
{
    /**
     * Este método criará um objeto Pagamento para a InstrucaoPagamento
     * que poderá ser utilizado para realizar transações (aprovar e depositar)
     *
     * @param integer                 $instrucaoPagamentoId
     * @param float                   $valor
     * @param PagamentoInterface|null $pagamento O objeto base utilizado para criar o pagamento.
     *                                           Útil quando a implementação difere do oferecido pelo bundle.
     *
     * @return PagamentoInterface
     */
    public function criarPagamento($instrucaoPagamentoId, $valor, $pagamento = null);

    /**
     * Este método executa uma transação aprovaEDeposita contra um pagamento.
     * (transação de "venda" ou "autorização com captura")
     *
     * The implementation will ensure that:
     * - PaymentInstruction's state is VALID
     * - Payment's state is NEW, or APPROVING
     * - PaymentInstruction only ever has one pending transaction
     *
     * In addition, if the Payment is NEW, the implementation will ensure:
     * - Payment's target amount is greater or equal to the requested amount
     *
     * In addition, if the Payment is APPROVING, the implementation will ensure:
     * - Payment's approving amount is equal to the requested amount
     * - Payment's depositing amount is equal to the requested amount
     *
     * For NEW payments, the implementation will:
     * - set Payment's state to APPROVING
     * - set Payment's approving amount to requested amount
     * - set Payment's depositing amount to requested amount
     * - increase PaymentInstruction's approving amount by requested amount
     * - increase PaymentInstruction's depositing amount by requested amount
     * - delegate the new transaction to an appropriate plugin implementation
     *
     * For APPROVING payments, the implementation will:
     * - delegate the pending transaction to an appropriate plugin implementation
     *
     * On successful response, the implementation will:
     * - set Payment's state to APPROVED
     * - set Payment's approving amount to zero
     * - set Payment's depositing amount to zero
     * - decrease PaymentInstruction's approving amount by requested amount
     * - decrease PaymentInstruction's depositing amount by requested amount
     * - set Payment's approved amount to processed amount
     * - set Payment's deposited amount to processed amount
     * - increase PaymentInstruction's approved amount by processed amount
     * - increase PaymentInstruction's deposited amount by processed amount
     * - set reason code to PluginInterface::REASON_CODE_SUCCESS
     *
     * On unsuccessful response, the implementation will:
     * - set Payment's state to FAILED
     * - set Payment's approving amount to zero
     * - set Payment's depositing amount to zero
     * - decrease PaymentInstruction's approving amount by requested amount
     * - decrease PaymentInstruction's depositing amount by requested amount
     *
     * On TimeoutException (including child classes), the implementation will:
     * - keep Payment's state unchanged
     * - keep Payment's approving/depositing amounts unchanged
     * - keep PaymentInstruction's approving/depositing amounts unchanged
     * - set reason code to PluginInterface::REASON_CODE_TIMEOUT
     *
     * @param integer $pagamentoId
     * @param float $quantia
     * @return Resultado
     */
    public function aprovaEDeposita($pagamentoId, $quantia);

    /**
     * Retorna a InstrucaoPagamento pelo id.
     *
     * @param int  $id
     * @param bool $mascararDadosSensiveis
     *
     * @throws Exception\InstrucaoPagamentoNaoEncontradaException
     * @return InstrucaoPagamentoInterface
     */
    public function getInstrucaoPagamento($id, $mascararDadosSensiveis = true);
}
