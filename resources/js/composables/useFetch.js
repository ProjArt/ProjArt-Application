import { API } from "../stores/api";
import axios from "axios";
/**
 * It takes a url, a token, a method and a body and returns a response
 * @param {FetchParameters} params - FetchParameters
 * @returns The response from the fetch request.
 *
 * interface FetchParameters {
 *      url: string;
 *      method: string;
 *      token?: string;
 *      data?: Object;
 *  }
 */
async function useFetch(params) {
    const [url, token, method, data] = [
        params.url,
        params.token,
        params.method,
        params.data,
    ];

    const apiToken = localStorage.getItem("token");
    console.log({ apiToken }, { token });
    const headers = {
        Accept: "application/json",
        Authorization: `Bearer ${token || apiToken}`,
    };

    const requestOptions = {
        url: url,
        method: method,
        headers: headers,
        data: data,
    };

    const response = await axios(requestOptions);
    return response.data;
}

export default useFetch;
