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
use BFOS\PagamentoBundle\GatewayPagamento\Assistente\AssistentePagamentoInterface;
use BFOS\PagamentoBundle\GatewayPagamento\Event\MudancaSituacaoPagamentoEvent;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\AcaoRequeridaException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\GatewayPagamentoBloqueadoException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\GatewayPagamentoException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\GatewayPagamentoTimeoutException;
use BFOS\PagamentoBundle\GatewayPagamento\Exception\InstrucaoPagamentoInvalidaException;
use BFOS\PagamentoBundle\GatewayPagamento\Registro\RegistroGatewayPagamentoInterface;
use BFOS\PagamentoBundle\Model\InstrucaoPagamentoInterface;
use BFOS\PagamentoBundle\Model\Pagamento;
use BFOS\PagamentoBundle\Model\PagamentoInterface;
use BFOS\PagamentoBundle\Model\TransacaoFinanceiraInterface;
use BFOS\PagamentoBundle\Utils\Number;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class GerenteGatewayPagamento implements GerenteGatewayPagamentoInterface
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var array $options */
    protected $options;

    /** @var AssistentePagamentoInterface $assistente */
    protected $assistente;

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
        AssistentePagamentoInterface $assistente,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        $logarInteracao
    ) {
        $this->entityManager = $entityManager;
        $this->options = $options;
        $this->registro = $registro;
        $this->assistente = $assistente;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->logarInteracao = $logarInteracao;
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
            $pagamento = $this->assistente->getPagamento($pagamento);
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

            if ($this->logarInteracao) {
                $this->logger->info(' --- vai chamar construirResultadoTransacaoFinanceira()');
            }
            $result = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_PENDENTE, $justificativa);
            if ($this->logarInteracao) {
                $this->logger->info(' --- voltou de construirResultadoTransacaoFinanceira()');
            }
            $result->setException($blocked);
            $result->setRecuperavel(true);

            if ($this->logarInteracao) {
                $this->logger->info(' --- FIM - catched GatewayPagamentoBloqueadoException');
            }
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

        if ($this->logarInteracao) {
            $this->logger->info(' --- vai persistir $pagamento , $instrucao e $transacao');
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
        if ($this->logarInteracao) {
            $this->logger->info('BFOSPAGAMENTO: GerenteGatewayPagamento.aprovaEDeposita() - INI');
        }

        // localiza o Pagamento
        if(is_numeric($pagamento)){
            // carrega o Pagamento
            $pagamento = $this->assistente->getPagamento($pagamento);
        }
        if(!$pagamento){
            throw new \Exception('O pagamento especificado não foi encontrado.');
        }

        /** @var InstrucaoPagamentoInterface $instrucao */
        $instrucao = $pagamento->getInstrucaoPagamento();

        if (InstrucaoPagamentoInterface::SITUACAO_VALIDA !== $instrucao->getSituacao()) {
            if ($this->logarInteracao) {
                $this->logger->info(' --- SAIDA 1 - GerenteGatewayPagamento.aprovaEDeposita()');
            }
            throw new InstrucaoPagamentoInvalidaException('A situação da InstrucaoPagamento deve ser VALIDA.');
        }

        $situacaoPagamento = $pagamento->getSituacao();
        if (PagamentoInterface::SITUACAO_NOVO === $situacaoPagamento) {
            if ($this->logarInteracao) {
                $this->logger->info(' --- Situacao do pagto é SITUACAO_NOVO - GerenteGatewayPagamento.aprovaEDeposita()');
            }
            if ($instrucao->temTransacaoPendente()) {
                if ($this->logarInteracao) {
                    $this->logger->info(' --- SAIDA 2 - GerenteGatewayPagamento.aprovaEDeposita()');
                }
                throw new InstrucaoPagamentoInvalidaException('A InstrucaoPagamento pode ter somente uma transacao pendente.');
            }

            if (1 === Number::compare($valor, $pagamento->getValorEsperado())) {
                if ($this->logarInteracao) {
                    $this->logger->info(' --- SAIDA 3 - GerenteGatewayPagamento.aprovaEDeposita()');
                }
                throw new \InvalidArgumentException('$valor não deve ser maior que o valor esperado do pagamento.');
            }

            if ($this->logarInteracao) {
                $this->logger->info(' --- Criando TransacaoFinanceira do tipo TIPO_TRANSACAO_APROVACAO_E_DEPOSITO - GerenteGatewayPagamento.aprovaEDeposita()');
                $this->logger->info(' --- Situação é SITUACAO_APROVANDO da TransacaoFinanceira - GerenteGatewayPagamento.aprovaEDeposita()');
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
            if ($this->logarInteracao) {
                $this->logger->info(' --- Situacao do pagto é SITUACAO_APROVANDO - GerenteGatewayPagamento.aprovaEDeposita()');
            }
            if (0 !== Number::compare($valor, $pagamento->getValorAprovando())) {
                if ($this->logarInteracao) {
                    $this->logger->info(' --- SAIDA 4 - GerenteGatewayPagamento.aprovaEDeposita()');
                }
                throw new \InvalidArgumentException('$valor deve ser igual ao valor aprovando do Pagamento.');
            }

            if (0 !== Number::compare($valor, $pagamento->getValorDepositando())) {
                if ($this->logarInteracao) {
                    $this->logger->info(' --- SAIDA 5 - GerenteGatewayPagamento.aprovaEDeposita()');
                }
                throw new \InvalidArgumentException('$valor deve ser igual ao valor depositando do Pagamento.');
            }

            $transacao = $pagamento->getTransacaoDeAprovacao();

            $retry = true;
        } else {
            if ($this->logarInteracao) {
                $this->logger->info(' --- SAIDA 6 - GerenteGatewayPagamento.aprovaEDeposita()');
            }
            throw new PagamentoInvalidoException('Situacao do Pagamento deve ser NOVO ou APROVANDO.');
        }

        /** @var GatewayPagamentoInterface $gateway */
        $gateway = $this->registro->get($instrucao->getGatewayPagamento());
        if(!$gateway){
            if ($this->logarInteracao) {
                $this->logger->info(' --- SAIDA 7 - GerenteGatewayPagamento.aprovaEDeposita()');
            }
            throw new \InvalidArgumentException(sprintf('O gateway de pagamento nao foi encontrado: %s', $instrucao->getGatewayPagamento()));
        }
        $situacaoAnterior = $pagamento->getSituacao();

        try {
            $gateway->aprovarEDepositar($transacao, $retry);

            if (GatewayPagamentoInterface::RESPOSTA_CODIGO_SUCESSO === $transacao->getCodigoResposta()) {
                if ($this->logarInteracao) {
                    $this->logger->info(' --- Gateway aprovouEDepositou com resposta RESPOSTA_CODIGO_SUCESSO - GerenteGatewayPagamento.aprovaEDeposita()');
                }
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
                if ($this->logarInteracao) {
                    $this->logger->info(' --- Gateway aprovouEDepositou com resposta DIFERENTE de RESPOSTA_CODIGO_SUCESSO - GerenteGatewayPagamento.aprovaEDeposita()');
                }
                $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_FALHOU);

                $pagamento->setSituacao(PagamentoInterface::SITUACAO_FALHOU);
                $pagamento->setValorAprovando(0.0);
                $pagamento->setValorDepositando(0.0);

                $instrucao->setValorAprovando($instrucao->getValorAprovando() - $valor);
                $instrucao->setValorDepositando($instrucao->getValorDepositando() - $valor);

                $this->dispararEventoMudancaSituacaoPagamento($pagamento, $situacaoAnterior);

                $resultado = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_FALHOU, $transacao->getJustificativaSituacao());
            }
        } catch (GatewayPagamentoBloqueadoException $blocked) {
            if ($this->logarInteracao) {
                $this->logger->info(' --- catched GatewayPagamentoBloqueadoException - GerenteGatewayPagamento.aprovaEDeposita()');
            }
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
        } catch (GatewayPagamentoException $ex) {
            if ($this->logarInteracao) {
                $this->logger->info(' --- catched GatewayPagamentoException - GerenteGatewayPagamento.aprovaEDeposita()');
            }
            $transacao->setSituacao(TransacaoFinanceiraInterface::SITUACAO_FALHOU);

            $pagamento->setSituacao(PagamentoInterface::SITUACAO_FALHOU);
            $pagamento->setValorAprovando(0.0);
            $pagamento->setValorDepositando(0.0);

            $instrucao->setValorAprovando($instrucao->getValorAprovando() - $valor);
            $instrucao->setValorDepositando($instrucao->getValorDepositando() - $valor);

            $this->dispararEventoMudancaSituacaoPagamento($pagamento, $situacaoAnterior);

            $resultado = $this->construirResultadoTransacaoFinanceira($transacao, ResultadoInterface::SITUACAO_FALHOU, $transacao->getJustificativaSituacao());
        }


        $this->entityManager->persist($pagamento);
        $this->entityManager->persist($resultado->getTransacao());
        $this->entityManager->persist($resultado->getInstrucaoPagamento());

        if ($this->logarInteracao) {
            $this->logger->info('BFOSPAGAMENTO: GerenteGatewayPagamento.aprovaEDeposita() - FIM');
        }
        return $resultado;
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
