/**
 * @required cripto-js
 * @author Moser Diogo
 * Classe para criptografia e descriptografia
 */
function Encrypt() {
    let self = this;

    this.config = {
        method: 'AES-256-CBC'
    }

    /**
     * @let integer 
     */
    this.encryptMethodLength = function() {
        let encryptMethod = this.config.method;
        let aesNumber = encryptMethod.match(/\d+/)[0];
        return parseInt(aesNumber);
    }

    /**
     * @param string value
     * @param string key
     * @return string
     */
    this.decrypt = function(value, key) {
        let json = JSON.parse(CryptoJS.enc.Utf8.stringify(CryptoJS.enc.Base64.parse(value)));
        let salt = CryptoJS.enc.Hex.parse(json.salt);
        let iv = CryptoJS.enc.Hex.parse(json.iv);
        let encrypted = json.ciphertext;
        let iterations = parseInt(json.iterations);

        if (iterations <= 0) {
            iterations = 49;
        }

        let encryptMethodLength = (this.encryptMethodLength() / 4);
        let hashKey = CryptoJS.PBKDF2(key, salt, {'hasher': CryptoJS.algo.SHA512, 'keySize': (encryptMethodLength / 8), 'iterations': iterations});
        let decrypted = CryptoJS.AES.decrypt(encrypted, hashKey, {'mode': CryptoJS.mode.CBC, 'iv': iv});

        return decrypted.toString(CryptoJS.enc.Utf8);
    }

    /**
     * @param string value
     * @param string key
     * @return string
     */
    this.encrypt = function(value, key) {
        let iv = CryptoJS.lib.WordArray.random(16);
        let salt = CryptoJS.lib.WordArray.random(128);
        let iterations = 49;
        let encryptMethodLength = (this.encryptMethodLength() / 4);
        let hashKey = CryptoJS.PBKDF2(key, salt, {'hasher': CryptoJS.algo.SHA512, 'keySize': (encryptMethodLength / 8), 'iterations': iterations});

        let encrypted = CryptoJS.AES.encrypt(value, hashKey, {'mode': CryptoJS.mode.CBC, 'iv': iv});
        let encryptedString = CryptoJS.enc.Base64.stringify(encrypted.ciphertext);

        let output = {
            'ciphertext': encryptedString,
            'iv': CryptoJS.enc.Hex.stringify(iv),
            'salt': CryptoJS.enc.Hex.stringify(salt),
            'iterations': iterations
        };

        return CryptoJS.enc.Base64.stringify(CryptoJS.enc.Utf8.parse(JSON.stringify(output)));
    }
}