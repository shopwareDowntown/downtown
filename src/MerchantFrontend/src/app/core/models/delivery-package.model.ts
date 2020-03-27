import {DeliveryBoy} from "./delivery-boy.model";
import {Merchant} from "./merchant.model";

export interface DeliveryPackage {
    id?: string;
    content: string;
    recipientTitle: string;
    recipientFirstName: string,
    recipientLastName: string,
    recipientStreet: string;
    recipientZipcode: string;
    recipientCity: string;
    comment: string;
    status: string;
    price: number;
    deliveryBoy: DeliveryBoy;
    shippingMethod: string;
    merchant: Merchant;
}
