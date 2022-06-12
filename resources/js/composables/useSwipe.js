function useSwipe({ element = document, excepts = [], onCreated = () => {}, precision = 100, onSwipeLeft = () => {}, onSwipeRight = () => {}, onSwipeUp = () => {}, onSwipeDown = () => {} } = {}) {
    let touchstartX;
    let touchstartY;
    let touchendX;
    let touchendY;

    onCreated();
    element.addEventListener(
        "touchstart",
        function(event) {
            console.log("event", event);
            console.log("paths", event.path);
            for (const path of event.path) {
                if (path.className) {
                    console.log("classes", path.className);
                    for (const p of path.className.split(" ")) {
                        for (const except of excepts) {
                            if (p === except) return;
                        }
                    }
                }
            }
            touchstartX = event.changedTouches[0].screenX;
            touchstartY = event.changedTouches[0].screenY;
        }, { passive: true }
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
        }, { passive: true }
    );
}

export default useSwipe;