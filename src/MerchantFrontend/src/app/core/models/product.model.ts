import {ServiceBookingTemplate} from "./service-booking-template.model";

export interface Product {
  id?: string;
  name: string;
  description: string;
  productType: string;
  active: boolean;
  media?: MediaData[];
  price?: number;
  tax?: number;
  serviceBookingTemplate?: ServiceBookingTemplate;
}

export interface ProductListData {
  data: Product[];
  total: number;
}

export interface MediaData {
  id: string,
  url: string
}
