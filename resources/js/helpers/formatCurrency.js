export const formatCurrencyIDR = (val) => {
    return val
        ? val.toLocaleString("id-ID", { style: "currency", currency: "IDR" })
        : "";
};
