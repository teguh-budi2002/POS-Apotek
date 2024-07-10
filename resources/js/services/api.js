import axios from "axios";
import { getToken } from "../helpers/getToken";

async function getCSRFToken() {
    try {
        const response = await axios.get("/sanctum/csrf-token");
        const csrfToken = response.data.csrf_token;

        axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;

        return csrfToken;
    } catch (error) {
        console.error(error);
    }
}

const apiServices = axios.create({
    baseURL: "/api/",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
    withCredentials: true,
});

apiServices.interceptors.request.use(async function (config) {
    const csrfToken = await getCSRFToken();
    const authToken = getToken;
    config.headers["X-CSRF-TOKEN"] = csrfToken;

    if (authToken) {
        config.headers.Authorization = `Bearer ${authToken}`;
    }
    return config;
});

export default apiServices;
