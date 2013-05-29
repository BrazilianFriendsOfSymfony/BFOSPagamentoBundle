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

/**
 * Como deve ser uma instrução para recebimento de um montante.
 * Modela, por exemplo, o valor que deve ser recebido por um pedido feito.
 * Também deve conter as informações adicionais para que o pagamento possa ser processado.
 *
 * Interface InstrucaoRecebimentoInterface
 *
 * @package BFOS\PagamentoBundle\Model
 */
interface InstrucaoRecebimentoInterface
{
    const SITUACAO_NOVA = 1;
    const SITUACAO_RECEBIDA = 2;
    const SITUACAO_VALIDA = 3;
    const SITUACAO_INVALIDA = 4;

    /**
     * Valor total que deverá ser recebido.
     *
     * @return float
     */
    public function getValorTotal();

    /**
     * Retorna a lista de pagamentos associados a instrução.
     *
     * @return array
     */
    public function getPagamentos();
}
