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


interface TransacaoFinanceiraInterface
{
    const SITUACAO_CANCELADA = 1;
    const SITUACAO_FALHADA = 2;
    const SITUACAO_NOVA = 3;
    const SITUACAO_PENDENTE = 4;
    const SITUACAO_CONCLUIDA_COM_SUCCESSO = 5;

    const TIPO_TRANSACAO_APROVACAO = 1;
    const TIPO_TRANSACAO_APROVACAO_E_DEPOSITO = 2;
    const TIPO_TRANSACAO_CREDITO = 3;
    const TIPO_TRANSACAO_DEPOSITO = 4;
    const TIPO_TRANSACAO_ESTORNO_APROVACAO = 5;
    const TIPO_TRANSACAO_ESTORNO_CREDITO = 6;
    const TIPO_TRANSACAO_ESTORNO_DEPOSITO = 7;

    /**
     * Retorna a situação atual da transação.
     *
     * @return int
     */
    public function getSituacao();

    /**
     * Define a situação atual da transação.
     *
     * @param int $situacao
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setSituacao($situacao);

    /**
     * Retorna o pagamento a qual a transação pertence.
     *
     * @return PagamentoInterface
     */
    public function getPagamento();

    /**
     * Define o pagamento a qual a transação pertence.
     *
     * @param PagamentoInterface $pagamento
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setPagamento(PagamentoInterface $pagamento);
}
