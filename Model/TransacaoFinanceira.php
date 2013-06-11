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


class TransacaoFinanceira implements TransacaoFinanceiraInterface
{
    protected $situacao;
    protected $pagamento;

    /**
     * @inheritdoc
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * @inheritdoc
     */
    public function setPagamento(PagamentoInterface $pagamento)
    {
        $this->pagamento = $pagamento;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPagamento()
    {
        return $this->pagamento;
    }


}
