$(document).ready(function() {

    /**
     * Carrega o botão que serve para subir toda a página caso o usuário desejar, esse botão será inserido em todas as páginas
     */
    let offset = 50;
    $(window).scroll(function() {
        if ($(this).scrollTop() > offset) {
            $('.back-to-top-button').addClass('show-button').removeClass('hide-button');
        } else {
            $('.back-to-top-button').removeClass('show-button').addClass('hide-button');
        }
    });

    // 
    $('.client-create').each(function() {
        let clientCreate = new ClientCreate($(this));
        clientCreate.init();
    });

    // Novo orcamento
    $('.budget-create').each(function() {
        let budgetCreate = new BudgetCreate($(this));
        budgetCreate.init();
    });

    // let test = new Test();
    // test.init();

    // $('.sign-out').each(function() {

    //     let signOut = new SignOut($(this));
    //     signOut.init();
    // });

    // Componente de busca de anuncio
    //$('.ajax-test').each(function() {

        //let test = new Test($(this));
        //test.init();
    //});

    // Menu lateral ADMIN
    $('.main-sidebar').each(function() {

        // let mainSideBar = new MainSideBar($(this));
        // mainSideBar.init();
    });
});