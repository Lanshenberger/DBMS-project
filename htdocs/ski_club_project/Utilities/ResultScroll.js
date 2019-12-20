/**
 * @file ResultScroll.js An external javascript file for scrolling down pages — used when a result of any type requires
 * the page to scroll.
 * @author Landon Shenberger
 */

/**
 * @function scrollToBottom() scrolls to the bottom of the page when called.
 */
function scrollToBottom() {
    window.scroll({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
}
