import { createToast } from "mosha-vue-toastify";
import "mosha-vue-toastify/dist/style.css";
function useToast(message, type) {
    const color = {
        success: "#00C851",
        warning: "#FFC107",
        info: "#2196F3",
        danger: "#FF5252",
        default: "#2196F3",
    };
    const config = {
        type: type,
        timeout: 1500,
        position: "top-center",
        showCloseButton: true,
        showIcon: true,
        transition: "slide",
        swipeClose: true,
        toastBackgroundColor: color[type],
    };
    createToast(message, config);
}
export default useToast;
