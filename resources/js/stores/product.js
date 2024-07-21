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
        filters: {
            search: ''
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
                const response = await apiServices.get(`product?page=${page}`, {
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
    },
    persist: true,
});
