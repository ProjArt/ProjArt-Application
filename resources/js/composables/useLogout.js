import router from "../router/routes";


const useLogout = () => {
    localStorage.removeItem("token");
    router.push("/");
};
export default useLogout;