import { API } from "../stores/api";
/**
 * It takes a url, a token, a method and a body and returns a response
 * @param {FetchParameters} params - FetchParameters
 * @returns The response from the fetch request.
 *
 * interface FetchParameters {
 *      url: string;
 *      method: string;
 *      jtoken?: string;
 *      body?: Object;
 *  }
 */
const useFetch = async (params) => {
    const [url, token, method, body] = [
        params.url,
        params.token,
        params.method,
        params.body,
    ];

    const formdata = new FormData();
    if (body) {
        Object.entries(body).forEach(([key, value]) => {
            formdata.append(key, value);
        });
    }

    const myHeaders = new Headers();
    myHeaders.append("Accept", "application/json");
    myHeaders.append("Authorization", `Bearer ${token}`);

    const requestOptions = {
        method: method,
        headers: myHeaders,
        redirect: "follow",
        body: method === "POST" ? formdata : null,
    };

    let response = null;
    try {
        response = await fetch(url, requestOptions);
        response = await response.json();
        return response;
    } catch (error) {
        console.log({ error });
    }
};

export default useFetch;
