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

namespace BFOS\PagamentoBundle\MeioPagamento\Registro;


use BFOS\PagamentoBundle\MeioPagamento\MeioPagamentoInterface;

class RegistroMeioPagamento implements RegistroMeioPagamentoInterface
{
    protected $meios;

    /**
     * @inheritdoc
     */
    public function registrar($identificador, MeioPagamentoInterface $meioPagamento, $etiqueta = '')
    {
        if(!$this->jahRegistrado($identificador)){
            $this->meios[$identificador] = array(
                'etiqueta' => $etiqueta,
                'meioPagamento' => $meioPagamento
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function desregistrar($identificador)
    {
        if($this->jahRegistrado($identificador)){
            unset($this->meios[$identificador]);
        }
    }

    /**
     * @inheritdoc
     */
    public function jahRegistrado($identificador)
    {
        return isset($this->meios[$identificador]);
    }

    /**
     * @inheritdoc
     */
    public function get($identificador)
    {
        if(!$this->jahRegistrado($identificador)){
            return $this->meios[$identificador]['meioPagamento'];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getEtiqueta($identificador)
    {
        if(!$this->jahRegistrado($identificador)){
            return $this->meios[$identificador]['etiqueta'];
        }
        return null;
    }


}
