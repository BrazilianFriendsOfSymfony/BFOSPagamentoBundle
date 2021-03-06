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

namespace BFOS\PagamentoBundle\GatewayPagamento\Exception;


use Exception;

class RedirecionarUsuarioAcaoRequeridaException extends AcaoRequeridaException
{
    protected $url;

    public function __construct($url, $message = "", $code = 0, Exception $previous = null)
    {
        $this->url = $url;
        parent::__construct($message, $code, $previous); // TODO: Change the autogenerated stub
    }


    /**
     * @param string $url
     *
     * @return RedirecionarUsuarioAcaoRequeridaException
*/
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

}
