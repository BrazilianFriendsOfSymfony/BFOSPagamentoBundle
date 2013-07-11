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

use Doctrine\Common\Collections\Collection;

/**
 * Como deve ser uma instrução para recebimento de um montante.
 * Modela, por exemplo, o valor que deve ser recebido por um pedido feito.
 * Também deve conter as informações adicionais para que o pagamento possa ser processado.
 *
 * Interface InstrucaoRecebimentoInterface
 *
 * @package BFOS\PagamentoBundle\Model
 */
interface InstrucaoPagamentoInterface
{
    const SITUACAO_NOVA = 1;
    const SITUACAO_RECEBIDA = 2;
    const SITUACAO_VALIDA = 3;
    const SITUACAO_INVALIDA = 4;

    /**
     * Id
     *
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setId($id);

    /**
     * Retorna a situação da instrução de pagamento.
     *
     * @return int
     */
    public function getSituacao();

    /**
     * Define a situação da instrução de pagamento.
     *
     * @param int $situacao
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setSituacao($situacao);

    /**
     * Retorna o identificador do que se refere a instrução de pagamento.
     * Geralmente é número do pedido ou ordem de serviço.
     *
     * @return string
     */
    public function getReferencia();

    /**
     * Retorna o identificador do meio de pagamento utilizado.
     *
     * @return string
     */
    public function getGatewayPagamento();

    /**
     * Define o identificador do meio de pagamento utilizado.
     *
     * @param string $gatewayPagamento
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setGatewayPagamento($gatewayPagamento);

    /**
     * Valor total que deverá ser recebido.
     *
     * @return float
     */
    public function getValorTotal();

    /**
     * Define o valor total que deverá ser recebido.
     *
     * @param float $valorTotal
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setValorTotal($valorTotal);

    /**
     * Retorna a lista de pagamentos associados a instrução.
     *
     * @return Collection
     */
    public function getPagamentos();

    /**
     * Retorna a transação pendente.
     *
     * @return TransacaoFinanceiraInterface
     */
    public function getTransacaoPendente();

    /**
     * Indica se há transação pendente.
     *
     * @return boolean
     */
    public function temTransacaoPendente();

    /**
     * Valor que em aprovação. Utilizado durante o processo de aprovação.
     *
     * @return float
     */
    public function getValorAprovando();

    /**
     * Define o valor em aprovação.
     *
     * @param float $valor
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setValorAprovando($valor);

    /**
     * Valor que foi aprovado até agora. Soma parcial é permitida.
     *
     * @return float
     */
    public function getValorAprovado();

    /**
     * Define o valor já aprovado.
     *
     * @param float $valor
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setValorAprovado($valor);

    /**
     * Valor que já foi depositado (autorizado e transferido, com recebimento garantido).
     *
     * @return float
     */
    public function getValorDepositado();

    /**
     * Define o valor que já foi depositado.
     *
     * @param float $valor
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setValorDepositado($valor);

    /**
     * Valor que está sendo depositado.
     *
     * @return float
     */
    public function getValorDepositando();

    /**
     * Define o valor que está sendo depositado.
     *
     * @param float $valor
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setValorDepositando($valor);

    /**
     * Retorna a moeda utilizada na transação. Por enquanto o padrão é BRL.
     *
     * @return string
     */
    public function getMoeda();

    /**
     * Define a moeda.
     *
     * @param string $moeda
     *
     * @return InstrucaoPagamentoInterface
     */
    public function setMoeda($moeda);

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
