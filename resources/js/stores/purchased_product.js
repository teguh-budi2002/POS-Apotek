import { defineStore } from "pinia";
import apiServices from "../services/api";

export const usePurchasedProductStore = defineStore("usePurchasedProductStore", {
  state: () => ({
    listOrderPurchasedProduct: [],
    listProducts: [],
    listProductSelected: [],
    productIds: [],
    detailPurchasedProduct: {},
    errorGetData: false,
    errorAddedData: false,
    errorEditData: false,
    errorSavedPayment: false,
    addedDataSuccessfully: false,
    editDataSuccessfully: false,
    errorChangeStatusOrder: false,
    productDoesntHaveDefaultStockError: false,
    errorMessage: '',
    filters: {
      search: "",
      apotek: "",
      supplier: "",
      status_order: "",
      status_payment: "",
      user: "",
      start_date: "",
      end_date: "",
    },
    URL: 'ordered-product/get-paginate-purchased-product',
    NEXT_PAGE_URL: '',
    PREV_PAGE_URL: '',
  }),
  actions: {
    async getListOrderPurchasedProduct(url = this.URL) { 
      this.errorGetData = false;
      try {
        const response = await apiServices.get(url, {
          params: {
            filters: this.filters
          }
        });        
        
        if (response.data.status_code === 200) {
          this.listOrderPurchasedProduct = response.data.datas.purchasedProducts.data;
          this.NEXT_PAGE_URL = response.data.datas.purchasedProducts?.next_page_url;
          this.PREV_PAGE_URL = response.data.datas.purchasedProducts?.prev_page_url;
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
        
        if (response.data.status_code === 201) {
          this.addedDataSuccessfully = true
        }
      } catch (error) {
        this.errorAddedData = true
        this.addedDataSuccessfully = false

        if (error?.response?.data?.status_code === 400) {          
          if (error.response.data.datas.hasOwnProperty('isProductDoesntHaveDefaultStock')) {
            this.productDoesntHaveDefaultStockError = true
            this.errorMessage = error.response.data.datas.message;
          }
        }        
      }
    },
    async editPurchasedProduct(datas, purchaseProductId) {      
      this.errorEditData = false
      this.productDoesntHaveDefaultStockError = false
      this.errorMessage = ''
      try {
        const response = await apiServices.patch(`ordered-product/edit-purchased-product/${purchaseProductId}`, datas)
        
        if (response.data.status_code === 200) {
          this.editDataSuccessfully = true
        }
      } catch (error) {
        this.errorEditData = true
        this.editDataSuccessfully = false
      }
    },
    async paidOrder(datas) {
      try {
        this.errorSavedPayment = false
        const response = await apiServices.post(`ordered-product/paid-order/${this.detailPurchasedProduct.id}`, datas, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })

        if (response.data.status_code === 200) {
          const updatedDataOrder = await response.data.datas.updatedDataOrder
          const findIndex = this.listOrderPurchasedProduct.findIndex((item) => item.id === updatedDataOrder.id);
          
          if (findIndex !== -1) {
            this.listOrderPurchasedProduct.splice(findIndex, 1, updatedDataOrder);
          }
          this.detailPurchasedProduct = updatedDataOrder;
        }
      } catch (error) {        
        this.errorSavedPayment = true
      }
    },
    async changeStatusOrder(status_order, order_id) {
      this.errorChangeStatusOrder = false
      try {
        const response = await apiServices.post(`ordered-product/change-status-order/${order_id}`, { 
          status_order: status_order
         });

        if (response.data.status_code === 200) {
          const updatedDataOrder = await response.data.datas.updatedDataOrder
          const findIndex = this.listOrderPurchasedProduct.findIndex((item) => item.id === updatedDataOrder.id);
          
          if (findIndex !== -1) {
            this.listOrderPurchasedProduct.splice(findIndex, 1, updatedDataOrder);
          }
        }
      } catch (error) {
        this.errorChangeStatusOrder = true
        console.log(error)
      }
    },
    setFilter(key, value) {
      this.filters[key] = value;
    },
    setSelectedListProducts(products) {
      this.listProductSelected.push(products);
    },
    setInitialListProductSelected(products) {  
      this.listProductSelected = [];
      this.productIds = [];

      const dataOldProducts = products.map((item) => ({
        id: item.id,
        name: item.name,
        product_code: item.product_code,
        unit_price: item.unit_price,
        unit: {
          id: item.unit_product_id,
          name: item.unit_product_name,
        },
        qty: item.product_detail.qty,
        tax: item.product_detail.tax,
        discount: item.product_detail.discount,
        price_after_discount: item.product_detail.price_after_discount,
        batch_number: item.product_detail.batch_number,
        profit_margin: item.product_detail.profit_margin,
        unit_selling_price: item.product_detail.selling_price,
        total_price: item.product_detail.sub_total,
        expired_date_product: item.product_detail.expired_date_product,
      }));
      this.listProductSelected.push(...dataOldProducts);
    },
    removeSelectedListProducts(product) {
      const indexOfProduct = this.listProductSelected.findIndex((item) => item.id === product.id);
      
      if (indexOfProduct !== -1) {
        this.productIds.splice(indexOfProduct, 1);
        this.listProductSelected.splice(indexOfProduct, 1);
      }
    },
    setNullListProductSelected() {
      this.productIds = [];
      this.listProductSelected = [];
    },
    setProductIds(id) {
      this.productIds.push(id);
    },
    setNullProductIds() {
      this.productIds = [];
    },
    setSelectedPurchasedProduct(purchasedProduct) { 
      this.detailPurchasedProduct = purchasedProduct;
    },
    setNullFilters() {
      this.filters = {
        search: "",
        apotek: "",
        supplier: "",
        status_order: "",
        status_payment: "",
        user: "",
        start_date: "",
        end_date: "",
      }
    },
    resetAddedDataStatus() {
      this.addedDataSuccessfully = false
    },
    resetEditDataStatus() {
      this.editDataSuccessfully = false
    }
  },
  persist: true
})