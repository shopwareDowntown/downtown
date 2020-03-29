import {ServiceBookingDate} from "./service-booking-date.model";
import {Product} from "./product.model";

export interface ServiceBookingTemplate {
  id?: string;
  type: string;
  dates: ServiceBookingDate[];
  product: Product;
}
