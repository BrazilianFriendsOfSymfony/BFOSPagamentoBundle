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

namespace BFOS\PagamentoBundle\Criptografia;


interface ServicoDeCriptografiaInterface
{
    /**
     * Este método descriptografa o valor passado.
     *
     * @param string $valorCriptografado
     */
    function descriptografar($valorCriptografado);

    /**
     * Este método criptografa o valor passado.
     *
     * Dados binários pode ser codificado em base64.
     *
     * @param string $valorOriginal
     */
    function criptografar($valorOriginal);
}
