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

interface AssistentePagamentoInterface
{
    /**
     * Este método criará um objeto InstrucaoPagamento
     * que poderá ser utilizado para realizar
     *
     * @param string|null $gatewayPagamento
     * @param float|null $valor
     *
     * @return InstrucaoPagamentoInterface
     */
    public function criarInstrucaoPagamento($gatewayPagamento = null, $valor = null);

    /**
     * Este método criará um objeto Pagamento para a InstrucaoPagamento
     * que poderá ser utilizado para realizar transações (aprovar e depositar)
     *
     * @param InstrucaoPagamentoInterface|integer $instrucaoPagamento
     * @param float                               $valor
     *
     * @return PagamentoInterface
     */
    public function criarPagamento($instrucaoPagamento, $valor);

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

    /**
     * Retorna o Pagamento pelo id.
     *
     * @param int $id
     *
     * @return PagamentoInterface
     *
     * @throws PagamentoNaoEncontradoException
     */
    public function getPagamento($id);
}
 