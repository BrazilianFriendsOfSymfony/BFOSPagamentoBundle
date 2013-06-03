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

namespace BFOS\PagamentoBundle\MeioPagamento\Registro;


use BFOS\PagamentoBundle\MeioPagamento\MeioPagamentoInterface;

interface RegistroMeioPagamentoInterface
{
    /**
     * Registra um meio de pagamento.
     *
     * @param string                 $identificador Identificador interno do meio de pagamento.
     * @param MeioPagamentoInterface $meioPagamento
     * @param string                 $etiqueta Texto que geralmente aparece aos usuários.
     *
     * @return void
     */
    public function registrar($identificador, MeioPagamentoInterface $meioPagamento, $etiqueta = '');

    /**
     * Remove um meio de pagamento a partir de seu identificador.
     *
     * @param string $identificador
     *
     * @return void
     */
    public function desregistrar($identificador);

    /**
     * Indica que um meio de pagamento já foi registrado.
     *
     * @param string $identificador
     *
     * @return boolean
     */
    public function jahRegistrado($identificador);

    /**
     * Retorna o meio de pagamento apartir de seu identificador.
     *
     * @param string $identificador
     *
     * @return MeioPagamentoInterface
     */
    public function get($identificador);

    /**
     * Retorna o texto utilizado como etiqueta apartir de seu identificador.
     *
     * @param string $identificador
     *
     * @return MeioPagamentoInterface
     */
    public function getEtiqueta($identificador);
}
