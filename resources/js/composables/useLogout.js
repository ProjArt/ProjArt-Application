const useLogout = () => {
    localStorage.removeItem("token");
    window.location.href = "/";
};
