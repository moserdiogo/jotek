/**
 * Classe para uso de modais
 * @param object config 
 */
function Modal(config) {

    let self = this;

    // Configuração padrão da classe
    this.config = {
        // type: WIDGET_MODAL,
        target: 'body',
        title: 'Olá',
        content: 'Deseja continuar?',
        size: undefined,
        height: undefined,
        style: 'default',
        theme: 'default',
        //animation: '',
        icon: 'ic ic-warning',
        closeIcon: true,
        isLoading: true,
        backgroundDismiss: true,
        backgroundImage: undefined,
        onDestroy: true,
        align: {
            vertical: 'center',
            horizontal: 'center',
        },
        buttons: {
            action: true,
            cancel: false,
            align: 'right',
            cancelButton: {
                label: 'CANCELAR',
                class: 'bg-pm pd-1 tx-wt tx-b ls-1 tx-md',
            },
            actionButtons: []
        },
        build: {
            divider: false,
            header: false,
            footer: false,
            content: {}
        }
    };

    // Sobreescreve a configuração da classe
    if (config) {
        this.config = $.extend(true, this.config, config);
    }

    this.init = function() {

            // Cria os elementos do modal
            this.container = new Div('md-bg');
            this.modalAlign = new Div('md-al');
            this.modalColumn = new Div();
            this.modalPanel = new Div('md-pn bd-rd-md');
            this.modalHeading = new Div('md-hd');
            this.modalHeadingIcon = new Icon('md-hd-i');
            this.modalHeadingContainer = new Div('md-hd-ct');
            this.modalHeadingRow = new Div('md-hd-row');
            this.modalHeadingContent = new Div('md-hd-c');
            this.modalHeadingTools = new Div('md-hd-tl');
            this.modalContent = new Div('md-ct');
            this.modalFooter = new Div('md-ft');
            this.modalFooterContainer = new Div('md-ft-ct');
            this.modalFooterRow = new Div('md-ft-row');
            this.modalFooterContent = new Div('md-ft-c');

            // Carrega o loader se passado como parametro
            this.loaderContainer = new Div('loader-grid row al-it-c jf-ct-c fh pt-5 pb-3');
            this.loaderGrid = new Div('col-12 tx-c');
            this.loader = new Div('loader');

            if (this.config.loader) {

                this.loaderGrid.append(this.loader);
                this.loaderContainer.append(this.loaderGrid);
                this.modalPanel.append(this.loaderContainer);
            }

            // Configura o z-index do Modal
            this.container.css('z-index', '4000');

            // Configura os elementos
            if (this.config.backgroundImage) {
                this.container.css({
                    backgroundImage: 'url(' + this.config.backgroundImage + ')'
                });
            }

            //this.container.addClass(this.config.animation);

            switch (this.config.theme) {
                case 'primary':
                    this.container.addClass('md-pm');
                    break;
                case 'info':
                    this.container.addClass('md-if');
                    break;
                case 'warning':
                    this.container.addClass('md-wn');
                    break;
                case 'danger':
                    this.container.addClass('md-dg');
                    break;
                default:
                    break;
            }

            this.modalHeadingContent.html(this.config.title);

            //this.modalContent.html(this.config.content); //responseText

            if (this.config.closeIcon) {

                this.closeContainer = new Div('md-close');
                this.closeIcon = new Icon('ic-close');

                this.closeContainer.append(this.closeIcon);
                this.modalPanel.append(this.closeContainer);
                this.closeIcon.click(function() {
                    self.close();
                });
            }

            if (this.config.backgroundDismiss) {
                $(document).mouseup(function(e) {
                    if (!self.modalPanel.is(e.target) && self.modalPanel.has(e.target).length === 0) {
                        self.close();
                    }
                });
            }
            switch (this.config.align.horizontal) {
                case 'center':
                    this.modalAlign.addClass('jf-ct-c');
                    break;
                case 'left':
                    this.modalAlign.addClass('jf-ct-s');
                    break;
                case 'right':
                    this.modalAlign.addClass('jf-ct-e');
                    break;
                default:
                    this.modalAlign.addClass('jf-ct-c');
                    break;
            }

            // TODO: problema no scroll:  Remover classe al-it-c na versão mobile
            switch (this.config.align.vertical) {
                case 'center':
                    this.modalAlign.addClass('al-it-c');
                    break;
                case 'top':
                    this.modalAlign.addClass('al-it-t');
                    break;
                case 'bottom':
                    this.modalAlign.addClass('al-it-e');
                    break;
                default:
                    this.modalAlign.addClass('al-it-c');
                    break;
            }

            this.setSize(this.config.size);

            switch (this.config.style) {
                case 'alert':
                case 'fullscreen':
                    this.modalFooterRow.addClass('jf-ct-c');
                    break;
                default:
                    switch (this.config.buttons.align) {
                        case 'center':
                            this.modalFooterRow.addClass('jf-ct-c');
                            break;
                        case 'left':
                            this.modalFooterRow.addClass('jf-ct-s');
                            break;
                        case 'right':
                            this.modalFooterRow.addClass('jf-ct-e');
                            break;
                        default:
                            this.modalFooterRow.addClass('jf-ct-e');
                            break;
                    }
                    break;
            }

            switch (this.config.style) {
                case 'alert':
                    this.container.addClass('alert');
                    this.modalHeadingIcon.addClass(this.config.icon);
                    this.modalHeadingRow.prepend(this.modalHeadingIcon);
                    this.modalColumn.css({ maxWidth: '350px' });
                    break;
                case 'fullscreen':
                    this.modalHeadingIcon.addClass(this.config.icon);
                    this.modalHeadingContent.prepend(this.modalHeadingIcon);
                    this.container.addClass('fullscreen');
                    break;
            }

            if (this.config.buttons.cancel) {
                this.cancelButton = new Button(this.config.buttons.cancelButton.class);
                this.cancelButton.html(this.config.buttons.cancelButton.label);
                this.modalFooterContent.append(this.cancelButton);
                this.cancelButton.click(function() {
                    self.close();
                });
            }

            if (this.config.buttons.action) {
                if (this.config.buttons.actionButtons.length > 0) {

                    for (var i in this.config.buttons.actionButtons) {
                        var buttonConfig = this.config.buttons.actionButtons[i];

                        this.setButton(buttonConfig);
                    }
                }
            }

            // Constroi o layout
            this.modalHeadingRow.append(this.modalHeadingContent);
            // this.modalHeadingRow.append(this.modalHeadingTools);
            this.modalHeadingContainer.append(this.modalHeadingRow);
            this.modalHeading.append(this.modalHeadingContainer);

            this.modalFooterRow.append(this.modalFooterContent);
            this.modalFooterContainer.append(this.modalFooterRow);
            this.modalFooter.append(this.modalFooterContainer);


            if (this.config.build.header) {
                if (this.config.build.header !== 'false') {
                    this.modalPanel.append(this.modalHeading);

                    if (this.config.headerClass) {
                        this.modalHeadingContent.addClass(this.config.headerClass);
                    }
                }
            }

            this.modalPanel.append(this.modalContent);

            if (this.config.build.content.padding) {
                if (!this.config.build.content.padding || this.config.build.content.padding == 'false') {
                    this.modalContent.addClass('no-pd');
                }
            }

            if (this.config.build.footer) {
                if (this.config.build.footer !== 'false') {
                    this.modalPanel.append(this.modalFooter);
                }
            }

            if (!this.config.build.divider || this.config.build.divider == 'false') {
                this.modalHeading.addClass('no-dv');
                this.modalFooter.addClass('no-dv');
            }

            if (this.config.height) {
                self.modalPanel.css('height', this.config.height);
            }

            if (this.config.panelClass) {
                self.modalPanel.addClass(this.config.panelClass);
            }

            this.modalColumn.append(this.modalPanel);
            this.modalAlign.append(this.modalColumn);
            this.container.append(this.modalAlign);

            //ESC Click Fechar
            $(document).keyup(function(e) {
                if (e.keyCode === 27) {
                    self.close();
                }
            });

        }
        //Init fim
        /**
         * Define a largura do modal
         * @param string size 
         */
    this.setSize = function(size) {

        // Reseta a classe da colune
        this.modalColumn.removeClass();

        if (this.config.maxWidth) {
            this.modalColumn.addClass('col');
            this.modalColumn.css({ maxWidth: this.config.maxWidth + 'px' });
        } else {
            switch (this.config.style) {
                case 'alert':
                    this.modalColumn.addClass('col');
                    break;
                default:
                    switch (size) {
                        case 'xsmall':
                            this.modalColumn.addClass('col-sm-4 col-md-3 col-lg-2');
                            break;
                        case 'small':
                            this.modalColumn.addClass('col-sm-6 col-md-5 col-lg-4');
                            break;
                        case 'medium':
                            this.modalColumn.addClass('col-sm-8 col-md-7 col-lg-6');
                            break;
                        case 'large':
                            this.modalColumn.addClass('col-sm-10 col-md-9 col-lg-8');
                            break;
                        case 'xlarge':
                            this.modalColumn.addClass('col-sm-12 col-md-11 col-lg-10');
                            break;
                        case 'full':
                            this.modalColumn.addClass('col-12');
                            break;
                        case 'auto':
                            this.modalColumn.addClass('col-auto');
                            break;
                        default:
                            this.modalColumn.addClass('col-sm-8 col-md-7 col-lg-6');
                            break;
                    }
                    break;
            }
        }
    }

    /**
     * Mostra o loader e esconde o conteudo
     */
    this.showLoader = function() {

        this.container.find('.loader-grid').removeClass('hd');
        this.container.find('.md-ct').addClass('hd');
    }

    /**
     * Esconde o loader e esconde o conteudo
     */
    this.hideLoader = function() {

        this.container.find('.loader-grid').addClass('hd');
        this.container.find('.md-ct').removeClass('hd');
    }

    /**
     * Define se o modal está carregando conteúdo ou executando alguma ação
     * @param bool isLoading
     */
    this.setLoading = function(isLoading) {
        let self = this;

        setTimeout(function() {
            self.config.isLoading = isLoading;
        }, TIME_SLOW);
    }

    /**
     * Adiciona um botão ao formulário
     * @param object buttonConfig 
     */
    this.setButton = function(buttonConfig) {
        self = this;
        let button;
        if (buttonConfig.class) {
            button = new Button(buttonConfig.class);
        } else {
            button = new Button('btn-ac');
        }

        button.html(buttonConfig.label);

        button.click(function() {
            if (buttonConfig.function) {
                buttonConfig.function.call(this, buttonConfig.params);
            }
        });

        if (buttonConfig.key) {
            $(document).on('keypress', function(event) {
                if (event.which == buttonConfig.key) {

                    if (buttonConfig.block) {
                        if (!self.config.isLoading) {
                            self.config.isLoading = true;
                            button.click();
                        }
                    } else {
                        button.click();
                    }

                }
            });
        }
        this.modalFooterContent.append(button);
    }

    /**
     * Altera conteúdo do modal
     * @param object view 
     */
    this.setContent = function(view) {
        this.modalContent.html('');
        this.modalContent.append(view);
    }

    /**
     * Define o elemento pai do modal
     */
    this.setTarget = function(target) {
        this.config.target = target;
    }

    // Remove o rodapé
    this.removeFooter = function() {
        this.modalFooter.remove();
    }

    // Animação negativa do modal
    //this.shake = function() {
    //    let self = this;
    //
    //    this.container.addClass('shake');
    //    setTimeout(function () {
    //        self.container.removeClass('shake');
    //    }, TIME_SLOW);
    //}

    // Mostra o modal na tela
    this.show = function() {
        this.container.removeClass('hide');
        $(this.config.target).append(this.getView());
    }

    // Define o callback de fechamento do modal
    this.setOnClose = function(onClose) {
        this.onClose = onClose;
    }

    // Define o callback de fechamento do modal
    this.setOnBeforeClose = function(onBeforeClose) {
        this.onBeforeClose = onBeforeClose;
    }

    // Elimina o modal
    this.close = function() {
        let self = this;

        if (this.onClose) {
            this.onClose.call();
        }

        this.container.addClass('hide');

        setTimeout(function() {
            self.container.remove();
            if (self.config.onDestroy) {
                self.onDestroy();
            }
        }, TIME_DEFAULT);
    }

    // Elimina o modal quando dentro de outro modal
    this.closeModalInside = function() {

        var container = $('div.md-bg');

        container.addClass('hide');

        setTimeout(function() {
            container.remove();
        }, TIME_MEDIUM);
    }

    // Exclui o objeto da memória
    this.onDestroy = function() {
        delete this;
    }

    /**
     * @returns view
     */
    this.getView = function() {
        return this.container;
    }
}