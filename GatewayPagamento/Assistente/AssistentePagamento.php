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

namespace BFOS\PagamentoBundle\GatewayPagamento\Assistente;


use BFOS\PagamentoBundle\Exception\PagamentoNaoEncontradoException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\InstrucaoPagamentoInvalidaException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\InstrucaoPagamentoNaoEncontradaException;
use BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface;
use BFOS\PagamentoBundle\Model\PagamentoInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;

class AssistentePagamento implements AssistentePagamentoInterface
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var array $options */
    protected $options;

    public function __construct(
        EntityManager $entityManager,
        $options = array()
    ) {
        $this->entityManager = $entityManager;
        $this->options = $options;
    }
    /**
     * @inheritdoc
     */
    public function criarInstrucaoPagamento($gatewayPagamento = null, $valor = null)
    {
        $class = $this->options['instrucao_pagamento_class'];
        /** @var InstrucaoPagamentoInterface $instrPagto */
        $instrPagto = new $class();
        $instrPagto->setValorTotal($valor);
        $instrPagto->setGatewayPagamento($gatewayPagamento);
        return $instrPagto;
    }


    /**
     * @inheritdoc
     */
    public function criarPagamento($instrucaoPagamento, $valor = null)
    {
        /** @var InstrucaoPagamentoInterface $instrucaoPagamento */
        if(is_numeric($instrucaoPagamento)){
            $instrucaoPagamento = $this->getInstrucaoPagamento((int) $instrucaoPagamento, false);
        }

        if (InstrucaoPagamentoInterface::SITUACAO_VALIDA !== $instrucaoPagamento->getSituacao()) {
            throw new InstrucaoPagamentoInvalidaException('A instrução de pagamento deve estar em situação SITUACAO_VALIDA.');
        }

        if (is_null($valor)) {
            $valor = $instrucaoPagamento->getValorTotal() - $instrucaoPagamento->getValorDepositado();
        }

        $class = $this->options['pagamento_class'];
        /** @var PagamentoInterface $pagamento */
        $pagamento = new $class();
        $pagamento->setInstrucaoPagamento($instrucaoPagamento);
        $pagamento->setValorEsperado($valor);

        $instrucaoPagamento->adicionarPagamento($pagamento);
        $this->entityManager->persist($pagamento);
        $this->entityManager->persist($instrucaoPagamento);

        return $pagamento;
    }


    /**
     * @inheritdoc
     */
    public function getInstrucaoPagamento($id, $mascararDadosSensiveis = true)
    {
        $instrPagto = $this->entityManager->getRepository($this->options['instrucao_pagamento_class'])->findOneBy(array('id' => $id));

        if (null === $instrPagto) {
            throw new InstrucaoPagamentoNaoEncontradaException(sprintf('O instrução de pagamento com o ID "%d" não foi encontrada.', $id));
        }

        if(true == $mascararDadosSensiveis){
            // FIXME: mascarar os dados sensiveis
        }

        return $instrPagto;
    }

    /**
     * @inheritdoc
     */
    public function getPagamento($id)
    {
        /** @var PagamentoInterface $pagamento */
        $pagamento = $this->entityManager->getRepository($this->options['pagamento_class'])->find($id, LockMode::PESSIMISTIC_WRITE);

        if (null === $pagamento) {
            throw new PagamentoNaoEncontradoException(sprintf('The pagamento with ID "%d" was not found.', $id));
        }

        return $pagamento;
    }
}
 