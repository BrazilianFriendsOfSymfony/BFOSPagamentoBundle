<?php
namespace BFOS\PagamentoBundle\Utils;

use Symfony\Component\Validator\Constraints as Assert;

class ParcelamentoConfiguracao
{

    /**
     * @var boolean $parcelamentoHabilitado
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="bool", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $parcelamentoHabilitado;

    /**
     * @var float $jurosParcelamento
     *
     * @Assert\NotBlank(groups={"parcelamento_habilitado"})
     * @Assert\Type(type="float", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $jurosParcelamento;

    /**
     * @var int $quantidadeMaximaParcelas
     *
     * @Assert\NotBlank(groups={"parcelamento_habilitado"})
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $quantidadeMaximaParcelas;

    /**
     * @var int $quantidadeMaximaParcelasSemJuros
     *
     * @Assert\NotBlank(groups={"parcelamento_habilitado"})
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $quantidadeMaximaParcelasSemJuros;

    /**
     * @var float $valorMinimoParcela
     *
     * @Assert\NotBlank(groups={"parcelamento_habilitado"})
     * @Assert\Type(type="float", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $valorMinimoParcela;

    function __construct($parcelamentoHabilitado = null, $jurosParcelamento = null, $quantidadeMaximaParcelas = null, $quantidadeMaximaParcelasSemJuros = null, $valorMinimoParcela = null)
    {
        $this->parcelamentoHabilitado = $parcelamentoHabilitado;
        $this->jurosParcelamento = $jurosParcelamento;
        $this->quantidadeMaximaParcelas = $quantidadeMaximaParcelas;
        $this->quantidadeMaximaParcelasSemJuros = $quantidadeMaximaParcelasSemJuros;
        $this->valorMinimoParcela = $valorMinimoParcela;
    }

    /**
     * @param float $jurosParcelamento
     */
    public function setJurosParcelamento($jurosParcelamento)
    {
        $this->jurosParcelamento = $jurosParcelamento;
        return $this;
    }

    /**
     * @return float
     */
    public function getJurosParcelamento()
    {
        return $this->jurosParcelamento;
    }

    /**
     * @param boolean $parcelamentoHabilitado
     */
    public function setParcelamentoHabilitado($parcelamentoHabilitado)
    {
        $this->parcelamentoHabilitado = $parcelamentoHabilitado;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getParcelamentoHabilitado()
    {
        return $this->parcelamentoHabilitado;
    }

    /**
     * @param int $quantidadeMaximaParcelas
     */
    public function setQuantidadeMaximaParcelas($quantidadeMaximaParcelas)
    {
        $this->quantidadeMaximaParcelas = $quantidadeMaximaParcelas;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantidadeMaximaParcelas()
    {
        return $this->quantidadeMaximaParcelas;
    }

    /**
     * @param int $quantidadeMaximaParcelasSemJuros
     */
    public function setQuantidadeMaximaParcelasSemJuros($quantidadeMaximaParcelasSemJuros)
    {
        $this->quantidadeMaximaParcelasSemJuros = $quantidadeMaximaParcelasSemJuros;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantidadeMaximaParcelasSemJuros()
    {
        return $this->quantidadeMaximaParcelasSemJuros;
    }

    /**
     * @param float $valorMinimoParcela
     */
    public function setValorMinimoParcela($valorMinimoParcela)
    {
        $this->valorMinimoParcela = $valorMinimoParcela;
        return $this;
    }

    /**
     * @return float
     */
    public function getValorMinimoParcela()
    {
        return $this->valorMinimoParcela;
    }




}
