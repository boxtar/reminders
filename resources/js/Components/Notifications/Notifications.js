class Notifications {
    constructor() {
        this.data = [];
        setInterval(() => this.update(), 1000);
    }

    // Add a notification
    add(text, type = types.info, sticky = false, lifetime = 3) {
        const id = Symbol(text + type);
        this.data.push({
            id,
            text,
            type,
            sticky,
            lifetime,
            timeElapsed: 0,
        });
        return id;
    }

    // Removes expired notifications and updates time remaining indicator of
    // notifications that have not yet expired.
    update() {
        if (this.data.length > 0) {
            this.data = this.data
                .map(this.updateNotification.bind(this))
                .filter(this.isNotificationAlive.bind(this));
        }
    }

    /**
     * Updates the notifications timeElapsed if not sticky
     *
     * @param {Object} notification
     */
    updateNotification(notification) {
        return notification.sticky
            ? notification
            : { ...notification, timeElapsed: notification.timeElapsed + 1 };
    }

    /**
     * Returns true if notification is still active, false if expired.
     * Whether or not a notification is active is based on the value
     * of the lifetime property. If >0 then still active.
     * Also, if sticky then it's always active until user closes.
     * @param {Object} notification
     */
    isNotificationAlive(notification) {
        return notification.sticky ? true : notification.lifetime - notification.timeElapsed >= 0;
    }

    // Remove a notification
    remove(id) {
        this.data = this.data.filter(notification => notification.id !== id);
    }

    // Find a notification by id
    getById(id) {
        const result = this.data.filter(notification => notification.id === id);
        if (result.length) {
            return result[0];
        }
        return false;
    }

    getAll() {
        return this.data;
    }
}

const types = {
    info: {
        text: "text-white",
        bg: "bg-blue-500",
    },
    success: {
        text: "text-white",
        bg: "bg-green-500",
    },
    error: {
        text: "text-white",
        bg: "bg-red-500",
    },
    warning: {
        text: "text-white",
        bg: "bg-orange-400",
    },
};

module.exports = {
    Notifications,
    types,
};
