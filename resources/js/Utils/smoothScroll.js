module.exports = function(top = 0, left = 0, behavior = "smooth") {
    window.scroll({
        top,
        left,
        behavior,
    });
};
