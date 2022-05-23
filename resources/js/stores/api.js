const API_URL = "http://localhost:8000/api/";
export const API = {
    register: {
        method: "POST",
        path: () => `${API_URL}register`,
    },
    login: {
        method: "POST",
        path: () => `${API_URL}login`,
    },
    test: {
        method: "GET",
        path: () => `${API_URL}`,
    },
};
