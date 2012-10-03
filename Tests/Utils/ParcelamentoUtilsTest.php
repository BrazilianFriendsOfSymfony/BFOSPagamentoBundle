<?php

namespace BFOS\PagamentoBundle\Tests\Utils;


use BFOS\PagamentoBundle\Utils\ParcelamentoConfiguracao;
use BFOS\PagamentoBundle\Utils\ParcelamentoUtils;

class ParcelamentoUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testObterOpcoesDeParcelamento()
    {
        $config = new ParcelamentoConfiguracao();
        $config->setParcelamentoHabilitado(true);
        $config->setJurosParcelamento(1.99);
        $config->setQuantidadeMaximaParcelas(12);
        $config->setQuantidadeMaximaParcelasSemJuros(3);
        $config->setValorMinimoParcela(50);

        $parcelamento = ParcelamentoUtils::obterOpcoesDeParcelamento($config, 500);

        $this->assertEquals(10, count($parcelamento), 'Quantidade de parcelas');
        $this->assertEquals(55.63, $parcelamento[10]['valor'], 'Valor das parcelas');
    }
}
