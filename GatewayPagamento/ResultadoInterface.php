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

interface ResultadoInterface
{
    const SITUACAO_FALHOU = 1;
    const SITUACAO_PENDENTE = 2;
    const SITUACAO_SUCESSO = 3;
    const SITUACAO_DESCONHECIDA = 4;

    /**
     * @param int $situacao
     * @return ResultadoInterface
     */
    public function setSituacao($situacao);

    /**
     * @return int
     */
    public function getSituacao();

    /**
     * @param GatewayPagamentoException $exception
     * @return ResultadoInterface
     */
    public function setException(GatewayPagamentoException $exception);

    /**
     * @return \Exception
     */
    public function getException();

    /**
     * Define o código que indica o porquê a situação da transação é o que é.
     *
     * @param string $justificativa
     *
     * @return ResultadoInterface
     */
    public function setJustificativaSituacao($justificativa);

    /**
     * Retorna o código que indica o porquê a situação da transação é o que é.
     *
     * @return string
     */
    public function getJustificativaSituacao();

    /**
     * Retorna a instrução de pagamento a que o resultado se refere.
     *
     * @return InstrucaoPagamentoInterface
     */
    public function getInstrucaoPagamento();

    /**
     * Define a instrução de pagamento a que o resultado se refere.
     *
     * @param InstrucaoPagamentoInterface $instrucao
     *
     * @return ResultadoInterface
     */
    public function setInstrucaoPagamento(InstrucaoPagamentoInterface $instrucao);

    /**
     * Retorna o pagamento a que o resultado se refere.
     *
     * @return PagamentoInterface
     */
    public function getPagamento();

    /**
     * Define o pagamento a que o resultado se refere.
     *
     * @param PagamentoInterface $pagamento
     *
     * @return ResultadoInterface
     */
    public function setPagamento(PagamentoInterface $pagamento);

    /**
     * Retorna a transação a que o resultado se refere.
     *
     * @return TransacaoFinanceiraInterface
     */
    public function getTransacao();

    /**
     * Retorna a transação a que o resultado se refere.
     *
     * @param TransacaoFinanceiraInterface $transacao
     *
     * @return ResultadoInterface
     */
    public function setTransacao(TransacaoFinanceiraInterface $transacao);

    /**
     * Indica que o resultado é recuperável.
     *
     * @return boolean
     */
    public function ehRecuperavel();

    /**
     * Define se o resultado é recuperável.
     *
     * @param boolean $boolean
     *
     * @return ResultadoInterface
     */
    public function setRecuperavel($boolean);
}
