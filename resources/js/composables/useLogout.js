const useLogout = () => {
    localStorage.removeItem("token");
    window.location.href = window.location.origin + "/register";
};
export default useLogout;