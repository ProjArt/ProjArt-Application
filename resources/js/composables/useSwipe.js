function useSwipe({ element = document, excepts = [], onCreated = () => {}, precision = 100, onSwipeLeft = () => {}, onSwipeRight = () => {}, onSwipeUp = () => {}, onSwipeDown = () => {} } = {}) {
    let touchstartX;
    let touchstartY;
    let touchendX;
    let touchendY;

    onCreated();
    element.addEventListener(
        "touchstart",
        function(event) {
            for (const path of event.path) {
                for (const except of excepts) {
                    if (path.className === except) return;
                }
            }
            touchstartX = event.changedTouches[0].screenX;
            touchstartY = event.changedTouches[0].screenY;
        },
        false
    );

    element.addEventListener(
        "touchend",
        function(event) {
            touchendX = event.changedTouches[0].screenX;
            touchendY = event.changedTouches[0].screenY;
            if (touchendX < touchstartX - precision) {
                onSwipeLeft();
            }

            if (touchendX > touchstartX + precision) {
                onSwipeRight();
            }

            if (touchendY < touchstartY - precision) {
                onSwipeUp();
            }

            if (touchendY > touchstartY + precision) {
                onSwipeDown();
            }
        },
        false
    );
}

export default useSwipe;