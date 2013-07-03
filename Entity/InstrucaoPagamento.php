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

namespace BFOS\PagamentoBundle\Entity;

use BFOS\PagamentoBundle\Model\InstrucaoPagamento as Base;

class InstrucaoPagamento extends Base
{
    protected $dadosAdicionaisOriginal;

    public function onPostLoad()
    {
        $this->dadosAdicionaisOriginal = clone $this->dadosAdicionais;
    }

    public function onPreSave()
    {
        $this->atualizadoEm = new \Datetime;

        // this is necessary until Doctrine adds an interface for comparing
        // value objects. Right now this is done by referential equality
        if (null !== $this->dadosAdicionaisOriginal && false === $this->dadosAdicionais->igual($this->dadosAdicionaisOriginal)) {
            $this->dadosAdicionais = clone $this->dadosAdicionais;
        }
    }
}
