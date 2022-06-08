import router from "../router/routes";
import { user } from "../stores/auth";


const useLogout = () => {
    localStorage.removeItem("token");
    user.value = null;
    router.push("/");
};
export default useLogout;