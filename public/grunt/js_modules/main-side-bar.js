/**
 * Menu lateral ADMIN
 * @author Moser Diogo
 */
function MainSideBar(container, config) {

    let self = this;

    this.container = container;
    this.encryption = new Encrypt();
    this.navItem = this.container.find('.nav-item-click');

    // Configuração da classe
    this.config = {

    }

    // Sobrescreve a configuração padrão da classe
    if (config) {
        this.config = $.extend(true, this.config, config);
    }

    this.init = function() {
 
        // self.navItem.click(function() {

        //     if($(this).hasClass('has-treeview')) {

        //         if($(this).hasClass('menu-open')) {
        //             self.navItem.removeClass('menu-open');
        //         } else {
        //             self.navItem.removeClass('menu-open');
        //             $(this).addClass('menu-open');
        //         }
        //     }
        // });
    }
}