export interface Product {
  id?: string;
  name: string;
  description: string;
  productType: string;
  active: boolean;
  media?: MediaData[];
  price?: number;
  tax?: number;
}

export interface ProductListData {
  data: Product[];
  total: number;
}

export interface MediaData {
  id: string,
  url: string
}
