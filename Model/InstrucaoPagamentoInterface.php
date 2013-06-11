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
     * Retorna a situação atual da instrução de pagamento.
     *
     * @return int
     */
    public function getSituacao();

    /**
     * Valor total que deverá ser recebido.
     *
     * @return float
     */
    public function getValorTotal();

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
}
