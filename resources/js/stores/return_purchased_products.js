import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useReturnPurchasedProductsStore = defineStore('useReturnPurchasedProductsStore', {
  state: () => ({
    listReturnPurchasedProducts: [],
    detailReturnPurchasedProduct: {},
    listDetailProductWantToReturn: [],
    listSuppliers: [],
    listApoteks: [],
    refNumbers: [],
    productIds: [],
    filters: {
      search: "",
      apotek: "",
      supplier: "",
      return_status: "",
      user: "",
      start_date: "",
      end_date: "",
    },
    errorGetData: false,
    errorAddedData: false,
    errorUpdatedData: false,
    NEXT_PAGE_URL: '',
    PREV_PAGE_URL: '',
    URL: 'return-product/get-paginate-return-product',
  }),
  actions: {
    async getDetailProductWantToReturn(ref_num) {
      this.errorGetData = false;
      try {
        const response = await apiServices.post("ordered-product/list-product-for-return", {
          reference_number: ref_num
        });
        
        if (response.data.status_code === 200) {
          this.listDetailProductWantToReturn = response.data.datas; 
        }
      } catch (error) {
        console.log(error.response);
        
        this.errorGetData = true;
        if (error?.response?.data?.status_code === 404) {
          this.products = [];
        }
      }
    },
    async getListReturnPurchasedProducts(url = this.URL) { 
      this.errorGetData = false;
      try {
        const response = await apiServices.get(url, {
          params: {
            filters: this.filters
          }
        });        
        // console.log(response.data.datas.returnPurchasedProducts.data);
        
        if (response.data.status_code === 200) {
          this.listReturnPurchasedProducts = response.data.datas.returnPurchasedProducts.data;
          this.NEXT_PAGE_URL = response.data.datas.purchasedProducts?.next_page_url;
          this.PREV_PAGE_URL = response.data.datas.purchasedProducts?.prev_page_url;
        }
      } catch (error) {
        this.errorGetData = true;        
        if (error?.response?.data?.status_code === 404) {
          this.listReturnPurchasedProducts = [];
        }
      }
    },
    async getListSuppliers($column = ["supplier_name", "address"]) {
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
    async getListApoteks($column = ["name_of_apotek"]) {
      this.errorGetData = false;
      try {
        const response = await apiServices.get("apotek/get-apotek/by-specifiec-column", {
          params: {
            customColumn: $column
          }
        });
        
        if (response.data.status_code === 200) {
          this.listApoteks = response.data.datas;
          
        }
      } catch (error) {
        this.errorGetData = true;
        if (error?.response?.data?.status_code === 404) {
          this.listApoteks = [];
        }
      }
    },
    async getListRefNumber() {
      this.errorGetData = false;
      try {
        const response = await apiServices.get("ordered-product/get-all-ref-numbers");
        
        if (response.data.status_code === 200) {
          this.refNumbers = response.data.datas;
        }
      } catch (error) {
        this.errorGetData = true;
      }
    },
    async AddReturnedProduct(datas) {
      this.errorAddedData = false;
      try {
        const response = await apiServices.post("return-product/add-return-product", datas);
        
      } catch (error) {
        this.errorAddedData = true;
      }
    },
    async editReturnedProduct(datas, return_ref_num) { 
      this.errorUpdatedData = false;
      try {
        const response = await apiServices.patch(`return-product/edit-return-product/${return_ref_num}`, datas);

        // if (response.data.status_code === 200) {
        //   const editedReturnProduct = response.data.datas.editedReturnProduct;
        //   const indexOfReturn = this.listReturnPurchasedProducts.findIndex((rp) => rp.return_reference_number === editedReturnProduct.return_reference_number);

        //   if (indexOfReturn !== -1) {
        //     this.listReturnPurchasedProducts.splice(indexOfReturn, 1, editedReturnProduct);
        //   }
        // }
        
      } catch (error) {
        this.errorUpdatedData = true;
      } 
    },
    setFilter(key, value) {
      this.filters[key] = value;
    },
    setNullFilters() {
      this.filters = {
        search: "",
        apotek: "",
        supplier: "",
        return_status: "",
        user: "",
        start_date: "",
        end_date: "",
      }
    },
    setFilterType(type) {
      this.filterType = type;
    },
    setProductIds(id) {
      this.productIds.push(id);
    },
    setNullListReturnPurchasedProducts() {
      this.productIds = [];
      this.listDetailProductWantToReturn = [];
    },
    setNullRefNumbers() {
      this.refNumbers = [];
    },
    setNullDetailProductWantToReturn() {
      this.listDetailProductWantToReturn = [];
    },
    setSelectedReturnPurchasedProduct(returnPurchasedProduct) { 
      this.detailReturnPurchasedProduct = returnPurchasedProduct;
    },
  },
  persist: true
})