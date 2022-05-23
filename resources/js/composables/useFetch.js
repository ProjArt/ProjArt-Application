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

    const headers = {
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
    };

    const requestOptions = {
        url: url,
        method: method,
        config: { headers },
        data: data,
    };

    try {
        const response = await axios(requestOptions);
        return response.data;
    } catch (error) {
        console.log({ error });
    }
}

export default useFetch;
