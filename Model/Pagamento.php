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


use BFOS\PagamentoBundle\Entity\DadosAdicionais;
use Doctrine\Common\Collections\ArrayCollection;

class Pagamento implements PagamentoInterface
{
    protected $id;
    protected $valorAprovado;
    protected $valorAprovando;
    protected $valorDepositando;
    protected $valorDepositado;
    protected $dataVencimento;
    protected $instrucaoPagamento;
    protected $situacao;
    protected $valorEsperado;
    protected $precisaDeAtencao;
    protected $vencido;
    protected $transacoes;
    protected $criadoEm;
    protected $atualizadoEm;
    /** @var  DadosAdicionaisInterface $dadosAdicionais */
    protected $dadosAdicionais;

    static public $labelsSituacao = array(
        self::SITUACAO_APROVADO => 'Aprovado',
        self::SITUACAO_APROVANDO => 'Aprovando',
        self::SITUACAO_CANCELADO => 'Cancelado',
        self::SITUACAO_DEPOSITADO => 'Depositado',
        self::SITUACAO_DEPOSITANDO => 'Depositando',
        self::SITUACAO_EXPIRADO => 'Expirado',
        self::SITUACAO_FALHOU => 'Falhou',
        self::SITUACAO_NOVO => 'Novo'
    );

    public function __construct()
    {
        $this->situacao = self::SITUACAO_NOVO;
        $this->valorAprovado = 0.0;
        $this->valorAprovando = 0.0;
        $this->valorDepositado = 0.0;
        $this->valorDepositando = 0.0;
        $this->valorEsperado = 0.0;
        $this->transacoes = new ArrayCollection();
        $this->precisaDeAtencao = false;
        $this->vencido = false;
        $this->criadoEm = new \DateTime;
        $this->dadosAdicionais = new DadosAdicionais();
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValorAprovado()
    {
        return $this->valorAprovado;
    }

    /**
     * @inheritdoc
     */
    public function setValorAprovado($valor)
    {
        $this->valorAprovado = $valor;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTransacaoDeAprovacao()
    {
        /** @var TransacaoFinanceiraInterface $transacao */
        foreach ($this->transacoes as $transacao) {
            $type = $transacao->getTipoTransacao();

            if (TransacaoFinanceiraInterface::TIPO_TRANSACAO_APROVACAO === $type
                || TransacaoFinanceiraInterface::TIPO_TRANSACAO_APROVACAO_E_DEPOSITO === $type) {

                return $transacao;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getValorAprovando()
    {
        return $this->valorAprovando;
    }

    /**
     * @inheritdoc
     */
    public function setValorAprovando($valor)
    {
        $this->valorAprovando = $valor;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValorDepositado()
    {
        return $this->valorDepositado;
    }

    /**
     * @inheritdoc
     */
    public function setValorDepositado($valor)
    {
        $this->valorDepositado = $valor;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValorDepositando()
    {
        return $this->valorDepositando;
    }

    /**
     * @inheritdoc
     */
    public function setValorDepositando($valor)
    {
        $this->valorDepositando = $valor;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTransacoesDeDeposito()
    {
        /** @var TransacaoFinanceiraInterface $transacao */
        return $this->transacoes->filter(function($transacao) {
                return TransacaoFinanceiraInterface::TIPO_TRANSACAO_DEPOSITO === $transacao->getTipoTransacao();
            });
    }

    /**
     * @inheritdoc
     */
    public function getDataVencimento()
    {
        return $this->dataVencimento;
    }

    /**
     * @inheritdoc
     */
    public function getInstrucaoPagamento()
    {
        return $this->instrucaoPagamento;
    }

    /**
     * @inheritdoc
     */
    public function setInstrucaoPagamento(InstrucaoPagamentoInterface $instrucaoPagamento)
    {
        $this->instrucaoPagamento = $instrucaoPagamento;
        return $this;
    }


    public function getTransacaoPendente()
    {
        /** @var TransacaoFinanceiraInterface $transacao */
        foreach ($this->transacoes as $transacao) {
            if (TransacaoFinanceiraInterface::SITUACAO_PENDENTE === $transacao->getSituacao()) {
                return $transacao;
            }
        }

        return null;
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
    public function getSituacaoLabel()
    {
        $label = '';
        if (isset(self::$labelsSituacao[$this->situacao])) {
            $label = self::$labelsSituacao[$this->situacao];
        }
        return $label;
    }

    /**
     * @inheritdoc
     */
    public function getValorEsperado()
    {
        return $this->valorEsperado;
    }

    /**
     * @inheritdoc
     */
    public function setValorEsperado($valor)
    {
        $this->valorEsperado = $valor;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function temTransacaoPendente()
    {
        return null == $this->getTransacaoPendente();
    }

    /**
     * @inheritdoc
     */
    public function precisaDeAtencao()
    {
        return $this->precisaDeAtencao;
    }

    public function setPrecisaDeAtencao($boolean)
    {
        $this->precisaDeAtencao = $boolean;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDataVencimento(\DateTime $data)
    {
        $this->dataVencimento = $data;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVencido()
    {
        return $this->vencido;
    }

    /**
     * @inheritdoc
     */
    public function setVencido($boolean)
    {
        $this->vencido = $boolean;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
        return $this;
    }

    /**
     * Adiciona uma transação ao pagamento.
     *
     * @param TransacaoFinanceiraInterface $transacao
     *
     * @return PagamentoInterface
     */
    public function adicionarTransacao(TransacaoFinanceiraInterface $transacao)
    {
        if($this->jahAdicionouTransacao($transacao)){
            return $this;
        }
        $this->transacoes[] = $transacao;
        $transacao->setPagamento($this);
        return $this;
    }

    /**
     * Indica se a transação já foi adicionada ao pagamento.
     *
     * @param TransacaoFinanceiraInterface $transacao
     *
     * @return PagamentoInterface
     */
    public function jahAdicionouTransacao(TransacaoFinanceiraInterface $transacao)
    {
        /** @var TransacaoFinanceiraInterface $trans */
        foreach ($this->transacoes as $trans) {
            if($trans->getId() == $transacao->getId()){
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function setAtualizadoEm($atualizadoEm)
    {
        $this->atualizadoEm = $atualizadoEm;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAtualizadoEm()
    {
        return $this->atualizadoEm;
    }

    /**
     * @inheritdoc
     */
    public function setCriadoEm($criadoEm)
    {
        $this->criadoEm = $criadoEm;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCriadoEm()
    {
        return $this->criadoEm;
    }

    /**
     * @inheritdoc
     */
    public function getDadosAdicionais()
    {
        return $this->dadosAdicionais;
    }

    /**
     * @inheritdoc
     */
    public function setDadosAdicionais($dados)
    {
        $this->dadosAdicionais = $dados;
        return $this;
    }

}
