import api from "../../../public/docs/collection.json";

/**
 * API contain every routes of the API each route is an object with the following properties:
 * @attribute method {string} The method of the route (GET, POST, PUT, etc.)
 * @attribute path {function} Function that returns the path of the route, can take arguments
 *
 * @hint every route object name is a camelCase version of the route name
 * Routes'names and documentation is available at http://localhost:8000/docs/
 * @example  Get Calendar => API.getCalendar.path(param1, param2, ...)
 */
export const API = {};
const API_URL = getUrl() + "api/";
const BASE_URL = getUrl();

/**
 * Get and transform the api url to an usable url
 * @returns {string} the app url
 */
function getUrl() {
    console.log(api.variable[0].value + "/");
    return api.variable[0].value + "/";
}

/**
 * It takes a route string, splits it into parts, and returns a function that takes
 * the parameters of the route and returns the full url
 * @param route {string} The route you want to create a function for.
 * @returns A function that returns a string.
 */
function createFunction(route) {
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
    let returnement = "return `" + BASE_URL + pathString + "`";
    const path = new Function(...params, returnement);
    return path;
}

(function addRoutesToApiObject() {
    api.item.forEach((group) => {
        group.item.forEach((route) => {
            let name = route.name.split(" ").map((word, index) => {
                word = word.toLowerCase();
                return index === 0 ?
                    word :
                    word.charAt(0).toUpperCase() + word.slice(1);
            });
            name = name.join("");
            API[name] = {
                method: route.request.method,
                path: createFunction(route.request.url.path),
            };
        });
    });
})();
console.log({ API });