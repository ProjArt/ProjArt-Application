import api from "../../../public/docs/collection.json";
const API_URL = "http://localhost:8000/api/";
const baseUrl = "http://localhost:8000/";
export const API = {
    register: {
        method: "POST",
        path: () => `${API_URL}register`,
    },
    login: {
        method: "POST",
        path: () => `${API_URL}login`,
    },
    events: {
        method: "GET",
        path: () => `${API_URL}events`,
    },
    newEvents: {
        method: "POST",
        path: () => `${API_URL}events`,
    },
};

api.item.forEach((group) => {
    group.item.forEach((route) => {
        let name = route.name.split(" ").map((word, index) => {
            word = word.toLowerCase();
            return index === 0
                ? word
                : word.charAt(0).toUpperCase() + word.slice(1);
        });
        name = name.join("");
        API[name] = {
            method: route.request.method,
            path: () => `${baseUrl}${route.request.url.path}`,
        };
    });
});
