function Test(container, config) {

    let self = this;

    this.container = container;
    this.encryption = new Encrypt();

    if (config) {
        this.config = $.extend(true, this.config, config);
    }

    this.init = function() {
        
        // let modal = new Modal();
        // modal.init();
        // modal.show();

        // $.ajax({
        //     url: BASE_API + "member/create",
        //     method: "POST",
        //     data: {
        //         config: self.encryption.encrypt(JSON.stringify({
        //             apiKey: API_TOKEN,
        //             user:'Moser',
        //             userName:'moser',
        //             password:'123456',
        //             ddd: 51,
        //             phone: 998790335,
        //             street: 'Av Taquara 1050',
        //             number: 1050,
        //             stateId: 23,
        //             cityId: 415132323,
        //             state: 23,
        //             userId: 35,
        //             city: 2,
        //             gender: 2,
        //             email: 'moserdiogo10@gmail.com',
        //             cpf: 83813608034,
        //             cnpj: 33014556000196,
        //             nameCompany: 'Zatta Digital 2',
        //             userType: 2,
        //             district: ''
        //         }), CRIPTO_TOKEN)
        //     },
        //     success: function(data) {

        //         console.log('Sem criptografia');
        //         console.log(data);
        //         console.log('-----------------------');
        //         // let modal = new Modal({loader:true});
        //         // modal.init();
        //         // modal.show();

        //         let dados = getResponse(data);

        //         // setTimeout(function(){

        //         //     // modal.hideLoader();
        //         // },5000);

        //         console.log('Criptografado');
        //         console.log(dados);
        //     },
        //     error: function(error) {
        //         console.log(error)
        //     }
        // })
    }
}