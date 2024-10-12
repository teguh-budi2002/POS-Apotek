import axios from "axios";
import Cookies from "js-cookie";

const getCSRFToken = async () => {
    try {
        await axios.get("/sanctum/csrf-cookie");
    } catch (error) {
        console.error("ERROR CSRF TOKEN MISMATCH", error);
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
    const CSRF_COOKIE = Cookies.get('XSRF-TOKEN');
    const REQUEST_METHOD = config.method;
    const AUTH_TOKEN = Cookies.get("token")
    
    if ((REQUEST_METHOD === 'post' || REQUEST_METHOD === 'put' || REQUEST_METHOD === 'patch' || REQUEST_METHOD === 'delete') && !CSRF_COOKIE) {
        await getCSRFToken()
    }    

    if (AUTH_TOKEN) {
        config.headers.Authorization = `Bearer ${AUTH_TOKEN}`;
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

export default apiServices;
