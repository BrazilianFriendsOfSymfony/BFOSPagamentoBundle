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
     * @return InstrucaoPagamentoInterface
     */
    public function setGatewayPagamento($gatewayPagamento);

    /**
     * Retorna o total do valor já aprovado.
     *
     * @return float
     */
    public function getValorAprovado();

    /**
     * Define o total do valor já aprovado.
     *
     * @param float $valorAprovado
     *
     * @return PagamentoInterface
     */
    public function setValorAprovado($valorAprovado);

    /**
     * Retorna a transação que aprovou o pagamento.
     *
     * @return TransacaoFinanceiraInterface|null
     */
    public function getTransacaoDeAprovacao();

    /**
     * Retorna o total do valor tentando ser aprovado.
     *
     * @return float
     */
    public function getValorAprovando();

    /**
     * Define o total do valor tentando ser aprovado.
     *
     * @param float $valorAprovando
     *
     * @return PagamentoInterface
     */
    public function setValorAprovando($valorAprovando);

    /**
     * Retorna o total do valor já depositado.
     *
     * @return float
     */
    public function getValorDepositado();

    /**
     * Define o total do valor já depositado.
     *
     * @param float $valorDepositado
     *
     * @return PagamentoInterface
     */
    public function setValorDepositado($valorDepositado);

    /**
     * Retorna o valor tentando ser depositado.
     *
     * @return float
     */
    public function getValorDepositando();

    /**
     * Define o valor tentando ser depositado.
     *
     * @param float $valorDepositando
     *
     * @return PagamentoInterface
     */
    public function setValorDepositando($valorDepositando);

    /**
     * @return Collection
     */
    public function getTransacoesDeDeposito();

    /**
     * Data de vencimento para concluir o pagamento.
     *
     * @return \DateTime
     */
    public function getDataVencimento();

    /**
     * Define a data limite para concluir o pagamento.
     *
     * @param \DateTime $data
     *
     * @return PagamentoInterface
     */
    public function setDataVencimento(\DateTime $data);

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

    /**
     * @return TransacaoFinanceiraInterface
     */
    public function getTransacaoPendente();

    /**
     * Retorna a situação do pagamento.
     *
     * @return int
     */
    public function getSituacao();

    /**
     * Retorna o label da situação do pagamento.
     *
     * @return string
     */
    public function getSituacaoLabel();

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

    /**
     * Indica que se alguma transação pendente.
     *
     * @return boolean
     */
    public function temTransacaoPendente();

    /**
     * @return boolean
     */
    public function precisaDeAtencao();

    /**
     * Indica se o pagamento está vencido.
     *
     * @return boolean
     */
    public function getVencido();

    /**
     * Indica se o pagamento está vencido.
     *
     * @param boolean $boolean
     *
     * @return PagamentoInterface
     */
    public function setVencido($boolean);

    public function setPrecisaDeAtencao($boolean);

    public function setSituacao($situacao);

    /**
     * Adiciona uma transação ao pagamento.
     *
     * @param TransacaoFinanceiraInterface $transacao
     *
     * @return PagamentoInterface
     */
    public function adicionarTransacao(TransacaoFinanceiraInterface $transacao);

    /**
     * Indica se a transação já foi adicionada ao pagamento.
     *
     * @param TransacaoFinanceiraInterface $transacao
     *
     * @return PagamentoInterface
     */
    public function jahAdicionouTransacao(TransacaoFinanceiraInterface $transacao);

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

}
