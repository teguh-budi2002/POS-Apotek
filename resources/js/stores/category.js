import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useCategoryStore = defineStore("useCategoryStore", {
    state: () => ({
        categories: [],
        trashed_categories: [],
        totalRecords: 0,
        errorGetCategories: false,
        errorAddedData: false,
        errorUpdateData: false,
        errorDeleteCategory: false,
        filters: {
            search: ''
        }
    }),
    actions: {
        async getCategoryPerPage(page = 1, rows = 10) {
            this.errorGetCategories = false;
            try {
                const response = await apiServices.get("category/pagination", {
                    params: {
                        page,
                        rows,
                        ...this.filters
                    }
                });

                if (response.data.status_code === 200) {
                    this.categories = response.data.datas.categories.data;
                    this.totalRecords = response.data.datas.total;
                }
            } catch (error) {
                this.errorGetCategories = true;
                if (error?.response?.data?.status_code === 404) {
                    this.categories = [];
                    this.totalRecords = 0;
                    console.log(error.response.data, "404");
                }
            }
        },
        async getAllCategoryProduct() {
            this.errorGetCategories = false;
            try {
                const response = await apiServices.get('category')

                if (response.data.status_code === 200) {
                    this.categories = response.data.datas.categories;
                }
            } catch (error) {
                 this.errorGetCategories = true;
                if (error?.response?.data?.status_code === 404) {
                    this.categories = [];
                    console.log(error.response.data, "404");
                }
            }
        },
        setFilter(key, value) {
            this.filters[key] = value;
        },
        async addCategory(datas) {
            try {
                const response = await apiServices.post(
                    "category/add-category",
                    datas
                );

                if (response.data.status_code === 201) {
                    this.errorAddedData = false;
                    const addedCategory = response.data.datas;

                    this.categories.splice(this.categories.length, 0, addedCategory);
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },
        async getTrashedCategories() {
            try {
                this.errorGetCategories = false;

                const response = await apiServices.get("category/trashed");

                if (response.data.status_code === 200) {
                    this.trashed_categories = response.data.datas;
                }
            } catch (error) {
                this.errorGetCategories = true;
                if (error?.response?.data?.status_code === 404) {
                    this.trashed_categories = []
                    console.log(error.response.data, "404");
                }
            }
        },
        async updateDataCategory(newData, categoryId) {
            try {
                newData["_method"] = "PATCH";
                const response = await apiServices.patch(`category/edit-category/${categoryId}`,
                    newData
                );

                if (response.data.status_code === 200) {
                    this.errorUpdateData = false;
                    const updatedCategory = response.data.datas.newUpdatedCategory;
                    const indexOfProduct = this.categories.findIndex(
                        (p) => p.id === updatedCategory.id
                    );
                    if (indexOfProduct !== -1) {
                        this.categories.splice(indexOfProduct, 1, updatedCategory);
                    }
                }
            } catch (error) {
                this.errorUpdateData = true;
            }
        },
         async deleteCategory(categoryId) {
            try {
                const response = await apiServices.delete(
                    `category/delete-category/${categoryId}`
                );

                if (response.data.status_code === 200) {
                    this.errorDeleteCategory = false;
                    const trashedCategory = response.data.datas.trashedCategory

                    const indexOfCategory = this.categories.findIndex(
                        (category) => category.id === categoryId
                    );
                    if (indexOfCategory !== -1) {
                        this.categories.splice(indexOfCategory, 1);
                        this.trashed_categories.splice(this.trashed_categories.length, 0, trashedCategory)
                    }
                }
            } catch (error) {
                this.errorDeleteCategory = true;
            }
        },
        async restoreCategory(categoryId) {
            try {
                const response = await apiServices.post(`category/restore-category/${categoryId}`)
                
                if (response.data.status_code === 200) {
                    this.errorAddedData = false;
                    const restoredCategory = response.data.datas.restoredCategory
                    const indexOfRestoredCategory = this.trashed_categories.findIndex((trashed) => trashed.id === restoredCategory.id)

                    if (indexOfRestoredCategory !== -1) { 
                        this.trashed_categories.splice(indexOfRestoredCategory, 1)
                    }
                    this.categories.splice(this.categories.length, 0, restoredCategory)
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },
        async setStatus(status, categoryId) {
            this.errorUpdateData = false;
             try {
                 const response = await apiServices.patch(`category/set-status/${categoryId}`, {
                     'status': status
                 })

                 if (response.data.status_code == 200) {
                    const updatedCategory = response.data.datas.newUpdatedCategory;
                    const indexOfCategory = this.categories.findIndex(
                        (p) => p.id === updatedCategory.id
                    );
                    if (indexOfCategory !== -1) {
                        this.categories.splice(indexOfCategory, 1, updatedCategory);
                    }
                 }
             } catch (error) {
                 this.errorUpdateData = true
             }
         }
    },
    persist: true,
});
