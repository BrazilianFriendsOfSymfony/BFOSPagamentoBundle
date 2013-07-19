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
 * Class DadosAdicionaisInterface
 *
 * @package BFOS\PagamentoBundle\Model
 */
interface DadosAdicionaisInterface
{
    function ehNecessariaCriptografia($nome);
    function remover($nome);
    function adicionar($nome, $valor, $criptografar = true, $persistir = true);

    /**
     * @param      $nome
     * @param mixed $padrao Valor que será retornado caso a propriedade não seja encontrada.
     *
     * @return mixed
     */
    function obter($nome, $padrao = null);
    function tem($nome);
    function todos();
    function igual(DadosAdicionaisInterface $dados);
}