
/*global define, window, document */

(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery'
        ], factory);
    } else {
        // Browser globals:
        factory(
            window.jQuery
        );
    }
}(function ($) {
    'use strict';

    function atualizarConfiguracao(form_selector, selector, forceUpdate) {
        var form = $(form_selector);
        var gatewayPagamento = form.find(selector).val();
        var prototype = $(form_selector).attr('prototype-' + gatewayPagamento);
        if (true == forceUpdate || form.find('.js_gateway_configuracao_container').html()=='') {
            form.find('.js_gateway_configuracao_container').html(prototype);
        }
    }

    function atualizarConfiguracaoCheckout($this) {
        var container = $($this).closest('.js_forma_pagamento_checkout_form_container');
        var prototype = container.attr('prototype-'+$($this).val());
        if(typeof prototype != 'undefined') {
            container.find('.js_gateway_configuracao_container').html(prototype);
        } else {
            container.find('.js_gateway_configuracao_container').html('');
        }
    }

    $(function(){

        var form_selector = '#bfos_pagamento_forma_pagamento_type';
        var select_selector = '[name="bfos_pagamento_forma_pagamento_type[gatewayPagamento]"]';
        var selector = form_selector + ' ' + select_selector;

        $('body').delegate(selector, 'change', function(e){
            atualizarConfiguracao(form_selector, select_selector, true);
        });

        atualizarConfiguracao(form_selector, select_selector, false);

        $('body').delegate('.js_forma_pagamento_checkout_form_container .js_opcao_forma_pagamento input[type="radio"]', 'change', function(e){
            atualizarConfiguracaoCheckout(this);
        });

        atualizarConfiguracaoCheckout($('.js_forma_pagamento_checkout_form_container .js_opcao_forma_pagamento input[type="radio"]:checked'));
    });

}));
