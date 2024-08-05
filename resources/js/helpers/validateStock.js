export const validateStockLevels = (datas) => {
    if (
        datas.minimum_stock_level > datas.stock ||
        (datas.maximum_stock_level !== null && datas.minimum_stock_level > datas.maximum_stock_level) ||
        (datas.maximum_stock_level !== null && (datas.maximum_stock_level < datas.stock || datas.maximum_stock_level < datas.minimum_stock_level))
    ) {

        if (datas.maximum_stock_level !== null && (datas.maximum_stock_level < datas.stock || datas.maximum_stock_level < datas.minimum_stock_level)) {
            return {
                severity: "error",
                life: 3000,
                summary: "Update Stock Product Failed",
                detail: "Maximum Stock Tidak Boleh Lebih Kecil Dari Stock & Min Stock.",
            };
        }

        return {
            severity: "error",
            life: 3000,
            summary: "Update Stock Product Failed",
            detail: "Minimum Stock Tidak Boleh Lebih Besar Dari Stock & Max Stock.",
        };
    }

    return null;
};