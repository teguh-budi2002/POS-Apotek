import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useCustomerStore = defineStore("useCustomerStore", {
    state: () => ({
        customers: [],
        trashed_customers: [],
        totalRecords: 0,
        errorGetCustomers: false,
        errorAddedData: false,
        errorUpdateData: false,
        errorDeleteCustomer: false,
        filters: {
            search: ''
        }
    }),
    actions: {
        async getCustomerPerPage(page = 1, rows = 10) {
            this.errorGetCustomers = false;
            try {
                const response = await apiServices.get("customer/pagination", {
                    params: {
                        page,
                        rows,
                        ...this.filters
                    }
                });

                if (response.data.status_code === 200) {
                    this.customers = response.data.datas.customers.data;
                    this.totalRecords = response.data.datas.total;
                }
            } catch (error) {
                this.errorGetCustomers = true;
                if (error?.response?.data?.status_code === 404) {
                    this.customers = [];
                    this.totalRecords = 0;
                    console.log(error.response.data, "404");
                }
            }
        },
        async getAllCustomerProduct() {
            try {
                this.errorGetCustomers = false;

                const response = await apiServices.get("customer");

                if (response.data.status_code === 200) {
                    this.customers = response.data.datas;
                }
            } catch (error) {
                this.errorGetCustomers = true;
                if (error?.response?.data?.status_code === 404) {
                    this.customers = []
                    console.log(error.response.data, "404");
                }
            }
        },
        async getTrashedCustomer() {
            try {
                this.errorGetCustomers = false;

                const response = await apiServices.get("customer/trashed");

                if (response.data.status_code === 200) {
                    this.trashed_customers = response.data.datas;
                }
            } catch (error) {
                this.errorGetCustomers = true;
                if (error?.response?.data?.status_code === 404) {
                    this.trashed_customers = []
                    console.log(error.response.data, "404");
                }
            }
        },
        async addCustomer(datas) {
            try {
                const response = await apiServices.post(
                    "customer/add-customer",
                    datas
                );

                if (response.data.status_code === 201) {
                    this.errorAddedData = false;
                    const addedCustomer = response.data.datas;

                    this.customers.splice(this.customers.length, 0, addedCustomer);
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },
        setFilter(key, value) {
            this.filters[key] = value;
        },
        async updateDataCustomer(newData, customerId) {
            try {
                newData["_method"] = "PATCH";
                const response = await apiServices.patch(`customer/edit-customer/${customerId}`,
                    newData
                );

                if (response.data.status_code === 200) {
                    this.errorUpdateData = false;
                    const updatedCustomer = response.data.datas.newUpdatedCustomer;
                    const indexOCustomer = this.customers.findIndex(
                        (p) => p.id === updatedCustomer.id
                    );
                    if (indexOCustomer !== -1) {
                        this.customers.splice(indexOCustomer, 1, updatedCustomer);
                    }
                }
            } catch (error) {
                this.errorUpdateData = true;
            }
        },
         async deleteCustomer(customerId) {
            try {
                const response = await apiServices.delete(
                    `customer/delete-customer/${customerId}`
                );

                if (response.data.status_code === 200) {
                    this.errorDeleteCustomer = false;
                    const trashedCustomer = response.data.datas.trashedCustomer

                    const indexOfCustomer = this.customers.findIndex((customer) => customer.id === customerId);
                    if (indexOfCustomer !== -1) {
                        this.customers.splice(indexOfCustomer, 1);
                        this.trashed_customers.splice(this.trashed_customers.length, 0, trashedCustomer)
                    }
                }
            } catch (error) {
                this.errorDeleteCustomer = true;
            }
        },
        async restoreCustomer(customerId) {
            try {
                const response = await apiServices.post(`customer/restore-customer/${customerId}`)
                
                if (response.data.status_code === 200) {
                    this.errorAddedData = false;
                    const restoredCustomer = response.data.datas.restoredCustomer
                    const indexOfRestoredCustomer = this.trashed_customers.findIndex((trashed) => trashed.id === restoredCustomer.id)

                    if (indexOfRestoredCustomer !== -1) { 
                        this.trashed_customers.splice(indexOfRestoredCustomer, 1)
                    }
                    this.customers.splice(this.customers.length, 0, restoredCustomer)
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },
    },
    persist: true,
});
