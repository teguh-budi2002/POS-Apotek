import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useProductStore = defineStore("useProductStore", {
    state: () => ({
        products: [],
        totalRecords: 0,
        errorGetProduct: false,
        errorAddedData: false,
        errorUpdateData: false,
        errorDeleteProduct: false,
        errorUpdateStock: false,
        filters: {
            search: ''
        },
        infoDeletedProduct: {
            id: null,
            name: null
        }
    }),
    getters: {
        getProducts: (state) => state.products,
        getTotalProducts: (state) => state.totalRecords,
    },
    actions: {
        async addProduct(datas) {
            try {
                const response = await apiServices.post(
                    "product/add-product",
                    datas,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                if (response.data.status_code === 201) {
                    this.errorAddedData = false;
                    const addedProduct = response.data.datas;

                    this.products.splice(this.products.length, 0, addedProduct);
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },

        async getProductsPerPage(page = 1, rows = 10) {
            this.errorGetProduct = false;
            try {
                const response = await apiServices.get(`product/pagination?page=${page}`, {
                    params: {
                        page,
                        rows,
                        ...this.filters
                    }
                });

                if (response.data.status_code === 200) {
                    this.products = response.data.datas.products.data;
                    this.totalRecords = response.data.datas.total;
                }
            } catch (error) {
                this.errorGetProduct = true;
                if (error?.response?.data?.status_code === 404) {
                    this.products = [];
                    this.totalRecords = 0;
                    console.log(error.response.data, "404");
                }
            }
        },

        setFilter(key, value) {
            this.filters[key] = value;
        },

        async updateDataProduct(newData, productId) {
            try {
                newData["_method"] = "PATCH";
                const response = await apiServices.patch(
                    `product/edit-product/${productId}`,
                    newData
                );

                if (response.data.status_code === 200) {
                    this.errorUpdateData = false;
                    const updatedProduct =
                        response.data.datas.newUpdatedProduct;
                    const indexOfProduct = this.products.findIndex(
                        (p) => p.id === updatedProduct.id
                    );
                    if (indexOfProduct !== -1) {
                        this.products.splice(indexOfProduct, 1, updatedProduct);
                    }
                }
            } catch (error) {
                this.errorUpdateData = true;
            }
        },
        async deleteProduct(productId) {
            try {
                const response = await apiServices.delete(
                    `product/delete-product/${productId}`
                );

                if (response.data.status_code === 200) {
                    this.errorDeleteProduct = false;

                    const index = this.products.findIndex(
                        (product) => product.id === productId
                    );
                    if (index !== -1) {
                        this.products.splice(index, 1);
                    }
                }
            } catch (error) {
                this.errorDeleteProduct = true;
            }
        },
        async setStatus(status, productId) {
            this.errorUpdateData = false;
             try {
                 const response = await apiServices.patch(`product/set-status/${productId}`, {
                     'status': status
                 })

                 if (response.data.status_code == 200) {
                    const updatedProduct = response.data.datas.newUpdatedProduct;
                    const indexOfProduct = this.products.findIndex(
                        (p) => p.id === updatedProduct.id
                    );
                    if (indexOfProduct !== -1) {
                        this.products.splice(indexOfProduct, 1, updatedProduct);
                    }
                 }
             } catch (error) {
                 this.errorUpdateData = true
             }
        },
        setInfoDeletedProduct(id, name) {
            this.infoDeletedProduct.id = id
            this.infoDeletedProduct.name = name
        },
        clearInfoDeletedProduct(id) {
            this.infoDeletedProduct.id = null
            this.infoDeletedProduct.name = null
        },

        async updateStockProduct(datas, stockId) {
            this.errorUpdateStock = false
            try {
                const response = await apiServices.patch(`stock/edit-stock/${stockId}`, datas)

                if (response.data.status_code === 200) {
                    const newUpdatedStockProduct = response.data.datas.newUpdatedStockProduct
                    const indexOfProduct = this.products.findIndex((product) => product.id === newUpdatedStockProduct.product_id)

                    if (indexOfProduct !== -1) { 
                        this.products.stock.splice(indexOfProduct, 1, newUpdatedStockProduct)
                    }
                }
            } catch (error) {
                this.errorUpdateData = true
            }
        }
    },
    persist: true,
});
