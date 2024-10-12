import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useSupplierStore = defineStore("useSupplierStore", {
    state: () => ({
      suppliers: [],
      listSuppliers: [],
      trashed_suppliers: [],
      totalRecords: 0,
      errorGetSuppliers: false,
      errorAddedData: false,
      errorUpdateData: false,
      errorDeleteSupplier: false,
      filters: {
          search: ''
      }
    }),
    actions: {
        async getSupplierPerPage(page = 1, rows = 10) {
            this.errorGetSuppliers = false;
            try {
                const response = await apiServices.get("supplier/pagination", {
                    params: {
                        page,
                        rows,
                        ...this.filters
                    }
                });

                if (response.data.status_code === 200) {
                    this.suppliers = response.data.datas.suppliers.data;
                    this.totalRecords = response.data.datas.total;
                }
            } catch (error) {
                this.errorGetSuppliers = true;
                if (error?.response?.data?.status_code === 404) {
                    this.suppliers = [];
                    this.totalRecords = 0;
                    console.log(error.response.data, "404");
                }
            }
        },
        async getAllSupplierProduct() {
            try {
                this.errorGetSuppliers = false;

                const response = await apiServices.get("supplier");

                if (response.data.status_code === 200) {
                    this.suppliers = response.data.datas;
                }
            } catch (error) {
                this.errorGetSuppliers = true;
                if (error?.response?.data?.status_code === 404) {
                    this.suppliers = []
                    console.log(error.response.data, "404");
                }
            }
        },
        async getListSupplierBySpecificColumn($column = ["supplier_name", "address"]) {
        this.errorGetData = false;
        try {
            const response = await apiServices.get("supplier/get-supplier/by-specifiec-column", {
              params: {
                customColumn: $column
              }
            });
          
            if (response.data.status_code === 200) {
            this.listSuppliers = response.data.datas;
              
            }
          } catch (error) {
            this.errorGetData = true;
            if (error?.response?.data?.status_code === 404) {
              this.listSuppliers = [];
            }
          }
        },
        async getTrashedSupplier() {
            try {
                this.errorGetSuppliers = false;

                const response = await apiServices.get("supplier/trashed");

                if (response.data.status_code === 200) {
                    this.trashed_suppliers = response.data.datas;
                }
            } catch (error) {
                this.errorGetSuppliers = true;
                if (error?.response?.data?.status_code === 404) {
                    this.trashed_suppliers = []
                    console.log(error.response.data, "404");
                }
            }
        },
        async addSupplier(datas) {
            try {
                const response = await apiServices.post(
                    "supplier/add-supplier",
                    datas
                );

                if (response.data.status_code === 201) {
                    this.errorAddedData = false;
                    const addedSupplier = response.data.datas;

                    this.suppliers.splice(this.suppliers.length, 0, addedSupplier);
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },
        setFilter(key, value) {
            this.filters[key] = value;
        },
        async updateDataSupplier(newData, supplierId) {
            try {
                newData["_method"] = "PATCH";
                const response = await apiServices.patch(`supplier/edit-supplier/${supplierId}`,
                    newData
                );

                if (response.data.status_code === 200) {
                    this.errorUpdateData = false;
                    const updatedSupplier = response.data.datas.newUpdatedSupplier;
                    const indexOSupplier = this.suppliers.findIndex(
                        (p) => p.id === updatedSupplier.id
                    );
                    if (indexOSupplier !== -1) {
                        this.suppliers.splice(indexOSupplier, 1, updatedSupplier);
                    }
                }
            } catch (error) {
                this.errorUpdateData = true;
            }
        },
         async deleteSupplier(supplierId) {
            try {
                const response = await apiServices.delete(
                    `supplier/delete-supplier/${supplierId}`
                );

                if (response.data.status_code === 200) {
                    this.errorDeleteSupplier = false;
                    const trashedSupplier = response.data.datas.trashedSupplier

                    const indexOfSupplier = this.suppliers.findIndex((supplier) => supplier.id === supplierId);
                    if (indexOfSupplier !== -1) {
                        this.suppliers.splice(indexOfSupplier, 1);
                        this.trashed_suppliers.splice(this.trashed_suppliers.length, 0, trashedSupplier)
                    }
                }
            } catch (error) {
                this.errorDeleteSupplier = true;
            }
        },
        async restoreSupplier(supplierId) {
            try {
                const response = await apiServices.post(`supplier/restore-supplier/${supplierId}`)
                
                if (response.data.status_code === 200) {
                    this.errorAddedData = false;
                    const restoredSupplier = response.data.datas.restoredSupplier
                    const indexOfRestoredSupplier = this.trashed_suppliers.findIndex((trashed) => trashed.id === restoredSupplier.id)

                    if (indexOfRestoredSupplier !== -1) { 
                        this.trashed_suppliers.splice(indexOfRestoredSupplier, 1)
                    }
                    this.suppliers.splice(this.suppliers.length, 0, restoredSupplier)
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },
    },
    persist: true,
});
