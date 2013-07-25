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


use BFOS\PagamentoBundle\Event\PagamentoEvents;
use BFOS\PagamentoBundle\Exception\PagamentoInvalidoException;
use BFOS\PagamentoBundle\Exception\PagamentoNaoEncontradoException;
use BFOS\PagamentoBundle\GatewayPagamento\Event\MudancaSituacaoPagamentoEvent;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\AcaoRequeridaException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\GatewayPagamentoBloqueadoException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\GatewayPagamentoException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\GatewayPagamentoTimeoutException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\InstrucaoPagamentoInvalidaException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\InstrucaoPagamentoNaoEncontradaException;
use BFOS\PagamentoBundle\GatewayPagamento\Registro\RegistroGatewayPagamentoInterface;
use BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface;
use BFOS\PagamentoBundle\Model\Pagamento;
use BFOS\PagamentoBundle\Model\PagamentoInterface;
use BFOS\PagamentoBundle\Model\TransacaoFinanceiraInterface;
use BFOS\PagamentoBundle\Utils\Number;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\Tests\Common\Annotations\Null;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class GerenteGatewayPagamento implements GerenteGatewayPagamentoInterface
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var array $options */
    protected $options;

    /** @var EventDispatcherInterface $dispatcher */
    private $dispatcher;

    /** @var Registro\RegistroGatewayPagamentoInterface $registro */
    protected $registro;

    /** @var LoggerInterface $logger */
    protected $logger;

    /** @var boolean $logarInteracao */
    protected $logarInteracao;

    public function __construct(
        EntityManager $entityManager,
        $options = array(),
        RegistroGatewayPagamentoInterface $registro,
        EventDispatcherInterface $dispatcher = null, LoggerInterface $logger, $logarInteracao
    ) {
        $this->entityManager = $entityManager;
        $this->options = $options;
        $this->dispatcher = $dispatcher;
        $this->registro = $registro;
        $this->logger = $logger;
        $this->logarInteracao = $logarInteracao;
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

        $this->entityManager->persist($pagamento);

        return $pagamento;
    }

    /**
     * @inheritdoc
     */
    public function aprova($pagamento, $valor)
    {
        if ($this->logarInteracao) {
            $this->logger->info(' --- PAGAMENTO - GERENTE - aprova()');
        }
        if(is_numeric($pagamento)){
            $pagamento = $this->getPagamento($pagamento);
        }

        $instrucao = $pagamento->getInstrucaoPagamento();

        if (InstrucaoPagamentoInterface::SITUACAO_VALIDA !== $instrucao->getSituacao()) {
            if ($this->logarInteracao) {
                $this->logger->info(' --- SAIDA 1 - PAGAMENTO - GERENTE - aprova()');
            }
            throw new InstrucaoPagamentoInvalidaException('O InstrucaoPagamento tem que ter SITUACAO_VALIDA.');
        }

        $pagamentoState = $pagamento->getSituacao();
        if (PagamentoInterface::SITUACAO_NOVO === $pagamentoState) {

            if (Number::compare($pagamento->getValorEsperado(), $valor) < 0) {
                if ($this->logarInteracao) {
                    $this->logger->info(' --- SAIDA 2 - PAGAMENTO - GERENTE - aprova()');
                }
                throw new \Exception('O valor esperado do Pagamento é menor que o valor solicitado.');
            }

            if ($instrucao->temTransacaoPendente()) {
                if ($this->logarInteracao) {
                    $this->logger->info(' --- SAIDA 3 - PAGAMENTO - GERENTE - aprova()');
                }
                throw new InstrucaoPagamentoInvalidaException('A InstrucaoPagamento, em qualquer momento, pode ter somente uma TransacaoFinanceira pendente.');
            }

            $jahTentada = false;

            $transacao = $this->criarTransacaoFinanceira();
            $transacao->setPagamento($pagamento);
            $transacao->setTipoTransacao(TransacaoFinanceiraInterface::TIPO_TRANSACAO_APROVACAO);
            $transacao->setValorSolicitado($valor);
            $pagamento->adicionarTransacao($transacao);

            $pagamento->setSituacao(PagamentoInterface::SITUACAO_APROVANDO);
            $pagamento->setValorAprovando($valor);
            $instrucao->setValorAprovando($instrucao->getValorAprovando() + $valor);

            $this->dispararEventoMudancaSituacaoPagamento($pagamento, PagamentoInterface::SITUACAO_NOVO);

        } else if (PagamentoInterface::SITUACAO_APROVANDO === $pagamentoState) {

            if (Number::compare($pagamento->getValorEsperado(), $valor) !== 0) {
                if ($this->logarInteracao) {
                    $this->logger->info(' --- SAIDA 4 - PAGAMENTO - GERENTE - aprova()');
                }
                throw new \Exception('O valor esperado do Pagamento tem que ser igual ao valor solicitado em uma transação jahTentada.');
            }

            $transacao = $pagamento->getTransacaoDeAprovacao();
            $jahTentada = true;
        } else {
            if ($this->logarInteracao) {
                $this->logger->info(' --- SAIDA 4 - PAGAMENTO - GERENTE - aprova()');
            }
            throw new PagamentoInvalidoException('A situacao do Pagamento deve ser SITUACAO_NOVO ou SITUACAO_APROVANDO.');
        }

        $gateway = $this->registro->get($instrucao->getGatewayPagamento());
        $situacaoAnterior = $pagamento->getSituacao();

        try {
            $gateway->aprovar($transacao, $jahTentada);

            if (GatewayPagamentoInterface::RESPOSTA_CODIGO_SUCESSO === $transacao->getCodigoResposta()) {
                $pagamento->setSituacao(PagamentoInterface::SITUACAO_APROVADO);
                $pagamento->setValorAprovando(0.0);
                $pagamento->setValorAprovado($transacao->getValorProcessado());
                $instrucao->setValorAprovando($instrucao->getValorAprovando() - $valor);
                $instrucao->setValorAprovado($instrucao->getValorAprovado() + $transacao->getValorProcessado());
                $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_CONCLUIDA_COM_SUCCESSO);

                $this->dispararEventoMudancaSituacaoPagamento($pagamento, $situacaoAnterior);

                $this->entityManager->persist($pagamento);
                $this->entityManager->persist($instrucao);
                $this->entityManager->persist($transacao);

                $result = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_SUCESSO, GatewayPagamentoInterface::JUSTIFICATIVA_SUCESSO);
            } else {
                $pagamento->setSituacao(PagamentoInterface::SITUACAO_FALHOU);
                $pagamento->setValorAprovando(0.0);
                $instrucao->setValorAprovando($instrucao->getValorAprovando() - $valor);
                $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_FALHOU);

                $this->dispararEventoMudancaSituacaoPagamento($pagamento, $situacaoAnterior);

                $this->entityManager->persist($pagamento);
                $this->entityManager->persist($instrucao);
                $this->entityManager->persist($transacao);

                $result = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_FALHOU, $transacao->getJustificativaSituacao());
            }
        } catch (GatewayPagamentoBloqueadoException $blocked) {
            if ($this->logarInteracao) {
                $this->logger->info(' --- catched GatewayPagamentoBloqueadoException');
            }
            $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_PENDENTE);

            if ($blocked instanceof GatewayPagamentoTimeoutException) {
                $justificativa = GatewayPagamentoInterface::JUSTIFICATIVA_TIMEOUT;
            } else if ($blocked instanceof AcaoRequeridaException) {
                $justificativa = GatewayPagamentoInterface::JUSTIFICATIVA_ACAO_REQUERIDA;
            } else if (null === $justificativa = $transacao->getJustificativaSituacao()) {
                $justificativa = GatewayPagamentoInterface::JUSTIFICATIVA_BLOQUEADO;
            }
            $transacao->setJustificativaSituacao($justificativa);
            $transacao->setCodigoResposta(GatewayPagamentoInterface::RESPOSTA_CODIGO_PENDENTE);

            $result = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_PENDENTE, $justificativa);
            $result->setException($blocked);
            $result->setRecuperavel(true);

        } catch (GatewayPagamentoException $ex) {
            if ($this->logarInteracao) {
                $this->logger->info(' --- catched GatewayPagamentoException');
            }
            $pagamento->setSituacao(PagamentoInterface::SITUACAO_FALHOU);
            $pagamento->setValorAprovando(0.0);
            $instrucao->setValorAprovando($instrucao->getValorAprovando() - $valor);
            $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_FALHOU);

            $this->dispararEventoMudancaSituacaoPagamento($pagamento, $situacaoAnterior);

            $result = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_FALHOU, $transacao->getJustificativaSituacao());
        }

        $this->entityManager->persist($pagamento);
        $this->entityManager->persist($instrucao);
        $this->entityManager->persist($transacao);

        if ($this->logarInteracao) {
            $this->logger->info(' --- FIM - PAGAMENTO - GERENTE - aprova()');
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function aprovaEDeposita($pagamento, $valor)
    {
        if(is_numeric($pagamento)){
            $pagamento = $this->getPagamento($pagamento);
        }

        /** @var InstrucaoPagamentoInterface $instrucao */
        $instrucao = $pagamento->getInstrucaoPagamento();

        if (InstrucaoPagamentoInterface::SITUACAO_VALIDA !== $instrucao->getSituacao()) {
            throw new InstrucaoPagamentoInvalidaException('A situação da InstrucaoPagamento deve ser VALIDA.');
        }

        $situacaoPagamento = $pagamento->getSituacao();
        if (PagamentoInterface::SITUACAO_NOVO === $situacaoPagamento) {
            if ($instrucao->temTransacaoPendente()) {
                throw new InstrucaoPagamentoInvalidaException('A InstrucaoPagamento pode ter somente uma transacao pendente.');
            }

            if (1 === Number::compare($valor, $pagamento->getValorEsperado())) {
                throw new \InvalidArgumentException('$valor não deve ser maior que o valor esperado do pagamento.');
            }

            $transacao = $this->criarTransacaoFinanceira();
            $transacao->setTipoTransacao(TransacaoFinanceiraInterface::TIPO_TRANSACAO_APROVACAO_E_DEPOSITO);
            $transacao->setPagamento($pagamento);
            $transacao->setValorSolicitado($valor);
            $pagamento->adicionarTransacao($transacao);

            $pagamento->setValorAprovando($valor);
            $pagamento->setValorDepositando($valor);
            $pagamento->setSituacao(PagamentoInterface::SITUACAO_APROVANDO);

            $instrucao->setValorAprovando($instrucao->getValorAprovando() + $valor);
            $instrucao->setValorDepositando($instrucao->getValorDepositando() + $valor);

            $this->dispararEventoMudancaSituacaoPagamento($pagamento, $situacaoPagamento);

            $retry = false;
        } else if (PagamentoInterface::SITUACAO_APROVANDO === $situacaoPagamento) {
            if (0 !== Number::compare($valor, $pagamento->getValorAprovando())) {
                throw new \InvalidArgumentException('$valor deve ser igual ao valor aprovando do Pagamento.');
            }

            if (0 !== Number::compare($valor, $pagamento->getValorDepositando())) {
                throw new \InvalidArgumentException('$valor deve ser igual ao valor depositando do Pagamento.');
            }

            $transacao = $pagamento->getTransacaoDeAprovacao();

            $retry = true;
        } else {
            throw new PagamentoInvalidoException('Situacao do Pagamento deve ser NOVO ou APROVANDO.');
        }

        /** @var GatewayPagamentoInterface $gateway */
        $gateway = $this->registro->get($instrucao->getGatewayPagamento());
        if(!$gateway){
            throw new \InvalidArgumentException(sprintf('O gateway de pagamento nao foi encontrado: %s', $instrucao->getGatewayPagamento()));
        }
        $situacaoAnterior = $pagamento->getSituacao();

        try {
            $gateway->aprovarEDepositar($transacao, $retry);

            if (GatewayPagamentoInterface::RESPOSTA_CODIGO_SUCESSO === $transacao->getCodigoResposta()) {
                $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_CONCLUIDA_COM_SUCCESSO);
                $valorProcessado = $transacao->getValorProcessado();

                $pagamento->setSituacao(PagamentoInterface::SITUACAO_DEPOSITADO);
                $pagamento->setValorAprovando(0.0);
                $pagamento->setValorDepositando(0.0);
                $pagamento->setValorAprovado($valorProcessado);
                $pagamento->setValorDepositado($valorProcessado);

                $instrucao->setValorAprovando($instrucao->getValorAprovando() - $valor);
                $instrucao->setValorDepositando($instrucao->getValorDepositando() - $valor);
                $instrucao->setValorAprovado($instrucao->getValorAprovado() + $valorProcessado);
                $instrucao->setValorDepositado($instrucao->getValorDepositado() + $valorProcessado);

                $this->dispararEventoMudancaSituacaoPagamento($pagamento, $situacaoAnterior);

                $resultado = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_SUCESSO, GatewayPagamentoInterface::JUSTIFICATIVA_SUCESSO);
            } else {
                $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_FALHOU);

                $pagamento->setSituacao(PagamentoInterface::SITUACAO_FALHOU);
                $pagamento->setValorAprovando(0.0);
                $pagamento->setValorDepositando(0.0);

                $instrucao->setValorAprovando($instrucao->getValorAprovando() - $valor);
                $instrucao->setValorDepositando($instrucao->getValorDepositando() - $valor);

                $this->dispararEventoMudancaSituacaoPagamento($pagamento, $situacaoAnterior);

                $resultado = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_FALHOU, $transacao->getJustificativaSituacao());
            }
        } catch (GatewayPagamentoException $ex) {
            $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_FALHOU);

            $pagamento->setSituacao(PagamentoInterface::SITUACAO_FALHOU);
            $pagamento->setValorAprovando(0.0);
            $pagamento->setValorDepositando(0.0);

            $instrucao->setValorAprovando($instrucao->getValorAprovando() - $valor);
            $instrucao->setValorDepositando($instrucao->getValorDepositando() - $valor);

            $this->dispararEventoMudancaSituacaoPagamento($pagamento, $situacaoAnterior);

            $resultado = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_FALHOU, $transacao->getJustificativaSituacao());
        } catch (GatewayPagamentoBloqueadoException $blocked) {
            $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_PENDENTE);

            if ($blocked instanceof GatewayPagamentoTimeoutException) {
                $justificativaSituacao = GatewayPagamentoInterface::JUSTIFICATIVA_TIMEOUT;
            } else if ($blocked instanceof AcaoRequeridaException) {
                $justificativaSituacao = GatewayPagamentoInterface::JUSTIFICATIVA_ACAO_REQUERIDA;
            } else if (null === $justificativaSituacao = $transacao->getJustificativaSituacao()) {
                $justificativaSituacao = GatewayPagamentoInterface::JUSTIFICATIVA_BLOQUEADO;
            }
            $transacao->setJustificativaSituacao($justificativaSituacao);
            $transacao->setCodigoResposta(GatewayPagamentoInterface::RESPOSTA_CODIGO_PENDENTE);

            $resultado = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_PENDENTE, $justificativaSituacao);
            $resultado->setException($blocked);
            $resultado->setRecuperavel(true);
        }


        $this->entityManager->persist($pagamento);
        $this->entityManager->persist($resultado->getTransacao());
        $this->entityManager->persist($resultado->getInstrucaoPagamento());

        return $resultado;
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

    /**
     * @return TransacaoFinanceiraInterface
     */
    protected function criarTransacaoFinanceira()
    {
        $class =& $this->options['transacao_financeira_class'];

        return new $class;
    }

    /**
     * @param TransacaoFinanceiraInterface $transacao
     * @param string                       $situacao
     * @param string                       $justificativaSituacao
     *
     * @return ResultadoInterface
     */
    protected function construirResultadoTransacaoFinanceira(TransacaoFinanceiraInterface $transacao, $situacao, $justificativaSituacao)
    {
        $class = &$this->options['resultado_class'];

        /** @var ResultadoInterface $resultado */
        $resultado = new $class($transacao, $situacao, $justificativaSituacao);
        $resultado->setSituacao($situacao);
        $resultado->setJustificativaSituacao($justificativaSituacao);
        $resultado->setTransacao($transacao);
        $resultado->setPagamento($transacao->getPagamento());
        $resultado->setInstrucaoPagamento($transacao->getPagamento()->getInstrucaoPagamento());
        return $resultado;
    }

    private function dispararEventoMudancaSituacaoPagamento(PagamentoInterface $pagamento, $situacaoAntiga)
    {
        if (null === $this->dispatcher) {
            return;
        }

        $event = new MudancaSituacaoPagamentoEvent($pagamento, $situacaoAntiga);
        $this->dispatcher->dispatch(PagamentoEvents::SITUACAO_MUDOU, $event);
    }
}
