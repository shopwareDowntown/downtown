export interface Product {
    id?: string;
    name: string;
    description: string;
    active: boolean;
    type: string;
    images: any[];
    price?: number;
    vat?: number;
}
