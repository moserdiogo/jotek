/**
 * Funçoes 
 */
// Específico para remoção da quebra de linhas
function replaceAll(str) {
    var index = str.indexOf('\n');
    while (index > -1) {
        str = str.replace('\n', '');
        index = str.indexOf('\n');
    }
    return (str);
}

// Descriptografa o resultado vindo da API
function getResponse(responseText) {
    let encryption = new Encrypt();

    let data = encryption.decrypt(replaceAll(responseText), CRIPTO_TOKEN);
    data = JSON.parse(data);

    return data;
}

// Classe para gerar token para uso em IDs de elementos HTML
class IDToken {
    constructor() {
        this.token = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    }

    /**
     * @returns {string} token
     */
    getToken() {
        return this.token;
    }
}