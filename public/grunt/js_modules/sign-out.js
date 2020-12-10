/**
 * Deslogar
 * @author Moser Diogo
 */
function SignOut(container, config) {

    let self = this;
    
    this.container = container;
    this.encryption = new Encrypt();

    // 
    this.config = {

    }

    // Sobrescreve a configuração padrão da classe
    if (config) {
        this.config = $.extend(true, this.config, config);
    }

    // Construtor
    this.init = function() {

        let self = this;

        self.container.click(function() {

            self.setSignOut();
        });
    }

    // Delsoga
    this.setSignOut = function() {

        $.ajax({
            url: BASE_API + "login/logout",
            method: "POST",
            data: {
                config: self.encryption.encrypt(JSON.stringify({
                    apiKey: API_TOKEN
                }), CRIPTO_TOKEN)
            },
            success: function(data) {

                console.log('Sem criptografia');
                console.log(data);
                console.log('-----------------------');
                // let modal = new Modal({loader:true});
                // modal.init();
                // modal.show();

                let dados = getResponse(data);

                // setTimeout(function(){

                //     // modal.hideLoader();
                // },5000);

                console.log('Criptografado');
                console.log(dados);
            },
            error: function(error) {
                console.log(error)
            }
        })
    }
}