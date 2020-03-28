import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Product } from '../../../core/models/product.model';
import { MerchantApiService } from '../../../core/services/merchant-api.service';

@Component({
  selector: 'portal-merchant-products-detail',
  templateUrl: './merchant-products-detail.component.html',
  styleUrls: ['./merchant-products-detail.component.scss']
})
export class MerchantProductsDetailComponent {

  product: Product = null;

  constructor(private activeRoute: ActivatedRoute, private merchantApiService: MerchantApiService) {
    // Get resolved product from route
    this.activeRoute.params.subscribe(value => {
      if (value && value.id) {
        this.merchantApiService.getProduct(value.id).subscribe((product: { data:Product}) => {
          this.product = product.data;
        });
      } else {
        this.product = <Product>{
          name: '',
          description: '',
          productType: '',
          price: 0,
          tax: 19
        }
      }

    });
  }

  saveProduct() {
    if (this.product.id) {
      this.merchantApiService.updateProduct(this.product).subscribe((product: Product) => {
        this.product = product;
      });
    } else {
      this.merchantApiService.addProduct(this.product).subscribe((product: { data:Product}) => {
        this.product = product.data;
      });
    }
  }
}
