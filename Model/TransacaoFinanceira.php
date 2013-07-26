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

class TransacaoFinanceira implements TransacaoFinanceiraInterface
{
    protected $id;
    protected $situacao;
    /** @var  PagamentoInterface $pagamento */
    protected $pagamento;
    protected $tipoTransacao;
    protected $valorSolicitado;
    protected $valorProcessado;
    protected $justificativaSituacao;
    protected $referencia;
    protected $codigoResposta;
    protected $idRastreamento;
    /** @var  DadosAdicionaisInterface $dadosAdicionais */
    protected $dadosAdicionais;
    protected $criadoEm;
    protected $atualizadoEm;

    public static $labelsSituacao = array(
        self::SITUACAO_CANCELADA => 'Cancelada',
        self::SITUACAO_FALHOU => 'Falhou',
        self::SITUACAO_NOVA => 'Nova',
        self::SITUACAO_PENDENTE => 'Pendente',
        self::SITUACAO_CONCLUIDA_COM_SUCCESSO => 'Concluída com sucesso',
    );

    public static $labelsTipoTransacao = array(
        self::TIPO_TRANSACAO_APROVACAO => 'Aprovação',
        self::TIPO_TRANSACAO_APROVACAO_E_DEPOSITO => 'Aprovação e depósito',
        self::TIPO_TRANSACAO_CONSULTA => 'Consulta',
        self::TIPO_TRANSACAO_CREDITO => 'Crédito',
        self::TIPO_TRANSACAO_DEPOSITO => 'Depósito',
        self::TIPO_TRANSACAO_ESTORNO_APROVACAO => 'Estorno da aprovação',
        self::TIPO_TRANSACAO_ESTORNO_CREDITO => 'Estorno do crédito',
        self::TIPO_TRANSACAO_ESTORNO_DEPOSITO => 'Estorno do depósito'
    );

    public function __construct()
    {
        $this->situacao = self::SITUACAO_NOVA;
        $this->valorProcessado = 0.0;
        $this->valorSolicitado = 0.0;
        $this->criadoEm = new \DateTime;
        $this->dadosAdicionais = new DadosAdicionais();
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

    /**
     * @inheritdoc
     */
    public function getTipoTransacao()
    {
        return $this->tipoTransacao;
    }

    /**
     * @inheritdoc
     */
    public function getTipoTransacaoLabel()
    {
        $label = '';
        if (isset(self::$labelsTipoTransacao[$this->tipoTransacao])) {
            $label = self::$labelsTipoTransacao[$this->tipoTransacao];
        }
        return $label;
    }

    /**
     * @inheritdoc
     */
    public function setTipoTransacao($tipo)
    {
        $this->tipoTransacao = $tipo;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValorSolicitado()
    {
        $this->valorSolicitado;
    }

    /**
     * @inheritdoc
     */
    public function setValorSolicitado($valor)
    {
        $this->valorSolicitado = $valor;
        return $this;
    }

    /**
     * Id da transacao.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o valor processado na transação.
     *
     * @return float
     */
    public function getValorProcessado()
    {
        return $this->valorProcessado;
    }

    /**
     * Define o valor processado na transação.
     *
     * @param float $valor
     *
     * @return TransacaoFinanceiraInterface
     */
    public function setValorProcessado($valor)
    {
        $this->valorProcessado = $valor;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getJustificativaSituacao()
    {
        return $this->justificativaSituacao;
    }

    /**
     * @inheritdoc
     */
    public function setJustificativaSituacao($justificativa)
    {
        $this->justificativaSituacao = $justificativa;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * @inheritdoc
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCodigoResposta()
    {
        return $this->codigoResposta;
    }

    /**
     * @inheritdoc
     */
    public function setCodigoResposta($codigo)
    {
        $this->codigoResposta = $codigo;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIdRastreamento()
    {
        return $this->idRastreamento;
    }

    /**
     * @inheritdoc
     */
    public function setIdRastreamento($id)
    {
        $this->idRastreamento = $id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDadosAdicionais()
    {
        if (null !== $this->dadosAdicionais) {
            return $this->dadosAdicionais;
        }

        if (null !== $this->pagamento) {
            return $this->pagamento->getInstrucaoPagamento()->getDadosAdicionais();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setDadosAdicionais($dados)
    {
        $this->dadosAdicionais = $dados;
        return $this;
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
}
