import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useApotekStore = defineStore("useApotekStore", {
    state: () => ({
        apoteks: [],
        trashed_apoteks: [],
        totalRecords: 0,
        errorGetApoteks: false,
        errorAddedData: false,
        errorUpdateData: false,
        errorDeleteApotek: false,
        filters: {
            search: ''
        }
    }),
    actions: {
        async getApotekPerPage(page = 1, rows = 10) {
            this.errorGetApoteks = false;
            try {
                const response = await apiServices.get("apotek/pagination", {
                    params: {
                        page,
                        rows,
                        ...this.filters
                    }
                });

                if (response.data.status_code === 200) {
                    this.apoteks = response.data.datas.apoteks.data;
                    this.totalRecords = response.data.datas.total;
                }
            } catch (error) {
                this.errorGetApoteks = true;
                if (error?.response?.data?.status_code === 404) {
                    this.apoteks = [];
                    this.totalRecords = 0;
                    console.log(error.response.data, "404");
                }
            }
        },
        async getAllApotekProduct() {
            try {
                this.errorGetApoteks = false;

                const response = await apiServices.get("apotek");

                if (response.data.status_code === 200) {
                    this.apoteks = response.data.datas;
                }
            } catch (error) {
                this.errorGetApoteks = true;
                if (error?.response?.data?.status_code === 404) {
                    this.apoteks = []
                    console.log(error.response.data, "404");
                }
            }
        },
        async getTrashedApotek() {
            try {
                this.errorGetApoteks = false;

                const response = await apiServices.get("apotek/trashed");

                if (response.data.status_code === 200) {
                    this.trashed_apoteks = response.data.datas;
                }
            } catch (error) {
                this.errorGetApoteks = true;
                if (error?.response?.data?.status_code === 404) {
                    this.trashed_apoteks = []
                    console.log(error.response.data, "404");
                }
            }
        },
        async addApotek(datas) {
            try {
                const response = await apiServices.post(
                    "apotek/add-apotek",
                    datas
                );

                if (response.data.status_code === 201) {
                    this.errorAddedData = false;
                    const addedApotek = response.data.datas;

                    this.apoteks.splice(this.apoteks.length, 0, addedApotek);
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },
        setFilter(key, value) {
            this.filters[key] = value;
        },
        async updateDataApotek(newData, apotekId) {
            try {
                newData["_method"] = "PATCH";
                const response = await apiServices.patch(`apotek/edit-apotek/${apotekId}`,
                    newData
                );

                if (response.data.status_code === 200) {
                    this.errorUpdateData = false;
                    const updatedApotek = response.data.datas.newUpdatedApotek;
                    const indexOApotek = this.apoteks.findIndex(
                        (p) => p.id === updatedApotek.id
                    );
                    if (indexOApotek !== -1) {
                        this.apoteks.splice(indexOApotek, 1, updatedApotek);
                    }
                }
            } catch (error) {
                this.errorUpdateData = true;
            }
        },
         async deleteApotek(apotekId) {
            try {
                const response = await apiServices.delete(
                    `apotek/delete-apotek/${apotekId}`
                );

                if (response.data.status_code === 200) {
                    this.errorDeleteApotek = false;
                    const trashedApotek = response.data.datas.trashedApotek

                    const indexOfApotek = this.apoteks.findIndex((apotek) => apotek.id === apotekId);
                    if (indexOfApotek !== -1) {
                        this.apoteks.splice(indexOfApotek, 1);
                        this.trashed_apoteks.splice(this.trashed_apoteks.length, 0, trashedApotek)
                    }
                }
            } catch (error) {
                this.errorDeleteApotek = true;
            }
        },
        async restoreApotek(apotekId) {
            try {
                const response = await apiServices.post(`apotek/restore-apotek/${apotekId}`)
                
                if (response.data.status_code === 200) {
                    this.errorAddedData = false;
                    const restoredApotek = response.data.datas.restoredApotek
                    const indexOfRestoredApotek = this.trashed_apoteks.findIndex((trashed) => trashed.id === restoredApotek.id)

                    if (indexOfRestoredApotek !== -1) { 
                        this.trashed_apoteks.splice(indexOfRestoredApotek, 1)
                    }
                    this.apoteks.splice(this.apoteks.length, 0, restoredApotek)
                }
            } catch (error) {
                this.errorAddedData = true;
            }
        },
    },
    persist: true,
});
