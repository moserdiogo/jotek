/**
 * Novo cliente
 * @author Moser Diogo
 */
function ClientCreate(container, config) {

    let self = this;

    this.container = container;
    this.formData;
    this.encryption = new Encrypt();

    // Configuração da classe
    this.config = {

    }

    // Sobrescreve a configuração padrão da classe
    if (config) {
        this.config = $.extend(true, this.config, config);
    }

    this.init = function() {
     
        // Configuração initial do formulário
        self.setInitialForm();

        // Maskaras
        self.setMask();

        // Busca os estados e popula o select
        // self.getState();

        // Busca as cidades de acordo com o estado e popula o select das cidades
        // self.setCity();

        self.setCities();

        // Submit do formulário
        self.container.find('.client-create-submit').click(function(e) {
            e.preventDefault();
            self.submit();
        });
    }

    // Configura o formulário inicial
    this.setInitialForm = function() {

        // 
        if(self.container.find("input[name=clientType]:checked").val() == 'juridical') {

            self.container.find('.phisical-group').addClass('hd');
            self.container.find('.juridical-group').removeClass('hd');
        } else {
            self.container.find('.phisical-group').removeClass('hd');
            self.container.find('.juridical-group').addClass('hd');
        }

        // 
        $(self.container.find("input[name=clientType]")).on('change', function() {

            if($(this).val() == 'juridical') {

                self.container.find('.phisical-group').addClass('hd');
                self.container.find('.juridical-group').removeClass('hd');
            } else {
                self.container.find('.phisical-group').removeClass('hd');
                self.container.find('.juridical-group').addClass('hd');
            }
        });
    }

    // Configuras as maskarasdos inputs
    this.setMask = function() {

        $(self.container.find('input[name="birthDate"]')).mask('00/00/0000');
        $(self.container.find('input[name="phone"]')).mask('(00) 00000-0000');
        $(self.container.find('input[name="cnpj"]')).mask('00.000.000/0000-00', {reverse: true});
        $(self.container.find('input[name="cpf"]')).mask('000.000.000-00', {reverse: true});
    }

    this.getState = function() {
 
        $.ajax({
            url: BASE_API + "geo/getStates",
            method: "POST",
            data: {
                config: self.encryption.encrypt(JSON.stringify({
                    apiKey: API_TOKEN,
                }), CRIPTO_TOKEN)
            },
            success: function(data) {

                let response = getResponse(data);

                if(response.errorCode == 0) {

                    response.data.forEach(item => {
                        self.container.find('select[name="state"]').append('<option value='+item.IDGS+'>'+item.State+'</option>');
                    });
                }
            },
            error: function(error) {
                console.log(error)
            }
        })
    }

    // Busca as cidades de acordo com o estado e popula o select das cidades
    this.setCity = function() {

        self.container.find('select[name="state"]').change(function() {
 
            $.ajax({
                url: BASE_API + "geo/getCitiesByState",
                method: "POST",
                data: {
                    config: self.encryption.encrypt(JSON.stringify({
                        apiKey: API_TOKEN,
                        stateId: $(this).val()
                    }), CRIPTO_TOKEN)
                },
                success: function(data) {
    
                    let response = getResponse(data);

                    console.log(response)
    
                    if(response.errorCode == 0) {
    
                        response.data.forEach(item => {
                            self.container.find('select[name="city"]').append('<option value='+item.IDC+'>'+item.City+'</option>');
                        });
                    }
                },
                error: function(error) {
                    console.log(error)
                }
            })
        });
    }

    // Busca as cidades de acordo com o estado e popula o select das cidades
    this.setCities = function() {

        let cityInput = self.container.find('input[name="cities"]'); 

        self.container.find('input[name="cities"]').keyup(function() {

            if($(this).val().length > 3) {
            
                $.ajax({
                    url: BASE_API + "geo/getCitiesByName",
                    method: "POST",
                    data: {
                        config: self.encryption.encrypt(JSON.stringify({
                            apiKey: API_TOKEN,
                            city: $(this).val()
                        }), CRIPTO_TOKEN)
                    },
                    success: function(data) {
        
                        let response = getResponse(data);
                        let citiesInput = self.container.find('.cities-list');
        
                        if(response.errorCode == 0) {

                            citiesInput.html('');

                            citiesInput.removeClass('hd');
        
                            response.data.forEach(item => {
                                self.container.find('.cities-list').append('<li value='+item.IDGC+'>'+item.City+'</li>');
                            });

                            // 
                            self.container.find('.cities-list li').click(function() {

                                citiesInput.addClass('hd');

                                self.container.find('input[name="cities"]:text').val($(this).text());
                                self.container.find('input[name="cities"]').attr('value', $(this).val());
                            });

                            // Esconde a lista de cidades quando clicado fora
                            window.addEventListener('click', function(e){   
                                if (document.getElementById('cities-list').contains(e.target)){
                                  // Clicked in box
                                } else{
                                  // Clicked outside the box
                                  citiesInput.addClass('hd');
                                }
                            });
                        }
                    },
                    error: function(error) {
                        console.log(error)
                    }
                })
            }
        });
    }

    // Pea os valores do formulário
    this.getInputValues = function() {

        let userType = self.container.find('input[name="clientType"]:checked').val();

        console.log(userType)


        this.formData = {
            entity: userType == 'phisical' ? 1 : 2,
            user: userType == 'phisical' ? self.container.find('input[name="user"]').val() : self.container.find('input[name="userCompany"]').val(),
            cpf: self.container.find('input[name="cpf"]').val() ? self.container.find('input[name="cpf"]').val().replace(/\D/g,'') : null,
            cnpj: self.container.find('input[name="cnpj"]').val() ? self.container.find('input[name="cnpj"]').val().replace(/\D/g,'') : null,
            birthDate: self.container.find('input[name="birthDate"]').val() ? self.container.find('input[name="birthDate"]').val() : null,
            email: self.container.find('input[name="email"]').val() ? self.container.find('input[name="email"]').val() : null,
            phone: self.container.find('input[name="phone"]').val() ? self.container.find('input[name="phone"]').val().replace(/\D/g,'') : null,
            city: self.container.find('input[name="cities"]').attr('value'),
            district: self.container.find('input[name="district"]').val() ? self.container.find('input[name="district"]').val() : null,
            number: self.container.find('input[name="number"]').val(),
            street: self.container.find('input[name="street"]').val() ? self.container.find('input[name="street"]').val() : null,
            complement: self.container.find('input[name="complement"]').val() ? self.container.find('input[name="complement"]').val() : null,
            zipCode: self.container.find('input[name="zipCode"]').val() ? self.container.find('input[name="zipCode"]').val() : null,
        }
    }

    // Valida os inputs do formulário
    this.validate = function() {

        var dateRegex = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
        console.log(dateRegex.test('21/01/1986'));
    }

    // Submissao do formulario
    this.submit = function() {

        self.getInputValues();

        console.log(self.formData.birthDate)

        // alert(777)
        // alert(9999)

        // console.log(88)
        // console.log(self.container.find('input[name="cities"]').attr('value'));

        $.ajax({
            url: BASE_API + "client/create",
            method: "POST",
            data: {
                config: self.encryption.encrypt(JSON.stringify({
                    apiKey: API_TOKEN,
                    userType: 3,
                    user: self.formData.user,
                    cpf: self.formData.cpf,
                    cnpj: self.formData.cnpj,
                    birthDate: self.formData.birthDate,
                    email: self.formData.email,
                    phone: self.formData.phone,
                    city: self.formData.city,
                    district: self.formData.district,
                    number: self.formData.number,
                    street: self.formData.street,
                    complement: self.formData.complement,
                    zipCode: self.formData.zipCode,
                    entity: self.formData.entity
                }), CRIPTO_TOKEN)
            },
            success: function(data) {

                let response = getResponse(data);

                if(response.errorCode == 0) {

                    // Reseta os campos do formulário
                    $('#client-create').trigger("reset");

                    self.container.find('.alert-message').html('Cadastrado com sucesso!');
                    self.container.find('.alert').removeClass('hd');

                    $([document.documentElement, document.body]).animate({
                        scrollTop: $(".alert").offset()
                    }, 2000);

                } else {
                    self.container.find('.alert-message').html(response.errorMessage);
                    self.container.find('.alert').removeClass('hd');

                    $([document.documentElement, document.body]).animate({
                        scrollTop: $(".alert").offset()
                    }, 2000);
                }
            },
            error: function(error) {
                console.log(error)
            }
        })
    }
    
    // Submissaõ do formulário
    this.submitModal = function() {

        let modal = new Modal();
        modal.init();



        $.ajax({
            // http://localhost/phalcon/production/jotek/cliente-adicionar
            // url: "C:/Users/Moser/Dropbox/www/phalcon/production/jotek/app/views/components/forms/client-create",
            url: "http://localhost/phalcon/production/jotek/components/test",
            // method: "GET",
            // async: false,
            // data: { },
            success: function(data) {

                console.log(data)

                modal.setContent(data);
                modal.show();

            },
            error: function(error) {
                console.log(error)
            }
        })

        
            // $.ajax({
            //     url: "http://localhost/phalcon/production/jotek/app/views/components/forms/client-create.phtml",
            //     async: true,   // asynchronous request? (synchronous requests are discouraged...)
            //     cache: false,   // with this, you can force the browser to not make cache of the retrieved data
            //     dataType: "text",  // jQuery will infer this, but you can set explicitly
            //     success: function( data, textStatus, jqXHR ) {

            //         console.log(data);
            //         // var resourceContent = data; // can be a global variable too...
            //         // process the content...
            //     }
            // });
        
    }
}