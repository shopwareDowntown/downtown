import {Component, OnDestroy, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {Product} from '../../../core/models/product.model';
import {Subscription} from 'rxjs';
import { MerchantApiService } from '../../../core/services/merchant-api.service';

@Component({
  selector: 'portal-merchant-products-detail',
  templateUrl: './merchant-products-detail.component.html',
  styleUrls: ['./merchant-products-detail.component.scss']
})
export class MerchantProductsDetailComponent implements OnInit, OnDestroy {

  product: Product = null;

  // Subscription
  private subResolver: Subscription;

  constructor(private activeRoute: ActivatedRoute, private merchantApiService: MerchantApiService) {
    // Get resolved product from route
    this.subResolver = this.activeRoute.data.subscribe(value => {
      if(value && value.product) {
        this.product = value.product;
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

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    if (this.subResolver) {
      this.subResolver.unsubscribe();
    }
  }

  saveProduct() {
    if(this.product.id) {
      this.merchantApiService.updateProduct(this.product).subscribe((product: Product) => {
        this.product = product;
      });
    } else {
      this.merchantApiService.addProduct(this.product).subscribe((product: Product) => {
        this.product = product;
      });
    }
  }
}
