import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useUnitProductStore = defineStore("useUnitProductStore", {
    state: () => ({
        units: [],
        totalRecords: 0,
        errorGetUnits: false,
        errorAddedData: false,
        errorUpdateData: false,
        errorDeleteCategory: false,
        filters: {
            search: ''
        }
    }),
    actions: {
        async getUnitPerPage(page = 1, rows = 10) {
            this.errorGetUnits = false;
            try {
                const response = await apiServices.get("unit/pagination", {
                    params: {
                        page,
                        rows,
                        ...this.filters
                    }
                });

                if (response.data.status_code === 200) {
                    this.units = response.data.datas.units.data;
                    this.totalRecords = response.data.datas.total;
                }
            } catch (error) {
                this.errorGetUnits = true;
                if (error?.response?.data?.status_code === 404) {
                    this.units = [];
                    this.totalRecords = 0;
                    console.log(error.response.data, "404");
                }
            }
        },
        async getAllUnitProduct() {
            try {
                this.errorGetUnits = false;

                const response = await apiServices.get("unit");

                if (response.data.status_code === 200) {
                    this.units = response.data.datas;
                }
            } catch (error) {
                this.errorGetUnits = true;
            }
        },
        async addUnit(datas) {
            try {
                const response = await apiServices.post(
                    "unit/add-unit",
                    datas
                );

                if (response.data.status_code === 201) {
                    this.errorAddedData = false;
                    const addedUnit = response.data.datas;

                    this.units.splice(this.units.length, 0, addedUnit);
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },
        setFilter(key, value) {
            this.filters[key] = value;
        },
        async updateDataUnit(newData, unitId) {
            try {
                newData["_method"] = "PATCH";
                const response = await apiServices.patch(`unit/edit-unit/${unitId}`,
                    newData
                );

                if (response.data.status_code === 200) {
                    this.errorUpdateData = false;
                    const updatedUnit = response.data.datas.newUpdatedUnit;
                    const indexOUnit = this.units.findIndex(
                        (p) => p.id === updatedUnit.id
                    );
                    if (indexOUnit !== -1) {
                        this.units.splice(indexOUnit, 1, updatedUnit);
                    }
                }
            } catch (error) {
                this.errorUpdateData = true;
            }
        },
         async deleteUnit(unitId) {
            try {
                const response = await apiServices.delete(
                    `unit/delete-unit/${unitId}`
                );

                if (response.data.status_code === 200) {
                    this.errorDeleteUnit = false;

                    const index = this.units.findIndex(
                        (unit) => unit.id === unitId
                    );
                    if (index !== -1) {
                        this.units.splice(index, 1);
                    }
                }
            } catch (error) {
                this.errorDeleteUnit = true;
            }
        },
        async setStatus(status, unitId) {
            this.errorUpdateData = false;
             try {
                 const response = await apiServices.patch(`unit/set-status/${unitId}`, {
                     'status': status
                 })

                 if (response.data.status_code == 200) {
                    const updatedUnit = response.data.datas.newUpdatedUnit;
                    const indexOfUnit = this.units.findIndex(
                        (p) => p.id === updatedUnit.id
                    );
                    if (indexOfUnit !== -1) {
                        this.units.splice(indexOfUnit, 1, updatedUnit);
                    }
                 }
             } catch (error) {
                 this.errorUpdateData = true
             }
         }
    },
    persist: true,
});
