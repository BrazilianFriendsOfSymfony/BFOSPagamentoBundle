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

namespace BFOS\PagamentoBundle\GatewayPagamento\Registro;


use BFOS\PagamentoBundle\GatewayPagamento\GatewayPagamentoInterface;

class RegistroGatewayPagamento implements RegistroGatewayPagamentoInterface
{
    protected $gateways = array();

    /**
     * @inheritdoc
     */
    public function registrar($identificador, GatewayPagamentoInterface $gatewayPagamento, $etiqueta = '')
    {
        if(!$this->jahRegistrado($identificador)){
            $this->gateways[$identificador] = array(
                'etiqueta' => $etiqueta,
                'gateway' => $gatewayPagamento
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function desregistrar($identificador)
    {
        if($this->jahRegistrado($identificador)){
            unset($this->gateways[$identificador]);
        }
    }

    /**
     * @inheritdoc
     */
    public function jahRegistrado($identificador)
    {
        return isset($this->gateways[$identificador]);
    }

    /**
     * @inheritdoc
     */
    public function get($identificador)
    {
        if($this->jahRegistrado($identificador)){
            return $this->gateways[$identificador]['gateway'];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getEtiqueta($identificador)
    {
        if($this->jahRegistrado($identificador)){
            return $this->gateways[$identificador]['etiqueta'];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getTodos()
    {
        $lista = array();
        foreach ($this->gateways as $identificador => $gateway) {
            $lista[$identificador] = $gateway['gateway'];
        }
        return $lista;
    }


}
