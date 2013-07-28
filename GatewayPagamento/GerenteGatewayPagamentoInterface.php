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


use BFOS\PagamentoBundle\Exception\PagamentoNaoEncontradoException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\InstrucaoPagamentoInvalidaException;
use BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface;
use BFOS\PagamentoBundle\Model\PagamentoInterface;

interface GerenteGatewayPagamentoInterface
{
    /**
     * Este método executa uma transação de aprovação de um pagamento.
     *
     * A implementação garante que:
     * - A situação da InstrucaoPagamento é VALIDA
     * - A situação do Pagamento é NOVO ou APROVANDO
     * - O valor esperado do Pagamento é correto
     * - A InstrucaoPagamento não tem transação pendente se o Pagamento é NOVO
     *
     * If Payment's state is NEW, the implementation will:
     * - change Payment's state to APPROVING
     * - set approving amount in Payment
     * - increase approving amount in PaymentInstruction
     * - delegate the transaction to an appropriate plugin implementation
     *
     * If Payment's state is APPROVING, the implementation will:
     * - delegate the existing approve transaction to an appropriate plugin implementation
     *
     * On successful response, the implementation will:
     * - reset the approving amount in Payment to zero
     * - decrease the approving amount in PaymentInstruction by the requested amount
     * - increase the approved amount in PaymentInstruction by the processed amount
     * - change Payment's state to APPROVED
     *
     * On unsuccessful response, the implementation will:
     * - reset the approving amount in Payment to zero
     * - change Payment's state to FAILED
     * - decrease the approving amount in PaymentInstruction by the requested amount
     *
     * On TimeoutException (including child classes), the implementation will:
     * - keep approving amounts in Payment, and PaymentInstruction unchanged
     * - keep Payment's state unchanged
     * - set reasonCode to PluginInterface::REASON_CODE_TIMEOUT
     *
     * // FIXME: How do we process cases where a user interaction is required, e.g. PayPal?
     *           This probably requires an additional exception which is processed similar to a PluginTimeoutException
     *
     * On any exception not mentioned above, the implementation will:
     * - rollback the transaction
     * - not persist any changes in the database
     *
     * @throws InstrucaoPagamentoInvalidaException Se a InstrucaoPagamento estiver em situação irregular
     *
     * @param PagamentoInterface|integer $pagamento
     * @param float $valor
     *
     * @return ResultadoInterface
     */
    public function aprova($pagamento, $valor);

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
     * @param PagamentoInterface|integer $pagamento
     * @param float $valor
     *
     * @throws InstrucaoPagamentoInvalidaException
     *
     * @return ResultadoInterface
     */
    public function aprovaEDeposita($pagamento, $valor);

}
