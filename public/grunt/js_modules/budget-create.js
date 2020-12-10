/**
 * Formulario de novo orcamento
 */
function BudgetCreate(container, config) {

    let self = this;

    this.container = container;
    this.encryption = new Encrypt();

    // Configuração da classe
    this.config = {

    }

    // Sobrescreve a configuração padrão da classe
    if (config) {
        this.config = $.extend(true, this.config, config);
    }

    // Construtor
    this.init = function() {

        // Abre modal para cadastrar novo cliente
        $('.budget-new').click(function() {
            alert(888)
            self.newClient();
        });

        // Busca o cliente por nome ao digitar 
        $(self.container.find('input[name="client"]')).keyup(function() {
            if($(this).val().length >= 3) {
                self.getClients($(this).val());
            } else {
                $('.client-list').addClass('hd');
            }
        })
    }

    // Busca cliente ao digitar
    this.getClients = function(inputValue) {
        
        $.ajax({
            url: BASE_API + "client/getClientsByName",
            method: "POST",
            data: {
                config: self.encryption.encrypt(JSON.stringify({
                    apiKey: API_TOKEN,
                    client: inputValue
                }), CRIPTO_TOKEN)
            },
            success: function(data) {

                let response = getResponse(data);
                let clientInput = self.container.find('.client-list');

                console.log(response)

                if(response.errorCode == 0) {

                    clientInput.html('');

                    clientInput.removeClass('hd');

                    if(response.data.length > 0) {
                        
                        response.data.forEach(item => {
                            self.container.find('.client-list').append('<li value='+item.IDMB+'>'+item.Name+'</li>');
                        });
    
                        // 
                        self.container.find('.client-list li').click(function() {
    
                            clientInput.addClass('hd');
    
                            self.container.find('input[name="client"]:text').val($(this).text());
                            self.container.find('input[name="client"]').attr('value', $(this).val());
                        });
    
                        // Esconde a lista de cidades quando clicado fora
                        window.addEventListener('click', function(e){   
                            if (document.getElementById('client-list').contains(e.target)){
                              // Clicked in box
                            } else{
                              // Clicked outside the box
                              clientInput.addClass('hd');
                            }
                        });
                    } else {
                        // clientInput.removeClass('hd');
                        self.container.find('.client-list').html('<li>Nenhum cliente encontrado com esse nome</li>');
                        
                        // Esconde a lista de cidades quando clicado fora
                        window.addEventListener('click', function(e){   
                            if (document.getElementById('client-list').contains(e.target)){
                              // Clicked in box
                            } else{
                              // Clicked outside the box
                              clientInput.addClass('hd');
                            }
                        });
                    }

                } else {
                    // alert(000088)
                    // alert(9)
                    // clientInput.removeClass('hd');
                    // // self.container.find('.client-list').html('<li>Nenhum cliente encontrado com esse nome</li>');
                    // console.log(self.container.find('.client-list'));
                    // alert(3)
                }
            },
            error: function(error) {
                console.log(error)
            }
        })
    }

    // Novo cliente
    this.newClient = function() {

        $.ajax({
            url: BASE_API + "components/test",
            success: function(data) {

                let modal = new Modal();
                modal.init();

                modal.setContent(data);

                modal.show();

                $('.client-create').each(function() {

                    let clientCreate = new ClientCreate($(this));
                    clientCreate.init();
                });
            },
            error: function(error) {
                console.log(error)
            }
        })
    }
}