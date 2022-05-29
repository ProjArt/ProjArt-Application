const useLogout = () => {
    localStorage.removeItem("token");
    window.location.href = process.env.MIX_BASE_URL;
};
export default useLogout;