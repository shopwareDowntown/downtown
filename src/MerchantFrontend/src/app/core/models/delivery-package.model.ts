import {DeliveryBoy} from "./delivery-boy.model";

export interface DeliveryPackage {
    id?: string;
    content: string;
    street: string;
    zipCode: string;
    city: string;
    comment: string;
    status: string;
    deliveryBoy: DeliveryBoy
}
