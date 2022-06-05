function useSwipe({ onSwipeLeft = () => {}, onSwipeRight = () => {}, onSwipeUp = () => {}, onSwipeDown = () => {} } = {}) {
    let touchstartX;
    let touchstartY;
    let touchendX;
    let touchendY;

    document.addEventListener(
        "touchstart",
        function(event) {
            touchstartX = event.changedTouches[0].screenX;
            touchstartY = event.changedTouches[0].screenY;
        },
        false
    );

    document.addEventListener(
        "touchend",
        function(event) {
            touchendX = event.changedTouches[0].screenX;
            touchendY = event.changedTouches[0].screenY;
            if (touchendX < touchstartX) {
                onSwipeLeft();
            }

            if (touchendX > touchstartX) {
                onSwipeRight();
            }

            if (touchendY < touchstartY) {
                onSwipeUp();
            }

            if (touchendY > touchstartY) {
                onSwipeDown();
            }
        },
        false
    );
}

export default useSwipe;