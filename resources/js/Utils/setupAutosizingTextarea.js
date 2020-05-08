module.exports = element => {
    // https://stackoverflow.com/a/5346855
    function resize() {
        element.style.height = "auto"; // Yes, this is required for it to work when the contents of the element are removed.
        element.style.height = element.scrollHeight + "px";
    }
    /* 0-timeout to get the already changed text */
    function delayedResize() {
        setTimeout(resize, 0);
    }
    element.addEventListener("change", resize);
    element.addEventListener("keydown", delayedResize);
    element.addEventListener("paste", delayedResize);
    element.addEventListener("cut", delayedResize);
    element.addEventListener("drop", delayedResize);
}
