/**
 * Loader
 * @author Moser Diogo
 */
function Loader(container, config) {

    let self = this;

    this.container = container;

    // Configuração da classe
    this.config = {

    }

    // Sobrescreve a configuração padrão da classe
    if (config) {
        this.config = $.extend(true, this.config, config);
    }

    // Construtor
    this.init = function() {

        $('body').append('<div class="loader-grid"><div class="loader"></div></div>');
    }
}