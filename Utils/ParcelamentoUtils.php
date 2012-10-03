<?php
namespace BFOS\PagamentoBundle\Utils;

class ParcelamentoUtils
{
    static public function obterOpcoesDeParcelamento(ParcelamentoConfiguracao $configuracao, $valor)
    {

        $parcelamentoHabilitado = $configuracao->getParcelamentoHabilitado();
        $jurosParcelamento = $configuracao->getJurosParcelamento() / 100;
        $quantidadeMaximaParcelas = $configuracao->getQuantidadeMaximaParcelas();
        $quantidadeMaximaParcelasSemJuros = $configuracao->getQuantidadeMaximaParcelasSemJuros();
        $valorMinimoParcela = $configuracao->getValorMinimoParcela();

        $p[1]['parcelas'] = 1;
        $p[1]['valor'] = $valor;

        if ($parcelamentoHabilitado)
        {

            if (!($valor <= $valorMinimoParcela))
            {
                // calcula parcelas sem juros
                for ($i = 1; $i <= $quantidadeMaximaParcelas; $i++)
                {
                    if (($valor / $i) >= $valorMinimoParcela)
                    {
                        $p[$i]['parcelas'] = $i;
                        $p[$i]['valor'] = round($valor / $i, 2);
                    } else
                    {
                        break;
                    }

                }

                // calcula parcelas com juros
                for ($i = $quantidadeMaximaParcelasSemJuros + 1; $i <= $quantidadeMaximaParcelas; $i++)
                {
                    if (isset($p[$i]))
                    {

                        $parcelas = $p[$i]['parcelas'];
                        // coeficiente de financiamento
                        $D = pow((1 + $jurosParcelamento), $parcelas);
                        $C = 1 / $D;
                        $cf = $jurosParcelamento / (1 - $C);
                        // calcula do valor parcela
                        $p[$i]['valor'] = round($cf * $valor, 2);
                        $p[$i]['juros'] = ($jurosParcelamento * 100) . '% a.m.';

                    }
                }

            }
        }

        return $p;

    }

    static public function choices($opcoesParcelamento){
        $choices = array();
        foreach ($opcoesParcelamento as $opcao) {
            $choices[$opcao['parcelas']] = $opcao['parcelas'] . ' x R$ ' . number_format($opcao['valor'],2,',','.') .
                (isset($opcao['juros'])?' ('.$opcao['juros'].')':' sem juros');
        }
        return $choices;
    }
}
