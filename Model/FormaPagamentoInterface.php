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


interface FormaPagamentoInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return FormaPagamentoInterface
     */
    public function setId($id);

    /**
     * Indica se a forma de pagamento está ativa.
     *
     * @return boolean
     */
    public function getAtivo();

    /**
     * Define se a forma de pagamento está ativa.
     *
     * @param boolean $ativo
     *
     * @return FormaPagamentoInterface
     */
    public function setAtivo($ativo);

    /**
     * Retorna o identificador do gateway de pagamento.
     *
     * @return string
     */
    public function getGatewayPagamento();

    /**
     * Define o identificador do gateway de pagamento.
     *
     * @param string $gateway
     *
     * @return FormaPagamentoInterface
     */
    public function setGatewayPagamento($gateway);

    /**
     * Retorna as configurações da forma de pagamento.
     *
     * @return array
     */
    public function getConfiguracao();

    /**
     * Define as configurações da forma de pagamento.
     *
     * @param $configuracao
     *
     * @return FormaPagamentoInterface
     */
    public function setConfiguracao($configuracao);

    /**
     * Retorna quando o registro foi criado.
     *
     * @return \DateTime
     */
    public function getCriadoEm();

    /**
     * Define quando o registro foi criado.
     *
     * @param \DateTime $data
     *
     * @return FormaPagamentoInterface
     */
    public function setCriadoEm($data);

    /**
     * Retorna quando o registro foi atualizado.
     *
     * @return \DateTime
     */
    public function getAtualizadoEm();

    /**
     * Define quando o registro foi atualizado.
     *
     * @param \DateTime $data
     *
     * @return FormaPagamentoInterface
     */
    public function setAtualizadoEm($data);
}
