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

use BFOS\PagamentoBundle\Model\FormaPagamento as Base;

class FormaPagamento extends Base
{
    public function onPreSave()
    {
        $this->atualizadoEm = new \DateTime;
    }
}
