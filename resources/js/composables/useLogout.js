import router from "../router/routes";
import { user } from "../stores/auth";


const useLogout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    localStorage.removeItem("theme");
    localStorage.removeItem("calendars");
    user.value = null;
    router.push("/");
};
export default useLogout;