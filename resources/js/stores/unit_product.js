import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useUnitProductStore = defineStore("useUnitProductStore", {
    state: () => ({
        units: [],
        trashed_units: [],
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
                if (error?.response?.data?.status_code === 404) {
                    this.units = []
                    console.log(error.response.data, "404");
                }
            }
        },
        async getTrashedUnit() {
            try {
                this.errorGetUnits = false;

                const response = await apiServices.get("unit/trashed");

                if (response.data.status_code === 200) {
                    this.trashed_units = response.data.datas;
                }
            } catch (error) {
                this.errorGetUnits = true;
                if (error?.response?.data?.status_code === 404) {
                    this.trashed_units = []
                    console.log(error.response.data, "404");
                }
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
                    const trashedUnit = response.data.datas.trashedUnit

                    const indexOfUnit = this.units.findIndex((unit) => unit.id === unitId);
                    if (indexOfUnit !== -1) {
                        this.units.splice(indexOfUnit, 1);
                        this.trashed_units.splice(this.trashed_units.length, 0, trashedUnit)
                    }
                }
            } catch (error) {
                this.errorDeleteUnit = true;
            }
        },
        async restoreUnit(unitId) {
            try {
                const response = await apiServices.post(`unit/restore-unit/${unitId}`)
                
                if (response.data.status_code === 200) {
                    this.errorAddedData = false;
                    const restoredUnit = response.data.datas.restoredUnit
                    const indexOfRestoredUnit = this.trashed_units.findIndex((trashed) => trashed.id === restoredUnit.id)

                    if (indexOfRestoredUnit !== -1) { 
                        this.trashed_units.splice(indexOfRestoredUnit, 1)
                    }
                    this.units.splice(this.units.length, 0, restoredUnit)
                }
            } catch (error) {
                this.errorAddedData = true;
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
