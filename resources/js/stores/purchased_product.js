import { defineStore } from "pinia";
import apiServices from "../services/api";

export const usePurchasedProductStore = defineStore("usePurchasedProductStore", {
  state: () => ({
    listOrderPurchasedProduct: [],
    listProducts: [],
    listProductSelected: [],
    listApoteks: [],
    listSuppliers: [],
    productIds: [],
    detailPurchasedProduct: {},
    errorGetData: false,
    errorAddedData: false,
    productDoesntHaveDefaultStockError: false,
    errorMessage: '',
    filters: {
      search: "",
    }
  }),
  actions: {
    async getListOrderPurchasedProduct(rows = 10) {
      this.errorGetData = false;
      try {
        const response = await apiServices.get("ordered-product/get-paginate-purchased-product", {
          rows,
          params: {
            ...this.filters
          }
        });        

        if (response.data.status_code === 200) {
          this.listOrderPurchasedProduct = response.data.datas.purchasedProducts.data;
        }
      } catch (error) {
        this.errorGetData = true;        
        if (error?.response?.data?.status_code === 404) {
          this.listOrderPurchasedProduct = [];
        }
      }
    },
    async getListProductsBySpecificColumn(column = ["unit_product_id", "product_code", "name", "unit_price", "profit_margin", "unit_selling_price"]) {
      this.errorGetData = false;
      try {
        const response = await apiServices.get("product/get-list-product/by-specific-column", {
          params: {
            customColumn: column,
            ...this.filters
          }
        });
        
        if (response.data.status_code === 200) {
          this.listProducts = response.data.datas;
          
        }
      } catch (error) {
        this.errorGetData = true;
        if (error?.response?.data?.status_code === 404) {
          this.listProducts = [];
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
    async AddPurchasedProduct(datas) {
      this.errorAddedData = false
      this.productDoesntHaveDefaultStockError = false
      this.errorMessage = ''
      try {
        const response = await apiServices.post("ordered-product/purchase-product", datas, {
          headers: {
            "Content-Type" : "multipart/form-data"
          }
        })
        console.log(response.data.datas);
        
        
      } catch (error) {
        this.errorAddedData = true
        
        if (error?.response?.data?.status_code === 400) {          
          if (error.response.data.datas.hasOwnProperty('isProductDoesntHaveDefaultStock')) {
            this.productDoesntHaveDefaultStockError = true
            this.errorMessage = error.response.data.datas.message;
          }
        }        
      }
    },
    setFilter(key, value) {
      this.filters[key] = value;
    },
    setSelectedListProducts(products) {
      this.listProductSelected.push(products);
    },
    removeSelectedListProducts(product) {
      const indexOfProduct = this.listProductSelected.findIndex((item) => item.id === product.id);
      
      if (indexOfProduct !== -1) {
        this.productIds.splice(indexOfProduct, 1);
        this.listProductSelected.splice(indexOfProduct, 1);
      }
    },
    setProductIds(id) {
      this.productIds.push(id);
    },
    setSelectedPurchasedProduct(purchasedProduct) { 
      this.detailPurchasedProduct = purchasedProduct;
    }
  },
  persist: true
})