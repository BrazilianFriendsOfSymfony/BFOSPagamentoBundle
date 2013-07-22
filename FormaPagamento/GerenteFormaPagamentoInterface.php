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

namespace BFOS\PagamentoBundle\FormaPagamento;


use BFOS\PagamentoBundle\Model\FormaPagamentoInterface;

interface GerenteFormaPagamentoInterface
{
    /**
     * Retorna as formas de pagamento habilitadas no sistema.
     * Também é possível interceptar quais formas estarão disponíveis
     * através dos eventos.
     *
     * @return array
     */
    public function getAtivas();

    /**
     * Retorna todas as formas de pagamento cadastradas no sistema, incluindo as inativas.
     *
     * @return array
     */
    public function getTodas();

    /**
     * @param int $id
     *
     * @return FormaPagamentoInterface
     */
    public function getFormaPagamentoById($id);

    /**
     * @param string  $gatewayPagamento Identificador do gateway de pagamento
     * @param boolean $somenteAtivas
     * @param boolean $somentePrimeiro
     *
     * @return FormaPagamentoInterface
     */
    public function getByGatewayPagamento($gatewayPagamento, $somenteAtivas = true, $somentePrimeiro = true);
}
