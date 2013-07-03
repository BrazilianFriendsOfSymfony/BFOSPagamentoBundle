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

namespace BFOS\PagamentoBundle\GatewayPagamento\Event;


use BFOS\PagamentoBundle\Model\PagamentoInterface;
use Symfony\Component\EventDispatcher\Event;

class MudancaSituacaoPagamentoEvent extends Event
{
    /** @var PagamentoInterface $pagamento */
    private $pagamento;
    private $situacaoAntiga;

    public function __construct(PagamentoInterface $pagamento, $situacaoAntiga)
    {
        $this->pagamento = $pagamento;
        $this->situacaoAntiga = $situacaoAntiga;
    }

    public function getPagamento()
    {
        return $this->pagamento;
    }

    public function getInstrucaoPagamento()
    {
        return $this->pagamento->getInstrucaoPagamento();
    }

    public function getSituacaoAntiga()
    {
        return $this->situacaoAntiga;
    }

    public function getSituacaoNova()
    {
        return $this->pagamento->getSituacao();
    }
}
