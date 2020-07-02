const Errors = require("./Errors");

module.exports = class Form {
    constructor(data, action = "/", method = "post") {
        this.data = { ...data };
        this.errors = new Errors();
        this.action = action;
        this.method = method.toUpperCase();
    }

    // Form data
    get(key) {
        return this.data[key];
    }
    set(key, value) {
        this.data[key] = value;
    }
    has(key) {
        return !!this.get(key);
    }

    // Set the endpoint to post the form to
    setAction(action) {
        this.action = action;
    }

    // Set the method
    setMethod(method) {
        this.method = method.toUpperCase();
    }

    getMethod() {
        return this.method;
    }

    send() {
        if (this.action) {
            // Activate pre-send hook if set by user
            if (this.preSend && typeof this.preSend == "function") this.preSend();

            const result = fetch(this.action, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    ...this.data,
                    _METHOD: this.getMethod(),
                }),
            })
                .then(resp => {
                    return resp.json();
                })
                .then(({ data }) => {
                    if (data.errors) {
                        for (const error in data.errors) this.errors.set(error, data.errors[error]);
                    }
                    return data;
                });

            // Activate post-send hook if set by user
            if (this.postSend && typeof this.postSend == "function") this.postSend();

            return result;
        }
        return Promise.reject(new Error("You must set an action before sending a form"));
    }
};
