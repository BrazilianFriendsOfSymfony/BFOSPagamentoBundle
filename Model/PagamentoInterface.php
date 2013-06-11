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


interface PagamentoInterface
{
    const SITUACAO_NOVO = 1;
    const SITUACAO_APROVANDO = 2;
    const SITUACAO_APROVADO = 3;
    const SITUACAO_CANCELADO = 4;
    const SITUACAO_EXPIRADO = 5;
    const SITUACAO_FALHOU = 6;
    const SITUACAO_DEPOSITANDO = 7;
    const SITUACAO_DEPOSITADO = 8;

    /**
     * Id.
     *
     * @return int
     */
    public function getId();

    /**
     * Define o id.
     *
     * @param int $id
     *
     * @return PagamentoInterface
     */
    public function setId($id);

    public function getValorAprovado();

    public function getTransacaoDeAprovacao();

    public function getValorAprovando();

    public function getValorDepositado();

    public function getValorDepositando();

    public function getTransacoesDeDeposito();

    public function setValorAprovado($valor);

    public function setValorAprovando($valor);

    public function setValorDepositado($valor);

    public function setValorDepositando($valor);

    public function getDataVencimento();

    /**
     * Retorna a InstrucaoPagamento a qual o pagamento pertence.
     *
     * @return InstrucaoPagamentoInterface
     */
    public function getInstrucaoPagamento();

    /**
     * Define a InstrucaoPagamento a qual o pagamento pertence.
     *
     * @param InstrucaoPagamentoInterface $instrucaoPagamento
     *
     * @return PagamentoInterface
     */
    public function setInstrucaoPagamento(InstrucaoPagamentoInterface $instrucaoPagamento);

    public function getTransacaoPendente();

    public function getSituacao();

    /**
     * Retorna o valor esperado por esse pagamento.
     *
     * @return float
     */
    public function getValorEsperado();

    /**
     * Define o valor esperado por esse pagamento.
     *
     * @param float $valor
     *
     * @return PagamentoInterface
     */
    public function setValorEsperado($valor);

    public function temTransacaoPendente();

    public function precisaDeAtencao();

    public function estahVencido();

    public function setPrecisaDeAtencao($boolean);

    public function setDataVencimento(\DateTime $date);

    public function setVencido($boolean);

    public function setSituacao($situacao);
}
