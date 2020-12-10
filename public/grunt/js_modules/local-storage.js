/**
 * 
 */
function LocalStorage() {

    let self = this;

    let tokenId = new IDToken();

    this.config = {
        session: 'filter'
    }

    // Inicializa
    this.init = function() {

        let storedData = self.getLocalStorage();

        if (storedData) {
            this.data = storedData;
        } else {

        }
    }

    // Salva os filtros no localstorage
    this.createFilter = function(data) {

        let encrypt = new Encrypt();
        let filter = JSON.stringify(data);
        localStorage.setItem(this.config.session, encrypt.encrypt(filter, API_KEY_TOKEN));
    }

    // Remove o item do localstorage
    this.removeFilter = function() {

        localStorage.removeItem(this.config.session);
    }

    // 
    this.editFilter = function(data) {

        let edit = self.getLocalStorage();

        // console.log('Data ->')
        // console.log(data)
        // console.log('Edit ->')
        // console.log(edit)

        let object3 = {...edit, ...data }

        self.createFilter(object3);

        console.log('Result ->')
        console.log(object3)
    }

    // 
    this.getFilter = function() {
        let filter = self.getLocalStorage();
        return filter;
    }

    /**
     * @return object
     */
    this.getLocalStorage = function() {
        let data;
        let encrypt = new Encrypt();

        if (localStorage.getItem(this.config.session)) {
            data = encrypt.decrypt(localStorage.getItem(this.config.session), API_KEY_TOKEN);
        } else {
            data = localStorage.getItem(this.config.session);
        }

        if (data) {
            return JSON.parse(data);
        }
    }
}