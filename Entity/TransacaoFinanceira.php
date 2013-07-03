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

use BFOS\PagamentoBundle\Model\TransacaoFinanceira as BaseTransacao;

class TransacaoFinanceira extends BaseTransacao
{
    protected $dadosAdicionaisOriginal;

    public function onPreSave()
    {
        $this->atualizadoEm = new \DateTime;
    }
    public function onPostLoad()
    {
        if (null !== $this->dadosAdicionais) {
            $this->dadosAdicionaisOriginal = clone $this->dadosAdicionais;
        }
    }

    public function onPrePersist()
    {
        $this->atualizadoEm = new \DateTime;

        if (null !== $this->dadosAdicionaisOriginal
            && null !== $this->dadosAdicionais
            && false === $this->dadosAdicionais->igual($this->dadosAdicionaisOriginal)) {
            $this->dadosAdicionais = clone $this->dadosAdicionais;
        }
    }
}
