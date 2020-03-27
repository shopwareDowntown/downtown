import {Merchant} from "./merchant.model";

export interface MerchantShippingMethod {
    id?: string;
    merchant: Merchant;
    name: string;
}
