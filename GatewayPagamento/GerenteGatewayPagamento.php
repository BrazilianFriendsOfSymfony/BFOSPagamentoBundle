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

namespace BFOS\PagamentoBundle\GatewayPagamento;


use BFOS\PagamentoBundle\GatewayPagamento\Exception\InstrucaoPagamentoInvalidaException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\InstrucaoPagamentoNaoEncontradaException;
use BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface;
use BFOS\PagamentoBundle\Model\Pagamento;
use Doctrine\ORM\EntityManager;

class GerenteGatewayPagamento implements GerenteGatewayPagamentoInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function criarPagamento($instrucaoPagamentoId, $valor, $pagamento = null)
    {
        $instrPagto = $this->getInstrucaoPagamento($instrucaoPagamentoId, false);

        if (InstrucaoPagamentoInterface::SITUACAO_VALIDA !== $instrPagto->getSituacao()) {
            throw new InstrucaoPagamentoInvalidaException('A instrução de pagamento deve estar em situação SITUACAO_VALIDA.');
        }

        // FIXME: Is it practical to check this at all? There can be many payments, credits, etc.
        //        Verify that this is consistent with the checks related to transactions
//        if (Number::compare($amount, $instruction->getAmount()) === 1) {
//            throw new Exception('The Payment\'s target amount must not be greater than the PaymentInstruction\'s amount.');
//        }

        if(is_null($pagamento)){
            $pagamento = new Pagamento();
        }
        $pagamento->setInstrucaoPagamento($instrPagto);
        $pagamento->setValorEsperado($valor);

        $this->entityManager->persist($pagamento);
        $this->entityManager->flush();

        return $pagamento;
    }

    /**
     * @inheritdoc
     */
    function aprovaEDeposita($pagamentoId, $quantia)
    {
        // TODO: Implement aprovaEDeposita() method.
    }

    /**
     * @inheritdoc
     */
    public function getInstrucaoPagamento($id, $mascararDadosSensiveis = true)
    {
        $instrPagto = $this->entityManager->getRepository('BFOSPagamentoBundle:InstrucaoPagamento')->findOneBy(array('id' => $id));

        if (null === $instrPagto) {
            throw new InstrucaoPagamentoNaoEncontradaException(sprintf('O instrução de pagamento com o ID "%d" não foi encontrada.', $id));
        }

        if(true == $mascararDadosSensiveis){
            // FIXME: mascarar os dados sensiveis
        }

        return $instrPagto;
    }


}
