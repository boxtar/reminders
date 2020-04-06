module.exports = class Errors {
    constructor() {
        this.errors = {};
    }
    get(key) {
        if (this.has(key)) return this.errors[key];
    }
    set(key, message = "") {
        if (typeof key == "object") {
            console.log(key)
        }
        this.errors[key] = message;
    }
    has(key) {
        return !!this.errors[key];
    }
    any() {
        return Object.keys(this.errors).length;
    }
    clear(key="") {
        if(key.length && this.has(key)) {
            const msg = this.errors[key]
            delete this.errors[key]
            return msg;
        }
        this.errors = {};
    }
}
