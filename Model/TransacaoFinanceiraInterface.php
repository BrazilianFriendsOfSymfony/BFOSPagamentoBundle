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
    const SITUACAO_FALHOU = 2;
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
     * Id da transacao.
     *
     * @return int
     */
    public function getId();

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

    /**
     * Retorna o tipo de transacao financeira.
     *
     * @return int
     */
    public function getTipoTransacao();

    /**
     * Define o tipo de transacao financeira.
     *
     * @param int $tipo
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setTipoTransacao($tipo);

    /**
     * Retorna o valor solicitado.
     *
     * @return float
     */
    public function getValorSolicitado();

    /**
     * Define o valor solicitado.
     *
     * @param float $valor
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setValorSolicitado($valor);

    /**
     * Retorna o valor processado na transação.
     *
     * @return float
     */
    public function getValorProcessado();

    /**
     * Define o valor processado na transação.
     *
     * @param float $valor
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setValorProcessado($valor);

    /**
     * Retorna o código que indica o porquê a situação da transação é o que é.
     *
     * @return string
     */
    public function getJustificativaSituacao();

    /**
     * Define o código que indica o porquê a situação da transação é o que é.
     *
     * @param string $justificativa
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setJustificativaSituacao($justificativa);

    /**
     * Retorna o número de referencia.
     *
     * @return string
     */
    public function getReferencia();

    /**
     * Define o número de referencia.
     *
     * @param string $referencia
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setReferencia($referencia);

    /**
     * Retorna o código da resposta da transação.
     *
     * @return string
     */
    public function getCodigoResposta();

    /**
     * Define o código da resposta da transação.
     *
     * @param string $codigo
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setCodigoResposta($codigo);

    /**
     * Retorna o código de rastreamento da transação (tracking id).
     *
     * @return string
     */
    public function getIdRastreamento();

    /**
     * Define o código de rastreamento da transação (tracking id).
     *
     * @param string $id
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setIdRastreamento($id);

    /**
     * Retorna os dados adicionais, geralmente ligado a um método de pagamento específico.
     *
     * @return DadosAdicionaisInterface
     */
    public function getDadosAdicionais();

    /**
     * Define os dados adicionais, geralmente ligado a um método de pagamento específico.
     *
     * @param DadosAdicionaisInterface $dados
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setDadosAdicionais($dados);

    /**
     * Define a data da última atualização do registro.
     *
     * @param \DateTime $atualizadoEm
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setAtualizadoEm($atualizadoEm);

    /**
     * Retorna a data da última atualização do registro.
     *
     * @return \DateTime
     */
    public function getAtualizadoEm();

    /**
     * Define a data de criação do registro.
     *
     * @param \DateTime $criadoEm
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setCriadoEm($criadoEm);

    /**
     * Retorna a data de criação do registro.
     *
     * @return \DateTime
     */
    public function getCriadoEm();
}
