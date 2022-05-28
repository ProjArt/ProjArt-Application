import api from "../../../public/docs/collection.json";
const API_URL = "http://localhost:8000/api/";
const baseUrl = "http://localhost:8000/";

/**
 * Contain every routes of the API each route is an object with the following properties:
 * @attribute method {string} The method of the route (GET, POST, PUT, etc.)
 * @attribute path {function} Function that returns the path of the route, can take arguments
 *
 * @hint every route object name is a camelCase version of the route name
 * Routes'names and documentation is available at http://localhost:8000/docs/
 * @example  Get Calendar => getCalendar
 */
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

/**
 * It takes a route string, splits it into parts, and returns a function that takes
 * the parameters of the route and returns the full url
 * @param route {string} The route you want to create a function for.
 * @returns A function that returns a string.
 */
const createFunction = (route) => {
    const urlParts = route.split("/");
    const params = [];
    let pathString = "";
    urlParts.forEach((part) => {
        if (part.startsWith(":")) {
            params.push(part.replace(":", ""));
            pathString += "${" + part.replace(":", "") + "}/";
        } else {
            pathString += part + "/";
        }
    });
    pathString = pathString.slice(0, -1) + "";
    let returnement = "return `" + baseUrl + pathString + "`";
    const path = new Function(...params, returnement);
    return path;
};

(function addRoutesToApiObject() {
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
                path: createFunction(route.request.url.path),
            };
        });
    });
})();
